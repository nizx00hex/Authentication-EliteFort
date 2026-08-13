<?php

declare(strict_types=1);

class Session
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Custom session cookie name
     */
    private const SESSION_NAME = 'EF_SESSION';

    /**
     * User can stay inactive for 30 minutes (1800 seconds)
     */
    private const IDLE_TIMEOUT = 1800;

    /**
     * Maximum session lifetime = 8 hours (28800 seconds)
     */
    private const ABSOLUTE_TIMEOUT = 28800;

    /**
     * SameSite cookie attribute
     */
    private const SAME_SITE = 'Lax';

    /**
     * Required user fields for authentication
     */
    private const REQUIRED_USER_FIELDS = ['id', 'fullname', 'username', 'email'];


    /* =========================================================
       SESSION START
    ========================================================= */

    /**
     * Start the PHP session with security configuration.
     */
    public static function start(): void
    {
        // Don't start twice
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Session configuration must happen before session_start()
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        // Give our PHP session cookie a custom name (EF_SESSION instead of PHPSESSID)
        session_name(self::SESSION_NAME);

        // Session cookie configuration
        // lifetime = 0 means this is a normal browser-session cookie
        // Persistent login is handled separately by RememberMe.class.php
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => RememberMe::isHttps(),
            'httponly' => true,
            'samesite' => self::SAME_SITE,
        ]);

        // Session must start before output
        if (headers_sent()) {
            throw new RuntimeException('Cannot start session after headers have been sent.');
        }

        session_start();
    }


    /* =========================================================
       LOGIN / LOGOUT
    ========================================================= */

    /**
     * Authenticate a user and create a session.
     */
    public static function login(array $user): void
    {
        self::start();

        // Make sure required user fields exist
        foreach (self::REQUIRED_USER_FIELDS as $field) {
            if (!array_key_exists($field, $user)) {
                throw new InvalidArgumentException("Missing user field: {$field}");
            }
        }

        // If somehow another authenticated session already exists, revoke it
        if (self::isAuthenticated()) {
            self::revokeCurrent();
        }

        // Prevent session fixation - login gets a NEW session ID
        self::regenerate();

        $now = time();

        // Create PHP authenticated session
        $_SESSION['auth'] = [
            'user_id' => (int) $user['id'],
            'fullname' => (string) $user['fullname'],
            'username' => (string) $user['username'],
            'email' => (string) $user['email'],
            'login_time' => $now,
            'last_activity' => $now,
        ];

        // Also register this login inside user_sessions table
        try {
            self::createSessionRecord((int) $user['id']);
        } catch (Throwable $e) {
            // If DB session record cannot be created, do not leave user authenticated
            self::destroy();
            throw $e;
        }
    }

    /**
     * Logout the current user and destroy the session.
     */
    public static function logout(): void
    {
        self::start();

        // Revoke current DB session first
        if (self::isAuthenticated()) {
            try {
                self::revokeCurrent();
            } catch (Throwable $e) {
                // Logout should still destroy the browser/PHP session even if DB operation fails
                error_log('Session revoke error: ' . $e->getMessage());
            }
        }

        // Destroy PHP session
        self::destroy();
    }


    /* =========================================================
       AUTHENTICATION CHECKS
    ========================================================= */

    /**
     * Check if the current user is authenticated.
     */
    public static function isAuthenticated(): bool
    {
        self::start();

        return isset($_SESSION['auth']['user_id']) && is_numeric($_SESSION['auth']['user_id']);
    }

    /**
     * Validate the current session against database record.
     */
    public static function validate(): bool
    {
        self::start();

        // No PHP login session
        if (!self::isAuthenticated()) {
            return false;
        }

        // Find current session in database
        $record = self::getSessionRecord();

        // PHP session exists but database session does not - fail closed
        if ($record === null) {
            self::destroy();
            return false;
        }

        // Session manually revoked?
        if ($record['revoked_at'] !== null) {
            self::destroy();
            return false;
        }

        $now = time();

        // Check absolute session expiration
        $expiresAt = strtotime((string) $record['expires_at']);
        if ($expiresAt === false || $now >= $expiresAt) {
            self::revokeCurrent();
            self::destroy();
            return false;
        }

        // Check idle timeout
        $lastActivity = strtotime((string) $record['last_activity']);
        if ($lastActivity === false) {
            self::revokeCurrent();
            self::destroy();
            return false;
        }

        if (($now - $lastActivity) > self::IDLE_TIMEOUT) {
            self::revokeCurrent();
            self::destroy();
            return false;
        }

        // Session is valid - update activity timestamp
        self::updateActivity();
        return true;
    }


    /* =========================================================
       USER DATA GETTERS
    ========================================================= */

    /**
     * Get the current authenticated user data.
     */
    public static function user(): ?array
    {
        self::start();

        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION['auth'];
    }

    /**
     * Get the current authenticated user ID.
     */
    public static function userId(): ?int
    {
        self::start();

        if (!self::isAuthenticated()) {
            return null;
        }

        return (int) $_SESSION['auth']['user_id'];
    }

    /**
     * Get the current authenticated username.
     */
    public static function username(): ?string
    {
        self::start();

        return isset($_SESSION['auth']['username'])
            ? (string) $_SESSION['auth']['username']
            : null;
    }

    /**
     * Get the current authenticated user email.
     */
    public static function email(): ?string
    {
        self::start();

        return isset($_SESSION['auth']['email'])
            ? (string) $_SESSION['auth']['email']
            : null;
    }


    /* =========================================================
       SESSION MANAGEMENT
    ========================================================= */

    /**
     * Regenerate session ID to prevent fixation.
     */
    public static function regenerate(): void
    {
        self::start();

        // If this is already an authenticated database-backed session, remember its current hash
        $authenticated = self::isAuthenticated();
        $oldHash = null;
        $userId = null;

        if ($authenticated) {
            $oldHash = self::sessionHash();
            $userId = self::userId();
        }

        // Generate new PHP session ID and delete old server-side PHP session
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Unable to regenerate session ID.');
        }

        // If an authenticated DB session existed, update its hash to match the new PHP session ID
        if ($authenticated && $oldHash !== null && $userId !== null) {
            $conn = Database::getConnection();

            $stmt = $conn->prepare('
                UPDATE user_sessions
                SET session_id_hash = :new_hash
                WHERE user_id = :user_id
                AND session_id_hash = :old_hash
                AND revoked_at IS NULL
                LIMIT 1
            ');

            $stmt->execute([
                'new_hash' => self::sessionHash(),
                'user_id' => $userId,
                'old_hash' => $oldHash,
            ]);
        }
    }

    /**
     * Destroy the entire session.
     */
    public static function destroy(): void
    {
        self::start();

        // Remove all PHP session data
        $_SESSION = [];

        // Delete browser EF_SESSION cookie
        self::deleteSessionCookie();

        // Destroy PHP server-side session
        session_destroy();
    }

    /**
     * Revoke the current database session.
     */
    public static function revokeCurrent(): void
    {
        self::start();

        // No session ID available
        if (session_id() === '') {
            return;
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            UPDATE user_sessions
            SET revoked_at = NOW()
            WHERE session_id_hash = :session_hash
            AND revoked_at IS NULL
            LIMIT 1
        ');

        $stmt->execute([
            'session_hash' => self::sessionHash(),
        ]);
    }

    /**
     * Revoke all sessions for a specific user.
     */
    public static function revokeAll(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            UPDATE user_sessions
            SET revoked_at = NOW()
            WHERE user_id = :user_id
            AND revoked_at IS NULL
        ');

        $stmt->execute([
            'user_id' => $userId,
        ]);
    }

    /**
     * Update the session activity timestamp.
     */
    public static function updateActivity(): void
    {
        self::start();

        if (!self::isAuthenticated()) {
            return;
        }

        $userId = self::userId();
        if ($userId === null) {
            return;
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            UPDATE user_sessions
            SET last_activity = NOW()
            WHERE user_id = :user_id
            AND session_id_hash = :session_hash
            AND revoked_at IS NULL
            AND expires_at > NOW()
            LIMIT 1
        ');

        $stmt->execute([
            'user_id' => $userId,
            'session_hash' => self::sessionHash(),
        ]);

        // Keep PHP session timestamp updated too
        $_SESSION['auth']['last_activity'] = time();
    }


    /* =========================================================
       GENERIC SESSION METHODS
    ========================================================= */

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     */
    public static function has(string $key): bool
    {
        self::start();
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Remove a session key.
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }


    /* =========================================================
       FLASH MESSAGES
    ========================================================= */

    /**
     * Store a flash message for one request.
     */
    public static function flash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Get and clear the flash message.
     */
    public static function getFlash(): ?array
    {
        self::start();

        if (!isset($_SESSION['_flash'])) {
            return null;
        }

        $flash = $_SESSION['_flash'];
        unset($_SESSION['_flash']);
        return $flash;
    }


    /* =========================================================
       DATABASE SESSION RECORDS
    ========================================================= */

    /**
     * Create a database session record.
     */
    private static function createSessionRecord(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        self::start();

        // Never store raw session_id() in database - store SHA-256 hash
        $sessionHash = self::sessionHash();

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Database columns have length limits
        if ($ip !== null) {
            $ip = substr((string) $ip, 0, 45);
        }

        if ($agent !== null) {
            $agent = substr((string) $agent, 0, 500);
        }

        $expiresAt = date('Y-m-d H:i:s', time() + self::ABSOLUTE_TIMEOUT);

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            INSERT INTO user_sessions
            (
                user_id,
                session_id_hash,
                ip_address,
                user_agent,
                last_activity,
                expires_at
            )
            VALUES
            (
                :user_id,
                :session_hash,
                :ip_address,
                :user_agent,
                NOW(),
                :expires_at
            )
        ');

        $stmt->execute([
            'user_id' => $userId,
            'session_hash' => $sessionHash,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Get the current database session record.
     */
    private static function getSessionRecord(): ?array
    {
        self::start();

        $userId = self::userId();
        if ($userId === null) {
            return null;
        }

        $conn = Database::getConnection();

        $stmt = $conn->prepare('
            SELECT
                id,
                user_id,
                session_id_hash,
                ip_address,
                user_agent,
                created_at,
                last_activity,
                expires_at,
                revoked_at
            FROM user_sessions
            WHERE user_id = :user_id
            AND session_id_hash = :session_hash
            LIMIT 1
        ');

        $stmt->execute([
            'user_id' => $userId,
            'session_hash' => self::sessionHash(),
        ]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        return $record ?: null;
    }


    /* =========================================================
       HELPER METHODS
    ========================================================= */

    /**
     * Hash the current session ID using SHA-256.
     */
    private static function sessionHash(): string
    {
        self::start();

        $id = session_id();
        if ($id === '') {
            throw new RuntimeException('No active session ID.');
        }

        return hash('sha256', $id);
    }

    /**
     * Delete the session cookie.
     */
    private static function deleteSessionCookie(): void
    {
        // PHP session is not using cookies
        if (!ini_get('session.use_cookies')) {
            return;
        }

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) ($params['secure'] ?? false),
                'httponly' => (bool) ($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? self::SAME_SITE,
            ]
        );
    }

    /**
     * Check if the connection is using HTTPS.
     */
    // private static function isHttps(): bool
    // {
    //     return isset($_SERVER['HTTPS'])
    //         && $_SERVER['HTTPS'] !== ''
    //         && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    // }
}