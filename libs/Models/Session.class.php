<?php

declare(strict_types=1);

class Session
{
    private const SESSION_NAME = 'EF_SESSION';
    private const IDLE_TIMEOUT = 1800;
    private const ABSOLUTE_TIMEOUT = 28800;
    private const SAME_SITE = 'Lax';
    private const REQUIRED_USER_FIELDS = ['id', 'fullname', 'username', 'email'];

    // Starts the PHP session with security configuration.
    public static function start(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        session_name(self::SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => RememberMe::isHttps(),
            'httponly' => true,
            'samesite' => self::SAME_SITE,
        ]);
        if (headers_sent()) {
            throw new RuntimeException('Cannot start session after headers have been sent.');
        }
        session_start();
    }

    // Authenticates a user and creates a session.
    public static function login(array $user): void {
        self::start();
        foreach (self::REQUIRED_USER_FIELDS as $field) {
            if (!array_key_exists($field, $user)) {
                throw new InvalidArgumentException("Missing user field: {$field}");
            }
        }
        if (self::isAuthenticated()) {
            self::revokeCurrent();
        }
        self::regenerate();
        $now = time();
        $_SESSION['auth'] = [
            'user_id' => (int) $user['id'],
            'fullname' => (string) $user['fullname'],
            'username' => (string) $user['username'],
            'email' => (string) $user['email'],
            'login_time' => $now,
            'last_activity' => $now,
        ];
        try {
            self::createSessionRecord((int) $user['id']);
        } catch (Throwable $e) {
            self::destroy();
            throw $e;
        }
    }

    // Logs out the current user and destroys the session.
    public static function logout(): void {
        self::start();
        if (self::isAuthenticated()) {
            try {
                self::revokeCurrent();
            } catch (Throwable $e) {
                error_log('Session revoke error: ' . $e->getMessage());
            }
        }
        self::destroy();
    }

    // Checks if the current user is authenticated.
    public static function isAuthenticated(): bool {
        self::start();
        return isset($_SESSION['auth']['user_id']) && is_numeric($_SESSION['auth']['user_id']);
    }

    // Validates the current session against database record.
    public static function validate(): bool {
        self::start();
        if (!self::isAuthenticated()) {
            return false;
        }
        if(!self::validateSessionUser()) {
            self::destroy();
            return false;
        }
        $userId = self::userId();
        $sessionHash = self::sessionHash();
        if (!self::validateSessionIdentity($userId, $sessionHash)) {
            self::destroy();
            return false;
        }
        $record = self::getSessionRecord();
        if ($record === null) {
            self::destroy();
            return false;
        }
        if ($record['revoked_at'] !== null) {
            self::destroy();
            return false;
        }
        $now = time();
        $expiresAt = strtotime((string) $record['expires_at']);
        if ($expiresAt === false || $now >= $expiresAt) {
            self::revokeCurrent();
            self::destroy();
            return false;
        }
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
        self::updateActivity();
        return true;
    }

    // Gets the current authenticated user data.
    public static function user(): ?array {
        self::start();
        if (!self::isAuthenticated()) {
            return null;
        }
        return $_SESSION['auth'];
    }

    // Gets the current authenticated user ID.
    public static function userId(): ?int {
        self::start();
        if (!self::isAuthenticated()) {
            return null;
        }
        return (int) $_SESSION['auth']['user_id'];
    }

    // Gets the current authenticated username.
    public static function username(): ?string {
        self::start();
        return isset($_SESSION['auth']['username'])
            ? (string) $_SESSION['auth']['username']
            : null;
    }

    // Gets the current authenticated user email.
    public static function email(): ?string {
        self::start();
        return isset($_SESSION['auth']['email'])
            ? (string) $_SESSION['auth']['email']
            : null;
    }

    // Regenerates session ID to prevent fixation.
    public static function regenerate(): void {
        self::start();
        $authenticated = self::isAuthenticated();
        $oldHash = null;
        $userId = null;
        if ($authenticated) {
            $oldHash = self::sessionHash();
            $userId = self::userId();
        }
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Unable to regenerate session ID.');
        }
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

    // Destroys the entire session.
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        self::deleteSessionCookie();
        session_destroy();
    }

    // Revokes the current database session.
    public static function revokeCurrent(): void {
        self::start();
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

    // Revokes all sessions for a specific user.
    public static function revokeAll(int $userId): void {
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

    // Updates the session activity timestamp.
    public static function updateActivity(): void {
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
        $_SESSION['auth']['last_activity'] = time();
    }

    // Sets a session value.
    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    // Gets a session value.
    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    // Checks if a session key exists.
    public static function has(string $key): bool {
        self::start();
        return array_key_exists($key, $_SESSION);
    }

    // Removes a session key.
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    // Stores a flash message for one request.
    public static function flash(string $type, string $message): void {
        self::start();
        $_SESSION['_flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    // Gets and clears the flash message.
    public static function getFlash(): ?array {
        self::start();
        if (!isset($_SESSION['_flash'])) {
            return null;
        }
        $flash = $_SESSION['_flash'];
        unset($_SESSION['_flash']);
        return $flash;
    }

    // Validates that the session user exists in the database.
    private static function validateSessionUser() {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT id
            FROM Auth
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $_SESSION['auth']['user_id']]);
        return $stmt->fetchColumn();
    }

    // Validates the session identity against database record.
    private static function validateSessionIdentity(int $userId, string $sessionHash): bool {
        $session = self::sessionExists($userId, $sessionHash);
        if ($session === null) {
            return false;
        }
        if (!hash_equals($session['session_id_hash'], $sessionHash)) {
            return false;
        }
        $currentAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if ($session['user_agent'] !== $currentAgent) {
            return false;
        }
        return true;
    }

    // Checks if a session exists in the database.
    private static function sessionExists(int $userId, string $sessionHash): ?array {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                session_id_hash,
                ip_address,
                user_agent
            FROM user_sessions
            WHERE user_id = :user_id
            AND session_id_hash = :session_hash
            AND revoked_at IS NULL
            AND expires_at > NOW()
            LIMIT 1
        ');
        $stmt->execute([
            'user_id'      => $userId,
            'session_hash' => $sessionHash
        ]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        return $session ?: null;
    }

    // Creates a database session record.
    private static function createSessionRecord(int $userId): void {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        self::start();
        $sessionHash = self::sessionHash();
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
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

    // Gets the current database session record.
    private static function getSessionRecord(): ?array {
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

    // Hashes the current session ID using SHA-256.
    private static function sessionHash(): string {
        self::start();
        $id = session_id();
        if ($id === '') {
            throw new RuntimeException('No active session ID.');
        }
        return hash('sha256', $id);
    }

    // Deletes the session cookie.
    private static function deleteSessionCookie(): void {
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
}