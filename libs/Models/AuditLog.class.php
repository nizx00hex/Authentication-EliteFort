<?php

declare(strict_types=1);

class AuditLog
{
    private const MAX_EVENT_LENGTH = 100;
    private const MAX_IP_LENGTH = 45;
    private const MAX_USER_AGENT_LENGTH = 500;
    private const DEFAULT_IP = null;

    // Records a generic audit event.
    public static function record(?int $userId, string $event): void
    {
        $event = trim(strtolower($event));
        if ($event === '') {
            throw new InvalidArgumentException('Audit event is required.');
        }
        if (strlen($event) > self::MAX_EVENT_LENGTH) {
            throw new InvalidArgumentException('Audit event is too long.');
        }
        if ($userId !== null && $userId <= 0) {
            $userId = null;
        }
        $ipAddress = self::clientIp();
        $userAgent = self::userAgent();
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            INSERT INTO audit_logs
            (
                user_id,
                event,
                ip_address,
                user_agent
            )
            VALUES
            (
                :user_id,
                :event,
                :ip_address,
                :user_agent
            )
        ');
        $stmt->execute([
            'user_id' => $userId,
            'event' => $event,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    // Records successful signup.
    public static function signupSuccess(int $userId): void
    {
        self::record($userId, 'auth.signup.success');
    }

    // Records successful login.
    public static function loginSuccess(int $userId): void
    {
        self::record($userId, 'auth.login.success');
    }

    // Records failed login attempt.
    public static function loginFailure(?int $userId = null): void
    {
        self::record($userId, 'auth.login.failed');
    }

    // Records logout.
    public static function logout(int $userId): void
    {
        self::record($userId, 'auth.logout');
    }

    // Records OTP sent.
    public static function otpSent(int $userId): void
    {
        self::record($userId, 'auth.otp.sent');
    }

    // Records successful OTP verification.
    public static function otpSuccess(int $userId): void
    {
        self::record($userId, 'auth.otp.verified');
    }

    // Records failed OTP verification.
    public static function otpFailure(int $userId): void
    {
        self::record($userId, 'auth.otp.failed');
    }

    // Records password reset request.
    public static function passwordResetRequested(int $userId): void
    {
        self::record($userId, 'auth.password.reset_requested');
    }

    // Records successful password reset.
    public static function passwordResetSuccess(int $userId): void
    {
        self::record($userId, 'auth.password.reset_success');
    }

    // Records password change.
    public static function passwordChanged(int $userId): void
    {
        self::record($userId, 'auth.password.changed');
    }

    // Records session creation.
    public static function sessionCreated(int $userId): void
    {
        self::record($userId, 'auth.session.created');
    }

    // Records session expiration.
    public static function sessionExpired(int $userId): void
    {
        self::record($userId, 'auth.session.expired');
    }

    // Records session revocation.
    public static function sessionRevoked(int $userId): void
    {
        self::record($userId, 'auth.session.revoked');
    }

    // Records rate limit event.
    public static function rateLimit(?int $userId = null): void
    {
        self::record($userId, 'security.rate_limit.blocked');
    }

    // Records CSRF failure.
    public static function csrfFailure(?int $userId = null): void
    {
        self::record($userId, 'security.csrf.failed');
    }

    // Records Remember Me token creation.
    public static function rememberCreated(int $userId): void
    {
        self::record($userId, 'auth.remember.created');
    }

    // Records Remember Me token usage.
    public static function rememberUsed(int $userId): void
    {
        self::record($userId, 'auth.remember.used');
    }

    // Records Remember Me token revocation.
    public static function rememberRevoked(int $userId): void
    {
        self::record($userId, 'auth.remember.revoked');
    }

    // Gets and validates client IP address.
    private static function clientIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if ($ip === null) {
            return self::DEFAULT_IP;
        }
        $ip = trim((string) $ip);
        if ($ip === '') {
            return self::DEFAULT_IP;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return self::DEFAULT_IP;
        }
        return substr($ip, 0, self::MAX_IP_LENGTH);
    }

    // Gets and validates user agent.
    private static function userAgent(): ?string
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if ($agent === null) {
            return null;
        }
        $agent = trim((string) $agent);
        if ($agent === '') {
            return null;
        }
        return substr($agent, 0, self::MAX_USER_AGENT_LENGTH);
    }
}