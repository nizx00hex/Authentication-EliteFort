<?php

declare(strict_types=1);

class RateLimiter
{
    private const MAX_IDENTIFIER_LENGTH = 255;
    private const ACTION_PATTERN = '/^[a-z0-9._-]{1,50}$/';
    private const DEFAULT_IP = 'unknown';

    // Checks if the rate limit has been exceeded.
    public static function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        if ($maxAttempts <= 0) {
            throw new InvalidArgumentException('Maximum attempts must be greater than zero.');
        }
        if ($windowSeconds <= 0) {
            throw new InvalidArgumentException('Window duration must be greater than zero.');
        }
        $record = self::findRecord($key);
        if ($record === null) {
            return false;
        }
        if ($record['blocked_until'] !== null) {
            $blockedUntil = strtotime((string) $record['blocked_until']);
            if ($blockedUntil !== false && time() < $blockedUntil) {
                return true;
            }
        }
        $windowStart = strtotime((string) $record['window_start']);
        if ($windowStart === false) {
            return false;
        }
        if (time() >= ($windowStart + $windowSeconds)) {
            return false;
        }
        return (int) $record['attempts'] >= $maxAttempts;
    }

    // Records an attempt for the given key.
    public static function hit(string $key, int $windowSeconds): void
    {
        if ($windowSeconds <= 0) {
            throw new InvalidArgumentException('Window duration must be greater than zero.');
        }
        [$identifier, $action] = self::parseKey($key);
        $conn = Database::getConnection();
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
        $windowStart = strtotime((string) $record['window_start']);
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

    // Clears the rate limit record for a key.
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

    // Gets the current number of attempts for a key.
    public static function attempts(string $key): int
    {
        $record = self::findRecord($key);
        if ($record === null) {
            return 0;
        }
        return (int) $record['attempts'];
    }

    // Gets the remaining attempts before rate limit is hit.
    public static function remaining(string $key, int $maxAttempts): int
    {
        if ($maxAttempts <= 0) {
            return 0;
        }
        $attempts = self::attempts($key);
        return max(0, $maxAttempts - $attempts);
    }

    // Gets the number of seconds until the next attempt is allowed.
    public static function retryAfter(string $key): int
    {
        $record = self::findRecord($key);
        if ($record === null) {
            return 0;
        }
        if ($record['blocked_until'] !== null) {
            $blockedUntil = strtotime((string) $record['blocked_until']);
            if ($blockedUntil !== false) {
                return max(0, $blockedUntil - time());
            }
        }
        return 0;
    }

    // Temporarily blocks a key for a specified duration.
    public static function block(string $key, int $seconds): void
    {
        if ($seconds <= 0) {
            throw new InvalidArgumentException('Block duration must be greater than zero.');
        }
        [$identifier, $action] = self::parseKey($key);
        $blockedUntil = date('Y-m-d H:i:s', time() + $seconds);
        $conn = Database::getConnection();
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

    // Checks if a key is currently blocked.
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
        if (time() < $blockedUntil) {
            return true;
        }
        self::clearBlock($key);
        return false;
    }

    // Creates a rate limit key.
    public static function makeKey(string $action, ?string $identifier = null): string
    {
        $action = strtolower(trim($action));
        if ($action === '') {
            throw new InvalidArgumentException('Rate-limit action is required.');
        }
        if (!preg_match(self::ACTION_PATTERN, $action)) {
            throw new InvalidArgumentException('Invalid rate-limit action.');
        }
        $identifier = strtolower(trim((string) $identifier));
        $ip = self::clientIp();
        if ($identifier === '') {
            $identifier = $ip;
        } else {
            $identifier = $identifier . '|' . $ip;
        }
        $identifier = substr($identifier, 0, self::MAX_IDENTIFIER_LENGTH);
        return $action . '::' . $identifier;
    }

    // Gets client IP address (validated).
    private static function clientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? self::DEFAULT_IP;
        $ip = trim((string) $ip);
        if ($ip === '') {
            return self::DEFAULT_IP;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return self::DEFAULT_IP;
        }
        return $ip;
    }

    // Finds a rate limit record by key.
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

    // Parses an internal rate limit key.
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

    // Clears only the temporary block (not the attempt count).
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