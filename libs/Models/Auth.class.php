<?php

declare(strict_types=1);

class Auth
{
    private const MIN_PASSWORD_LENGTH = 8;
    private const USERNAME_PATTERN = '/^[A-Za-z0-9_]{3,50}$/';
    private const MAX_FULLNAME_LENGTH = 100;
    private const MAX_EMAIL_LENGTH = 255;
    private const MAX_IP_LENGTH = 45;
    private const MAX_USER_AGENT_LENGTH = 500;
    private const MIN_FULLNAME_LENGTH = 2;

    // Registers a new user account.
    public static function signup(string $fullname, string $username, string $email, string $password, string $confirmPassword): int {
        $fullname = trim($fullname);
        $username = trim($username);
        $email    = strtolower(trim($email));
        self::validateFullname($fullname);
        self::validateUsername($username);
        self::validateEmail($email);
        self::validatePassword($password, $confirmPassword);
        self::checkDuplicateAccount($email, $username);
        $passwordHash = self::hashPassword($password);
        $ipAddress = self::getClientIp();
        $userAgent = self::getUserAgent();
        $userId = self::insertUser($fullname, $username, $email, $passwordHash, $ipAddress, $userAgent);
        return $userId;
    }

    // Authenticates a user with email/username and password.
    public static function login(string $identifier, string $password): array
    {
        $identifier = trim($identifier);
        if ($identifier === '' || $password === '') {
            throw new RuntimeException('Enter the correct email or password.');
        }
        $user = self::findByIdentifier($identifier);
        if ($user === null) {
            throw new RuntimeException('Enter the correct email or password.');
        }
        if (!self::verifyPassword($password, (string) $user['password'])) {
            throw new RuntimeException('Enter the correct email or password.');
        }
        if ((int) $user['is_verified'] !== 1) {
            Session::flash('error', 'Your account is not verified.');
            header('Location: otp-verify.php');
            exit;
        }
        if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
            self::rehashPassword((int) $user['id'], $password);
        }
        unset($user['password']);
        return $user;
    }

    // Checks if email or username already exists.
    public static function exists(string $email, string $username): bool
    {
        $email = strtolower(trim($email));
        $username = trim($username);
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT id
            FROM Auth
            WHERE email = :email OR username = :username
            LIMIT 1
        ');
        $stmt->execute([
            'email' => $email,
            'username' => $username,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    // Checks if email already exists.
    public static function emailExists(string $email): bool
    {
        $email = strtolower(trim($email));
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT id
            FROM Auth
            WHERE email = :email
            LIMIT 1
        ');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    // Checks if username already exists.
    public static function usernameExists(string $username): bool
    {
        $username = trim($username);
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT id
            FROM Auth
            WHERE username = :username
            LIMIT 1
        ');
        $stmt->execute(['username' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    // Finds user by ID.
    public static function findById(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                fullname,
                username,
                email,
                password,
                is_verified,
                otp_hash,
                otp_expiry,
                otp_attempts,
                otp_last_sent,
                register_date,
                register_ip,
                register_agent,
                updated_at
            FROM Auth
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Finds user by email.
    public static function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                fullname,
                username,
                email,
                password,
                is_verified,
                otp_hash,
                otp_expiry,
                otp_attempts,
                otp_last_sent,
                register_date,
                register_ip,
                register_agent,
                updated_at
            FROM Auth
            WHERE email = :email
            LIMIT 1
        ');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Finds user by username.
    public static function findByUsername(string $username): ?array
    {
        $username = trim($username);
        if ($username === '') {
            return null;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                fullname,
                username,
                email,
                password,
                is_verified,
                otp_hash,
                otp_expiry,
                otp_attempts,
                otp_last_sent,
                register_date,
                register_ip,
                register_agent,
                updated_at
            FROM Auth
            WHERE username = :username
            LIMIT 1
        ');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Finds user by email or username.
    public static function findByIdentifier(string $identifier): ?array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $identifier = strtolower($identifier);
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                fullname,
                username,
                email,
                password,
                is_verified,
                otp_hash,
                otp_expiry,
                otp_attempts,
                otp_last_sent,
                register_date,
                register_ip,
                register_agent,
                updated_at
            FROM Auth
            WHERE email = :email OR username = :username
            LIMIT 1
        ');
        $stmt->execute([
            'email' => $identifier,
            'username' => $identifier,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Checks if account is verified.
    public static function isVerified(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT is_verified
            FROM Auth
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
        $verified = $stmt->fetchColumn();
        return $verified !== false && (int) $verified === 1;
    }

    // Verifies a password against its hash.
    public static function verifyPassword(string $password, string $hash): bool
    {
        if ($password === '' || $hash === '') {
            return false;
        }
        return password_verify($password, $hash);
    }

    // Updates user password.
    public static function updatePassword(int $userId, string $newPassword): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        if (strlen($newPassword) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException(
                'Password must contain at least ' . self::MIN_PASSWORD_LENGTH . ' characters.'
            );
        }
        $passwordHash = self::hashPassword($newPassword);
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET password = :password
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
        if ($stmt->rowCount() === 0 && self::findById($userId) === null) {
            throw new RuntimeException('User does not exist.');
        }
    }

    // Validates fullname.
    private static function validateFullname(string $fullname): void
    {
        if ($fullname === '') {
            throw new InvalidArgumentException('Full name is required.');
        }
        if (mb_strlen($fullname) < self::MIN_FULLNAME_LENGTH) {
            throw new InvalidArgumentException('Full name is too short.');
        }
        if (mb_strlen($fullname) > self::MAX_FULLNAME_LENGTH) {
            throw new InvalidArgumentException('Full name is too long.');
        }
    }

    // Validates username.
    private static function validateUsername(string $username): void
    {
        if ($username === '') {
            throw new InvalidArgumentException('Username is required.');
        }
        if (!preg_match(self::USERNAME_PATTERN, $username)) {
            throw new InvalidArgumentException(
                'Username must be 3-50 characters and contain only letters, numbers and underscores.'
            );
        }
    }

    // Validates email.
    private static function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Enter a valid email address.');
        }
        if (mb_strlen($email) > self::MAX_EMAIL_LENGTH) {
            throw new InvalidArgumentException('Email address is too long.');
        }
    }

    // Validates password.
    private static function validatePassword(string $password, string $confirmPassword): void
    {
        if ($password === '') {
            throw new InvalidArgumentException('Password is required.');
        }
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException(
                'Password must contain at least ' . self::MIN_PASSWORD_LENGTH . ' characters.'
            );
        }
        if ($password !== $confirmPassword) {
            throw new InvalidArgumentException('Passwords do not match.');
        }
    }

    // Checks for duplicate email or username.
    private static function checkDuplicateAccount(string $email, string $username): void
    {
        if (self::emailExists($email)) {
            throw new RuntimeException('An account with this email already exists.');
        }
        if (self::usernameExists($username)) {
            throw new RuntimeException('This username is already taken.');
        }
    }

    // Hashes a password.
    private static function hashPassword(string $password): string
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Unable to secure password.');
        }
        return $passwordHash;
    }

    // Inserts new user into database.
    public static function insertUser(
        string $fullname,
        string $username,
        string $email,
        string $passwordHash,
        ?string $ipAddress,
        ?string $userAgent
    ): int {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            INSERT INTO Auth
            (
                fullname,
                username,
                email,
                password,
                is_verified,
                register_ip,
                register_agent
            )
            VALUES
            (
                :fullname,
                :username,
                :email,
                :password,
                0,
                :register_ip,
                :register_agent
            )
        ');
        try {
            $stmt->execute([
                'fullname' => $fullname,
                'username' => $username,
                'email' => $email,
                'password' => $passwordHash,
                'register_ip' => $ipAddress,
                'register_agent' => $userAgent,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Email or username already exists.');
            }
            throw $e;
        }
        return (int) $conn->lastInsertId();
    }

    // Gets client IP address.
    private static function getClientIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if ($ip !== null) {
            $ip = substr((string) $ip, 0, self::MAX_IP_LENGTH);
        }
        return $ip;
    }

    // Gets user agent.
    private static function getUserAgent(): ?string
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if ($agent !== null) {
            $agent = substr((string) $agent, 0, self::MAX_USER_AGENT_LENGTH);
        }
        return $agent;
    }

    // Rehashes password with current default algorithm.
    private static function rehashPassword(int $userId, string $plainPassword): void
    {
        $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        if ($newHash === false) {
            return;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET password = :password
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute([
            'password' => $newHash,
            'id' => $userId,
        ]);
    }
}