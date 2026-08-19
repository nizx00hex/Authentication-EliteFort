<?php

declare(strict_types=1);

class Otp
{
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MIN = 100000;
    private const OTP_MAX = 999999;

    // Generates and sends a new OTP for user verification.
    public static function createForUser(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        $user = Auth::findById($userId);
        if ($user === null) {
            throw new RuntimeException('User does not exist.');
        }
        if ((int) $user['is_verified'] === 1) {
            throw new RuntimeException('This account is already verified.');
        }
        $otp = self::generate();
        $otpHash = self::hash($otp);
        $expiry = self::createExpiry();
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET
                otp_hash = :otp_hash,
                otp_expiry = :otp_expiry,
                otp_attempts = 0,
                otp_last_sent = NOW()
            WHERE id = :id
            AND is_verified = 0
            LIMIT 1
        ');
        $stmt->execute([
            'otp_hash' => $otpHash,
            'otp_expiry' => $expiry,
            'id' => $userId,
        ]);
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Unable to create OTP.');
        }
        $sent = Mailer::sendOtp((string) $user['email'], $otp);
        if (!$sent) {
            self::clear($userId);
            throw new RuntimeException('Unable to send verification email.');
        }
    }

    // Verifies the submitted OTP for a user.
    public static function verify(int $userId, string $otp): bool
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        $otp = trim($otp);
        if (!self::validFormat($otp)) {
            throw new InvalidArgumentException('Enter a valid 6-digit OTP.');
        }
        $data = self::getOtpData($userId);
        if ($data === null) {
            throw new RuntimeException('Invalid verification request.');
        }
        if ((int) $data['is_verified'] === 1) {
            throw new RuntimeException('This account is already verified.');
        }
        if (empty($data['otp_hash'])) {
            throw new RuntimeException('No active OTP exists. Request a new OTP.');
        }
        if (empty($data['otp_expiry'])) {
            throw new RuntimeException('No active OTP exists. Request a new OTP.');
        }
        if (self::isExpired((string) $data['otp_expiry'])) {
            throw new RuntimeException('OTP has expired. Request a new OTP.');
        }
        $attempts = (int) $data['otp_attempts'];
        if ($attempts >= self::MAX_ATTEMPTS) {
            throw new RuntimeException('Too many incorrect OTP attempts. Request a new OTP.');
        }
        $correct = self::verifyHash($otp, (string) $data['otp_hash']);
        if (!$correct) {
            self::incrementAttempts($userId);
            $remaining = self::MAX_ATTEMPTS - ($attempts + 1);
            if ($remaining <= 0) {
                throw new RuntimeException('Too many incorrect OTP attempts. Request a new OTP.');
            }
            throw new RuntimeException("Incorrect OTP. {$remaining} attempt(s) remaining.");
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET
                is_verified = 1,
                otp_hash = NULL,
                otp_expiry = NULL,
                otp_attempts = 0,
                otp_last_sent = NULL
            WHERE id = :id
            AND is_verified = 0
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Unable to verify account.');
        }
        return true;
    }

    // Resends OTP with cooldown checking.
    public static function resend(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        $user = Auth::findById($userId);
        if ($user === null) {
            throw new RuntimeException('User does not exist.');
        }
        if ((int) $user['is_verified'] === 1) {
            throw new RuntimeException('This account is already verified.');
        }
        if (!self::canResend($userId)) {
            $remaining = self::remainingCooldown($userId);
            throw new RuntimeException("Please wait {$remaining} second(s) before requesting another OTP.");
        }
        self::createForUser($userId);
    }

    // Checks if user can request a new OTP.
    public static function canResend(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }
        $data = self::getOtpData($userId);
        if ($data === null) {
            return false;
        }
        if ((int) $data['is_verified'] === 1) {
            return false;
        }
        if (empty($data['otp_last_sent'])) {
            return true;
        }
        $lastSent = strtotime((string) $data['otp_last_sent']);
        if ($lastSent === false) {
            return true;
        }
        $elapsed = time() - $lastSent;
        return $elapsed >= self::RESEND_COOLDOWN_SECONDS;
    }

    // Gets remaining cooldown time in seconds.
    public static function remainingCooldown(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $data = self::getOtpData($userId);
        if ($data === null || empty($data['otp_last_sent'])) {
            return 0;
        }
        $lastSent = strtotime((string) $data['otp_last_sent']);
        if ($lastSent === false) {
            return 0;
        }
        $elapsed = time() - $lastSent;
        $remaining = self::RESEND_COOLDOWN_SECONDS - $elapsed;
        return max(0, $remaining);
    }

    // Generates a numeric OTP.
    private static function generate(): string
    {
        return (string) random_int(self::OTP_MIN, self::OTP_MAX);
    }

    // Hashes the OTP using password_hash.
    private static function hash(string $otp): string
    {
        $hash = password_hash($otp, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Unable to secure OTP.');
        }
        return $hash;
    }

    // Verifies OTP against stored hash.
    private static function verifyHash(string $otp, string $hash): bool
    {
        if ($otp === '' || $hash === '') {
            return false;
        }
        return password_verify($otp, $hash);
    }

    // Creates OTP expiry datetime.
    private static function createExpiry(): string
    {
        return date('Y-m-d H:i:s', time() + (self::OTP_EXPIRY_MINUTES * 60));
    }

    // Checks if OTP has expired.
    private static function isExpired(string $expiry): bool
    {
        $timestamp = strtotime($expiry);
        if ($timestamp === false) {
            return true;
        }
        return time() >= $timestamp;
    }

    // Increments failed OTP attempt counter.
    private static function incrementAttempts(int $userId): void
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET otp_attempts = otp_attempts + 1
            WHERE id = :id
            AND is_verified = 0
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
    }

    // Clears OTP data from user record.
    private static function clear(int $userId): void
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            UPDATE Auth
            SET
                otp_hash = NULL,
                otp_expiry = NULL,
                otp_attempts = 0,
                otp_last_sent = NULL
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
    }

    // Gets OTP data for a user.
    private static function getOtpData(int $userId): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                email,
                is_verified,
                otp_hash,
                otp_expiry,
                otp_attempts,
                otp_last_sent
            FROM Auth
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // Validates OTP format (exactly N digits).
    private static function validFormat(string $otp): bool
    {
        return preg_match('/^\d{' . self::OTP_LENGTH . '}$/', $otp) === 1;
    }
}