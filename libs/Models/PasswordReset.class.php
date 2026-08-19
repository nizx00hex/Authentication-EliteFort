<?php

declare(strict_types=1);

class PasswordReset
{
    private const TOKEN_LIFETIME_MINUTES = 30;
    private const AUTHORIZATION_LIFETIME_MINUTES = 10;
    private const SESSION_KEY = 'password_reset';
    private const TOKEN_BYTES = 32;
    private const MIN_PASSWORD_LENGTH = 8;
    private const MAX_PASSWORD_LENGTH = 4096;

    // Requests a password reset email for a user.
    public static function request(string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $user = Auth::findByEmail($email);
        if ($user === null) {
            return;
        }
        if ((int) $user['is_verified'] !== 1) {
            return;
        }
        $userId = (int) $user['id'];
        $token = self::generateToken();
        $tokenHash = self::hashToken($token);
        $expiresAt = self::createExpiry();
        $conn = Database::getConnection();
        try {
            $conn->beginTransaction();
            $invalidateOld = $conn->prepare('
                UPDATE password_resets
                SET used_at = NOW()
                WHERE user_id = :user_id
                AND used_at IS NULL
            ');
            $invalidateOld->execute(['user_id' => $userId]);
            $stmt = $conn->prepare('
                INSERT INTO password_resets
                (
                    user_id,
                    token_hash,
                    expires_at
                )
                VALUES
                (
                    :user_id,
                    :token_hash,
                    :expires_at
                )
            ');
            $stmt->execute([
                'user_id' => $userId,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt,
            ]);
            $resetId = (int) $conn->lastInsertId();
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
        $resetLink = self::buildResetLink($token);
        $sent = Mailer::sendPasswordReset(
            (string) $user['email'],
            $resetLink
        );
        if (!$sent) {
            try {
                self::invalidate($resetId);
            } catch (Throwable $e) {
                error_log('Unable to invalidate failed password reset token: ' . $e->getMessage());
            }
            throw new RuntimeException('Unable to send password reset email.');
        }
    }

    // Verifies a password reset token.
    public static function verifyToken(string $token): ?array
    {
        $token = trim($token);
        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return null;
        }
        $reset = self::findValidReset($token);
        if ($reset === null) {
            return null;
        }
        if (self::isExpired((string) $reset['expires_at'])) {
            self::invalidate((int) $reset['id']);
            return null;
        }
        $submittedHash = self::hashToken($token);
        if (!hash_equals((string) $reset['token_hash'], $submittedHash)) {
            return null;
        }
        return $reset;
    }

    // Authorizes a user to reset their password after token verification.
    public static function authorize(int $resetId, int $userId): void
    {
        if ($resetId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException('Invalid password reset authorization.');
        }
        Session::start();
        Session::set(self::SESSION_KEY, [
            'reset_id' => $resetId,
            'user_id' => $userId,
            'verified' => true,
            'verified_at' => time(),
        ]);
    }

    // Checks if the current session has valid reset authorization.
    public static function isAuthorized(): bool
    {
        Session::start();
        $authorization = Session::get(self::SESSION_KEY);
        if (!is_array($authorization)) {
            return false;
        }
        if (empty($authorization['verified'])) {
            return false;
        }
        if (empty($authorization['reset_id']) ||
            empty($authorization['user_id']) ||
            empty($authorization['verified_at'])) {
            self::clearAuthorization();
            return false;
        }
        $authorizedAt = (int) $authorization['verified_at'];
        $maximumAge = self::AUTHORIZATION_LIFETIME_MINUTES * 60;
        if ((time() - $authorizedAt) > $maximumAge) {
            self::clearAuthorization();
            return false;
        }
        return true;
    }

    // Gets the user ID from an authorized reset session.
    public static function authorizedUserId(): ?int
    {
        if (!self::isAuthorized()) {
            return null;
        }
        $authorization = Session::get(self::SESSION_KEY);
        return (int) $authorization['user_id'];
    }

    // Performs the password reset after authorization.
    public static function reset(string $password, string $confirmPassword): void
    {
        Session::start();
        if (!self::isAuthorized()) {
            throw new RuntimeException('Password reset authorization has expired.');
        }
        self::validateNewPassword($password, $confirmPassword);
        $authorization = Session::get(self::SESSION_KEY);
        $resetId = (int) $authorization['reset_id'];
        $userId = (int) $authorization['user_id'];
        $reset = self::getResetRecord($resetId, $userId);
        if ($reset === null) {
            self::clearAuthorization();
            throw new RuntimeException('Invalid password reset request.');
        }
        if ($reset['used_at'] !== null) {
            self::clearAuthorization();
            throw new RuntimeException('This password reset link has already been used.');
        }
        if (self::isExpired((string) $reset['expires_at'])) {
            self::invalidate($resetId);
            self::clearAuthorization();
            throw new RuntimeException('This password reset request has expired.');
        }
        $user = Auth::findById($userId);
        if ($user === null) {
            self::clearAuthorization();
            throw new RuntimeException('User account does not exist.');
        }
        self::performPasswordReset($userId, $resetId, $password);
        self::clearAuthorization();
        self::sendSecurityNotification((string) $user['email']);
    }

    // Invalidates a password reset token.
    public static function invalidate(int $resetId): void
    {
        if ($resetId <= 0) {
            return;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE password_resets
            SET used_at = NOW()
            WHERE id = :id
            AND used_at IS NULL
            LIMIT 1
        ');
        $stmt->execute(['id' => $resetId]);
    }

    // Clears the password reset authorization from session.
    public static function clearAuthorization(): void
    {
        Session::remove(self::SESSION_KEY);
    }

    // Validates new password and confirmation.
    private static function validateNewPassword(string $password, string $confirmPassword): void
    {
        if ($password === '') {
            throw new InvalidArgumentException('New password is required.');
        }
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException(
                'Password must contain at least ' . self::MIN_PASSWORD_LENGTH . ' characters.'
            );
        }
        if (strlen($password) > self::MAX_PASSWORD_LENGTH) {
            throw new InvalidArgumentException('Password is too long.');
        }
        if ($password !== $confirmPassword) {
            throw new InvalidArgumentException('Passwords do not match.');
        }
    }

    // Gets a password reset record from the database.
    private static function getResetRecord(int $resetId, int $userId): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                user_id,
                token_hash,
                expires_at,
                used_at,
                created_at
            FROM password_resets
            WHERE id = :reset_id
            AND user_id = :user_id
            LIMIT 1
        ');
        $stmt->execute([
            'reset_id' => $resetId,
            'user_id' => $userId,
        ]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reset ?: null;
    }

    // Finds a valid (unused) reset record by token hash.
    private static function findValidReset(string $token): ?array
    {
        $tokenHash = self::hashToken($token);
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                user_id,
                token_hash,
                expires_at,
                used_at,
                created_at
            FROM password_resets
            WHERE token_hash = :token_hash
            AND used_at IS NULL
            LIMIT 1
        ');
        $stmt->execute(['token_hash' => $tokenHash]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reset ?: null;
    }

    // Performs the actual password reset with transaction.
    private static function performPasswordReset(int $userId, int $resetId, string $password): void
    {
        $conn = Database::getConnection();
        try {
            $conn->beginTransaction();
            Auth::updatePassword($userId, $password);
            self::invalidate($resetId);
            Session::revokeAll($userId);
            RememberMe::revokeAll($userId);
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    // Sends security notification after password change.
    private static function sendSecurityNotification(string $email): void
    {
        try {
            Mailer::sendPasswordChanged($email);
        } catch (Throwable $e) {
            error_log('Password changed email failed: ' . $e->getMessage());
        }
    }

    // Generates a cryptographically secure reset token.
    private static function generateToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_BYTES));
    }

    // Hashes a reset token for database storage.
    private static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    // Creates a token expiry datetime.
    private static function createExpiry(): string
    {
        return date(
            'Y-m-d H:i:s',
            time() + (self::TOKEN_LIFETIME_MINUTES * 60)
        );
    }

    // Checks if a token has expired.
    private static function isExpired(string $expiry): bool
    {
        $timestamp = strtotime($expiry);
        if ($timestamp === false) {
            return true;
        }
        return time() >= $timestamp;
    }

    // Builds the password reset URL with token.
    private static function buildResetLink(string $token): string
    {
        $appUrl = $_ENV['APP_URL'] ?? getenv('APP_URL');
        if ($appUrl === false || trim((string) $appUrl) === '') {
            throw new RuntimeException('APP_URL is not configured.');
        }
        return rtrim((string) $appUrl, '/') .
               '/reset-verify.php?token=' .
               rawurlencode($token);
    }
}