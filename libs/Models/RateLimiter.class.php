<?php

declare(strict_types=1);

class RateLimiter
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Maximum length for identifier field in database
     */
    private const MAX_IDENTIFIER_LENGTH = 255;

    /**
     * Action pattern for validation
     */
    private const ACTION_PATTERN = '/^[a-z0-9._-]{1,50}$/';

    /**
     * Default IP when not available
     */
    private const DEFAULT_IP = 'unknown';


    /* =========================================================
       CHECK TOO MANY ATTEMPTS
    ========================================================= */

    /**
     * Check if the rate limit has been exceeded.
     */
    public static function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        if ($maxAttempts <= 0) {
            throw new InvalidArgumentException('Maximum attempts must be greater than zero.');
        }

        if ($windowSeconds <= 0) {
            throw new InvalidArgumentException('Window duration must be greater than zero.');
        }

        $record = self::findRecord($key);

        // No rate-limit record yet
        if ($record === null) {
            return false;
        }

        // Explicit temporary block still active
        if ($record['blocked_until'] !== null) {
            $blockedUntil = strtotime((string) $record['blocked_until']);
            if ($blockedUntil !== false && time() < $blockedUntil) {
                return true;
            }
        }

        // Check rate-limit window
        $windowStart = strtotime((string) $record['window_start']);
        if ($windowStart === false) {
            return false;
        }

        // Window already expired
        if (time() >= ($windowStart + $windowSeconds)) {
            return false;
        }

        // Still inside current window
        return (int) $record['attempts'] >= $maxAttempts;
    }


    /* =========================================================
       RECORD AN ATTEMPT
    ========================================================= */

    /**
     * Record an attempt for the given key.
     */
    public static function hit(string $key, int $windowSeconds): void
    {
        if ($windowSeconds <= 0) {
            throw new InvalidArgumentException('Window duration must be greater than zero.');
        }

        [$identifier, $action] = self::parseKey($key);

        $conn = Database::getConnection();
        $record = self::findRecord($key);

        // No record exists - create first attempt
        if ($record === null) {
            $stmt = $conn->prepare('
                INSERT INTO rate_limits
                (
                    identifier,
                    action,
                    attempts,
                    window_start,
                    blocked_until
                )
                VALUES
                (
                    :identifier,
                    :action,
                    1,
                    NOW(),
                    NULL
                )
            ');

            $stmt->execute([
                'identifier' => $identifier,
                'action' => $action,
            ]);

            return;
        }

        // Check current window
        $windowStart = strtotime((string) $record['window_start']);

        // Invalid or expired window - start a new window with attempt #1
        if ($windowStart === false || time() >= ($windowStart + $windowSeconds)) {
            $stmt = $conn->prepare('
                UPDATE rate_limits
                SET
                    attempts = 1,
                    window_start = NOW(),
                    blocked_until = NULL
                WHERE identifier = :identifier
                AND action = :action
                LIMIT 1
            ');

            $stmt->execute([
                'identifier' => $identifier,
                'action' => $action,
            ]);

            return;
        }

        // Still inside window - increment attempts
        $stmt = $conn->prepare('
            UPDATE rate_limits
            SET attempts = attempts + 1
            WHERE identifier = :identifier
            AND action = :action
            LIMIT 1
        ');

        $stmt->execute([
            'identifier' => $identifier,
            'action' => $action,
        ]);
    }


    /* =========================================================
       CLEAR RATE LIMIT
    ========================================================= */

    /**
     * Clear the rate limit record for a key.
     */
    public static function clear(string $key): void
    {
        [$identifier, $action] = self::parseKey($key);

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            DELETE FROM rate_limits
            WHERE identifier = :identifier
            AND action = :action
            LIMIT 1
        ');

        $stmt->execute([
            'identifier' => $identifier,
            'action' => $action,
        ]);
    }


    /* =========================================================
       ATTEMPT COUNTERS
    ========================================================= */

    /**
     * Get the current number of attempts for a key.
     */
    public static function attempts(string $key): int
    {
        $record = self::findRecord($key);

        if ($record === null) {
            return 0;
        }

        return (int) $record['attempts'];
    }

    /**
     * Get the remaining attempts before rate limit is hit.
     */
    public static function remaining(string $key, int $maxAttempts): int
    {
        if ($maxAttempts <= 0) {
            return 0;
        }

        $attempts = self::attempts($key);

        return max(0, $maxAttempts - $attempts);
    }


    /* =========================================================
       RETRY INFORMATION
    ========================================================= */

    /**
     * Get the number of seconds until the next attempt is allowed.
     */
    public static function retryAfter(string $key): int
    {
        $record = self::findRecord($key);

        if ($record === null) {
            return 0;
        }

        // If manually blocked, calculate blocked time
        if ($record['blocked_until'] !== null) {
            $blockedUntil = strtotime((string) $record['blocked_until']);

            if ($blockedUntil !== false) {
                return max(0, $blockedUntil - time());
            }
        }

        return 0;
    }


    /* =========================================================
       TEMPORARY BLOCK MANAGEMENT
    ========================================================= */

    /**
     * Temporarily block a key for a specified duration.
     */
    public static function block(string $key, int $seconds): void
    {
        if ($seconds <= 0) {
            throw new InvalidArgumentException('Block duration must be greater than zero.');
        }

        [$identifier, $action] = self::parseKey($key);

        $blockedUntil = date('Y-m-d H:i:s', time() + $seconds);

        $conn = Database::getConnection();

        // Make sure record exists
        $record = self::findRecord($key);

        if ($record === null) {
            $stmt = $conn->prepare('
                INSERT INTO rate_limits
                (
                    identifier,
                    action,
                    attempts,
                    window_start,
                    blocked_until
                )
                VALUES
                (
                    :identifier,
                    :action,
                    0,
                    NOW(),
                    :blocked_until
                )
            ');

            $stmt->execute([
                'identifier' => $identifier,
                'action' => $action,
                'blocked_until' => $blockedUntil,
            ]);

            return;
        }

        // Update existing record
        $stmt = $conn->prepare('
            UPDATE rate_limits
            SET blocked_until = :blocked_until
            WHERE identifier = :identifier
            AND action = :action
            LIMIT 1
        ');

        $stmt->execute([
            'blocked_until' => $blockedUntil,
            'identifier' => $identifier,
            'action' => $action,
        ]);
    }

    /**
     * Check if a key is currently blocked.
     */
    public static function isBlocked(string $key): bool
    {
        $record = self::findRecord($key);

        if ($record === null || $record['blocked_until'] === null) {
            return false;
        }

        $blockedUntil = strtotime((string) $record['blocked_until']);

        if ($blockedUntil === false) {
            return false;
        }

        // Block still active
        if (time() < $blockedUntil) {
            return true;
        }

        // Block expired - clear blocked_until
        self::clearBlock($key);

        return false;
    }


    /* =========================================================
       KEY MANAGEMENT
    ========================================================= */

    /**
     * Create a rate limit key.
     */
    public static function makeKey(string $action, ?string $identifier = null): string
    {
        $action = strtolower(trim($action));

        if ($action === '') {
            throw new InvalidArgumentException('Rate-limit action is required.');
        }

        // Validate action format
        if (!preg_match(self::ACTION_PATTERN, $action)) {
            throw new InvalidArgumentException('Invalid rate-limit action.');
        }

        // Normalize identifier
        $identifier = strtolower(trim((string) $identifier));

        // Always include client IP
        $ip = self::clientIp();

        if ($identifier === '') {
            $identifier = $ip;
        } else {
            $identifier = $identifier . '|' . $ip;
        }

        // Limit database field length
        $identifier = substr($identifier, 0, self::MAX_IDENTIFIER_LENGTH);

        // Internal key format: action::identifier
        return $action . '::' . $identifier;
    }


    /* =========================================================
       PRIVATE HELPER METHODS
    ========================================================= */

    /**
     * Get client IP address (validated).
     */
    private static function clientIp(): string
    {
        // Do NOT blindly trust HTTP_X_FORWARDED_FOR
        // because clients can spoof it unless your trusted proxy is configured

        $ip = $_SERVER['REMOTE_ADDR'] ?? self::DEFAULT_IP;

        $ip = trim((string) $ip);

        if ($ip === '') {
            return self::DEFAULT_IP;
        }

        // Validate IPv4 / IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return self::DEFAULT_IP;
        }

        return $ip;
    }

    /**
     * Find a rate limit record by key.
     */
    private static function findRecord(string $key): ?array
    {
        [$identifier, $action] = self::parseKey($key);

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            SELECT
                id,
                identifier,
                action,
                attempts,
                window_start,
                blocked_until,
                updated_at
            FROM rate_limits
            WHERE identifier = :identifier
            AND action = :action
            LIMIT 1
        ');

        $stmt->execute([
            'identifier' => $identifier,
            'action' => $action,
        ]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    /**
     * Parse an internal rate limit key.
     */
    private static function parseKey(string $key): array
    {
        $key = trim($key);

        if ($key === '') {
            throw new InvalidArgumentException('Rate-limit key is required.');
        }

        $parts = explode('::', $key, 2);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid rate-limit key.');
        }

        $action = trim($parts[0]);
        $identifier = trim($parts[1]);

        if ($action === '' || $identifier === '') {
            throw new InvalidArgumentException('Invalid rate-limit key.');
        }

        return [$identifier, $action];
    }

    /**
     * Clear only the temporary block (not the attempt count).
     */
    private static function clearBlock(string $key): void
    {
        [$identifier, $action] = self::parseKey($key);

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            UPDATE rate_limits
            SET blocked_until = NULL
            WHERE identifier = :identifier
            AND action = :action
            LIMIT 1
        ');

        $stmt->execute([
            'identifier' => $identifier,
            'action' => $action,
        ]);
    }
}