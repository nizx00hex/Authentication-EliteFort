<?php

declare(strict_types=1);

class RememberMe
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Cookie name for remember-me token
     */
    private const COOKIE_NAME = 'ef_remember';

    /**
     * Persistent login lifetime in days
     */
    private const LIFETIME_DAYS = 30;

    /**
     * Selector: 16 random bytes = 32 hexadecimal characters
     * Used to locate the database row (not the main secret)
     */
    private const SELECTOR_BYTES = 16;

    /**
     * Validator: 32 random bytes = 64 hexadecimal characters
     * The main secret part stored as hash in database
     */
    private const VALIDATOR_BYTES = 32;

    /**
     * SameSite cookie attribute
     */
    private const SAME_SITE = 'Lax';

    /**
     * Expected selector length in characters (32 hex chars)
     */
    private const SELECTOR_LENGTH = 32;

    /**
     * Expected validator length in characters (64 hex chars)
     */
    private const VALIDATOR_LENGTH = 64;

    /**
     * Seconds in a day
     */
    private const SECONDS_PER_DAY = 86400;

    /**
     * Cookie expiration offset for deletion
     */
    private const COOKIE_DELETE_OFFSET = 3600;


    /* =========================================================
       CREATE REMEMBER-ME TOKEN
    ========================================================= */

    /**
     * Create a new remember-me token for a user.
     */
    public static function create(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        // Make sure user exists
        $user = Auth::findById($userId);

        if ($user === null) {
            throw new RuntimeException('User does not exist.');
        }

        // Only verified users should receive persistent login credentials
        if ((int) $user['is_verified'] !== 1) {
            throw new RuntimeException('Account must be verified first.');
        }

        // Generate random selector (used to locate the database row)
        $selector = self::generateSelector();

        // Generate validator (the main secret part)
        $validator = self::generateValidator();

        // Never store raw validator in database
        $validatorHash = self::hashValidator($validator);

        // 30-day expiration
        $expiresTimestamp = time() + (self::LIFETIME_DAYS * self::SECONDS_PER_DAY);
        $expiresAt = date('Y-m-d H:i:s', $expiresTimestamp);

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            INSERT INTO remember_tokens
            (
                user_id,
                selector,
                validator_hash,
                expires_at
            )
            VALUES
            (
                :user_id,
                :selector,
                :validator_hash,
                :expires_at
            )
        ');

        $stmt->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'validator_hash' => $validatorHash,
            'expires_at' => $expiresAt,
        ]);

        // Only after DB insert succeeds do we create the browser cookie
        self::setCookie($selector, $validator, $expiresTimestamp);
    }


    /* =========================================================
       AUTHENTICATE USING REMEMBER COOKIE
    ========================================================= */

    /**
     * Authenticate a user using the remember-me cookie.
     */
    public static function authenticate(): ?array
    {
        Session::start();

        // Normal PHP session already exists - Remember Me is not needed
        if (Session::isAuthenticated()) {
            return Session::user();
        }

        // Read selector + validator from browser cookie
        $cookie = self::parseCookie();

        if ($cookie === null) {
            return null;
        }

        $selector = $cookie['selector'];
        $validator = $cookie['validator'];

        // Find matching database token
        $token = self::findToken($selector);

        // Selector does not exist
        if ($token === null) {
            self::deleteCookie();
            return null;
        }

        // Check token expiration
        $expiresAt = strtotime((string) $token['expires_at']);

        if ($expiresAt === false || time() >= $expiresAt) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }

        // Hash validator sent by browser
        $submittedHash = self::hashValidator($validator);

        // Timing-safe comparison
        if (!hash_equals((string) $token['validator_hash'], $submittedHash)) {
            // Invalid validator - revoke this persistent token
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }

        // Load user
        $user = Auth::findById((int) $token['user_id']);

        if ($user === null) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }

        // User must still be verified
        if ((int) $user['is_verified'] !== 1) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }

        // Never pass password hash into Session::login()
        unset($user['password']);

        // =====================================================
        // CREATE A NEW NORMAL LOGIN SESSION
        // =====================================================
        // Remember Me does not replace PHP sessions.
        // It authenticates the user and creates a fresh PHP + user_sessions login.
        Session::login($user);

        // =====================================================
        // ROTATE REMEMBER TOKEN
        // =====================================================
        // Old persistent token is replaced after successful use
        self::rotate((int) $token['id'], (int) $token['user_id']);

        return $user;
    }


    /* =========================================================
       COOKIE CHECK
    ========================================================= */

    /**
     * Check if the remember-me cookie exists.
     */
    public static function exists(): bool
    {
        return isset($_COOKIE[self::COOKIE_NAME])
            && is_string($_COOKIE[self::COOKIE_NAME])
            && $_COOKIE[self::COOKIE_NAME] !== '';
    }


    /* =========================================================
       TOKEN ROTATION
    ========================================================= */

    /**
     * Rotate a remember-me token (replace with new one).
     */
    public static function rotate(int $tokenId, int $userId): void
    {
        if ($tokenId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException('Invalid remember token.');
        }

        // Generate replacement token first
        $selector = self::generateSelector();
        $validator = self::generateValidator();
        $validatorHash = self::hashValidator($validator);

        $expiresTimestamp = time() + (self::LIFETIME_DAYS * self::SECONDS_PER_DAY);
        $expiresAt = date('Y-m-d H:i:s', $expiresTimestamp);

        $conn = Database::getConnection();

        try {
            $conn->beginTransaction();

            // Replace existing token instead of leaving the old one valid
            $stmt = $conn->prepare('
                UPDATE remember_tokens
                SET
                    selector = :selector,
                    validator_hash = :validator_hash,
                    expires_at = :expires_at,
                    created_at = NOW()
                WHERE id = :id
                AND user_id = :user_id
                LIMIT 1
            ');

            $stmt->execute([
                'selector' => $selector,
                'validator_hash' => $validatorHash,
                'expires_at' => $expiresAt,
                'id' => $tokenId,
                'user_id' => $userId,
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Unable to rotate remember token.');
            }

            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            // Do not keep old browser credential if rotation failed
            self::deleteCookie();
            throw $e;
        }

        // Browser receives matching replacement
        self::setCookie($selector, $validator, $expiresTimestamp);
    }


    /* =========================================================
       FORGET / REMOVE TOKEN
    ========================================================= */

    /**
     * Forget the current remember-me token.
     */
    public static function forget(): void
    {
        // Read current cookie
        $cookie = self::parseCookie();

        if ($cookie !== null) {
            $token = self::findToken($cookie['selector']);

            if ($token !== null) {
                self::deleteToken((int) $token['id']);
            }
        }

        // Always remove browser cookie
        self::deleteCookie();
    }

    /**
     * Revoke all remember-me tokens for a user.
     */
    public static function revokeAll(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            DELETE FROM remember_tokens
            WHERE user_id = :user_id
        ');

        $stmt->execute(['user_id' => $userId]);

        // If current browser cookie belongs to this user, remove it too
        $cookie = self::parseCookie();

        if ($cookie === null) {
            return;
        }

        $token = self::findToken($cookie['selector']);

        // Token may already be deleted above.
        // In either case removing local cookie is safe.
        self::deleteCookie();
    }


    /* =========================================================
       TOKEN GENERATION
    ========================================================= */

    /**
     * Generate a random selector.
     */
    private static function generateSelector(): string
    {
        return bin2hex(random_bytes(self::SELECTOR_BYTES));
    }

    /**
     * Generate a random validator.
     */
    private static function generateValidator(): string
    {
        return bin2hex(random_bytes(self::VALIDATOR_BYTES));
    }


    /* =========================================================
       VALIDATOR HASHING
    ========================================================= */

    /**
     * Hash the validator for database storage.
     */
    private static function hashValidator(string $validator): string
    {
        // Validator is already a high-entropy random secret.
        // SHA-256 provides deterministic lookup/comparison without storing raw validator.
        return hash('sha256', $validator);
    }


    /* =========================================================
       COOKIE PARSING
    ========================================================= */

    /**
     * Parse the remember-me cookie.
     */
    private static function parseCookie(): ?array
    {
        if (!self::exists()) {
            return null;
        }

        $cookie = (string) $_COOKIE[self::COOKIE_NAME];

        // Cookie format: selector:validator
        $parts = explode(':', $cookie, 2);

        if (count($parts) !== 2) {
            self::deleteCookie();
            return null;
        }

        [$selector, $validator] = $parts;

        // Expected: selector = 32 hex chars, validator = 64 hex chars
        if (!preg_match('/^[a-f0-9]{' . self::SELECTOR_LENGTH . '}$/i', $selector)
            || !preg_match('/^[a-f0-9]{' . self::VALIDATOR_LENGTH . '}$/i', $validator)) {
            self::deleteCookie();
            return null;
        }

        return [
            'selector' => strtolower($selector),
            'validator' => strtolower($validator),
        ];
    }


    /* =========================================================
       DATABASE OPERATIONS
    ========================================================= */

    /**
     * Find a token by selector.
     */
    private static function findToken(string $selector): ?array
    {
        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            SELECT
                id,
                user_id,
                selector,
                validator_hash,
                expires_at,
                created_at
            FROM remember_tokens
            WHERE selector = :selector
            LIMIT 1
        ');

        $stmt->execute(['selector' => $selector]);

        $token = $stmt->fetch(PDO::FETCH_ASSOC);

        return $token ?: null;
    }

    /**
     * Delete a token by ID.
     */
    private static function deleteToken(int $tokenId): void
    {
        if ($tokenId <= 0) {
            return;
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            DELETE FROM remember_tokens
            WHERE id = :id
            LIMIT 1
        ');

        $stmt->execute(['id' => $tokenId]);
    }


    /* =========================================================
       COOKIE MANAGEMENT
    ========================================================= */

    /**
     * Set the remember-me cookie.
     */
    private static function setCookie(string $selector, string $validator, int $expires): void
    {
        if (headers_sent()) {
            throw new RuntimeException('Cannot create remember cookie after headers have been sent.');
        }

        // Browser stores: selector:validator
        // NOT password, NOT password hash, NOT PHP session ID
        $value = $selector . ':' . $validator;

        setcookie(
            self::COOKIE_NAME,
            $value,
            [
                'expires' => $expires,
                'path' => '/',
                'secure' => self::isHttps(),
                'httponly' => true,
                'samesite' => self::SAME_SITE,
            ]
        );

        // Make cookie immediately available during current PHP request
        $_COOKIE[self::COOKIE_NAME] = $value;
    }

    /**
     * Delete the remember-me cookie.
     */
    private static function deleteCookie(): void
    {
        if (!headers_sent()) {
            setcookie(
                self::COOKIE_NAME,
                '',
                [
                    'expires' => time() - self::COOKIE_DELETE_OFFSET,
                    'path' => '/',
                    'secure' => self::isHttps(),
                    'httponly' => true,
                    'samesite' => self::SAME_SITE,
                ]
            );
        }

        unset($_COOKIE[self::COOKIE_NAME]);
    }


    /* =========================================================
       HELPER METHODS
    ========================================================= */

    /**
     * Check if the connection is using HTTPS.
     */
    public static function isHttps(): bool
    {
        return isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== ''
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    }
}