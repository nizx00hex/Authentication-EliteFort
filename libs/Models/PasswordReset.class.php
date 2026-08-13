<?php

declare(strict_types=1);

class PasswordReset
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Reset email link lifetime (in minutes)
     */
    private const TOKEN_LIFETIME_MINUTES = 30;

    /**
     * After clicking a valid reset link, user has this many minutes
     * to submit the new password form
     */
    private const AUTHORIZATION_LIFETIME_MINUTES = 10;

    /**
     * Temporary PHP session key for reset authorization
     */
    private const SESSION_KEY = 'password_reset';

    /**
     * Token length in bytes (32 bytes = 256 bits = 64 hex characters)
     */
    private const TOKEN_BYTES = 32;

    /**
     * Minimum password length
     */
    private const MIN_PASSWORD_LENGTH = 8;

    /**
     * Maximum password length to prevent DOS
     */
    private const MAX_PASSWORD_LENGTH = 4096;


    /* =========================================================
       REQUEST PASSWORD RESET
    ========================================================= */

    /**
     * Request a password reset email for a user.
     */
    public static function request(string $email): void
    {
        $email = strtolower(trim($email));

        // Invalid email - return silently (generic response for security)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        // Find user internally
        $user = Auth::findByEmail($email);

        // Do not tell the user whether this account exists (security)
        if ($user === null) {
            return;
        }

        // Optionally require verified accounts
        if ((int) $user['is_verified'] !== 1) {
            return;
        }

        $userId = (int) $user['id'];

        // Generate cryptographically secure reset token
        // Plain token goes ONLY into the email
        $token = self::generateToken();

        // Database receives only hash
        $tokenHash = self::hashToken($token);

        // Create expiration datetime
        $expiresAt = self::createExpiry();

        $conn = Database::getConnection();

        try {
            $conn->beginTransaction();

            // Invalidate any previous unused password reset token for this user
            // Only the newest reset link should work
            $invalidateOld = $conn->prepare('
                UPDATE password_resets
                SET used_at = NOW()
                WHERE user_id = :user_id
                AND used_at IS NULL
            ');

            $invalidateOld->execute(['user_id' => $userId]);

            // Insert new password-reset request
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

        // Build reset URL
        $resetLink = self::buildResetLink($token);

        // Send email
        $sent = Mailer::sendPasswordReset(
            (string) $user['email'],
            $resetLink
        );

        // If email sending failed, invalidate this reset token
        if (!$sent) {
            try {
                self::invalidate($resetId);
            } catch (Throwable $e) {
                error_log('Unable to invalidate failed password reset token: ' . $e->getMessage());
            }

            throw new RuntimeException('Unable to send password reset email.');
        }
    }


    /* =========================================================
       VERIFY RESET TOKEN
    ========================================================= */

    /**
     * Verify a password reset token.
     */
    public static function verifyToken(string $token): ?array
    {
        $token = trim($token);

        // Validate token format (64 hex characters)
        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return null;
        }

        // Find matching active reset request
        $reset = self::findValidReset($token);

        if ($reset === null) {
            return null;
        }

        // Check expiration
        if (self::isExpired((string) $reset['expires_at'])) {
            // Expired token should no longer remain usable
            self::invalidate((int) $reset['id']);
            return null;
        }

        // Extra defensive comparison
        $submittedHash = self::hashToken($token);

        if (!hash_equals((string) $reset['token_hash'], $submittedHash)) {
            return null;
        }

        return $reset;
    }


    /* =========================================================
       AUTHORIZE PASSWORD RESET
    ========================================================= */

    /**
     * Authorize a user to reset their password after token verification.
     */
    public static function authorize(int $resetId, int $userId): void
    {
        if ($resetId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException('Invalid password reset authorization.');
        }

        Session::start();

        // Store temporary authorization
        // This is NOT a logged-in auth session
        Session::set(self::SESSION_KEY, [
            'reset_id' => $resetId,
            'user_id' => $userId,
            'verified' => true,
            'verified_at' => time(),
        ]);
    }


    /* =========================================================
       CHECK RESET AUTHORIZATION
    ========================================================= */

    /**
     * Check if the current session has valid reset authorization.
     */
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

        // Temporary reset authorization also expires
        $authorizedAt = (int) $authorization['verified_at'];
        $maximumAge = self::AUTHORIZATION_LIFETIME_MINUTES * 60;

        if ((time() - $authorizedAt) > $maximumAge) {
            self::clearAuthorization();
            return false;
        }

        return true;
    }


    /* =========================================================
       GET AUTHORIZED USER
    ========================================================= */

    /**
     * Get the user ID from an authorized reset session.
     */
    public static function authorizedUserId(): ?int
    {
        if (!self::isAuthorized()) {
            return null;
        }

        $authorization = Session::get(self::SESSION_KEY);
        return (int) $authorization['user_id'];
    }


    /* =========================================================
       RESET PASSWORD
    ========================================================= */

    /**
     * Perform the password reset after authorization.
     */
    public static function reset(string $password, string $confirmPassword): void
    {
        Session::start();

        // User must have previously opened and verified a valid reset link
        if (!self::isAuthorized()) {
            throw new RuntimeException('Password reset authorization has expired.');
        }

        // Validate password
        self::validateNewPassword($password, $confirmPassword);

        $authorization = Session::get(self::SESSION_KEY);
        $resetId = (int) $authorization['reset_id'];
        $userId = (int) $authorization['user_id'];

        // Re-check reset record in database
        // Never trust only PHP session state
        $reset = self::getResetRecord($resetId, $userId);

        if ($reset === null) {
            self::clearAuthorization();
            throw new RuntimeException('Invalid password reset request.');
        }

        // Already used?
        if ($reset['used_at'] !== null) {
            self::clearAuthorization();
            throw new RuntimeException('This password reset link has already been used.');
        }

        // Expired?
        if (self::isExpired((string) $reset['expires_at'])) {
            self::invalidate($resetId);
            self::clearAuthorization();
            throw new RuntimeException('This password reset request has expired.');
        }

        // Get user before changing password (needed for security email)
        $user = Auth::findById($userId);

        if ($user === null) {
            self::clearAuthorization();
            throw new RuntimeException('User account does not exist.');
        }

        // Perform security-sensitive database changes
        self::performPasswordReset($userId, $resetId, $password);

        // Reset authorization no longer needed
        self::clearAuthorization();

        // Send security notification
        self::sendSecurityNotification((string) $user['email']);
    }


    /* =========================================================
       INVALIDATE RESET TOKEN
    ========================================================= */

    /**
     * Invalidate a password reset token.
     */
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


    /* =========================================================
       CLEAR TEMPORARY RESET AUTHORIZATION
    ========================================================= */

    /**
     * Clear the password reset authorization from session.
     */
    public static function clearAuthorization(): void
    {
        Session::remove(self::SESSION_KEY);
    }


    /* =========================================================
       VALIDATION METHODS
    ========================================================= */

    /**
     * Validate new password and confirmation.
     */
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


    /* =========================================================
       DATABASE OPERATIONS
    ========================================================= */

    /**
     * Get a password reset record from the database.
     */
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

    /**
     * Find a valid (unused) reset record by token hash.
     */
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


    /* =========================================================
       PASSWORD RESET EXECUTION
    ========================================================= */

    /**
     * Perform the actual password reset with transaction.
     */
    private static function performPasswordReset(int $userId, int $resetId, string $password): void
    {
        $conn = Database::getConnection();

        try {
            $conn->beginTransaction();

            // Hash and update password
            Auth::updatePassword($userId, $password);

            // Mark reset link as used
            self::invalidate($resetId);

            // Force logout on every active device
            Session::revokeAll($userId);

            // Remove all persistent login tokens
            RememberMe::revokeAll($userId);

            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }


    /* =========================================================
       EMAIL NOTIFICATIONS
    ========================================================= */

    /**
     * Send security notification after password change.
     */
    private static function sendSecurityNotification(string $email): void
    {
        try {
            Mailer::sendPasswordChanged($email);
        } catch (Throwable $e) {
            // Email failure should not undo the password reset
            error_log('Password changed email failed: ' . $e->getMessage());
        }
    }


    /* =========================================================
       TOKEN GENERATION & HASHING
    ========================================================= */

    /**
     * Generate a cryptographically secure reset token.
     */
    private static function generateToken(): string
    {
        // 32 bytes = 256 bits randomness
        // bin2hex makes it a 64-character token
        return bin2hex(random_bytes(self::TOKEN_BYTES));
    }

    /**
     * Hash a reset token for database storage.
     */
    private static function hashToken(string $token): string
    {
        // SHA-256 is appropriate here because reset tokens are
        // already high-entropy random secrets.
        // Unlike passwords, we need deterministic hashing so
        // the database can locate the token record efficiently.
        return hash('sha256', $token);
    }


    /* =========================================================
       EXPIRY MANAGEMENT
    ========================================================= */

    /**
     * Create a token expiry datetime.
     */
    private static function createExpiry(): string
    {
        return date(
            'Y-m-d H:i:s',
            time() + (self::TOKEN_LIFETIME_MINUTES * 60)
        );
    }

    /**
     * Check if a token has expired.
     */
    private static function isExpired(string $expiry): bool
    {
        $timestamp = strtotime($expiry);

        // Invalid expiry should fail closed
        if ($timestamp === false) {
            return true;
        }

        return time() >= $timestamp;
    }


    /* =========================================================
       URL BUILDING
    ========================================================= */

    /**
     * Build the password reset URL with token.
     */
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