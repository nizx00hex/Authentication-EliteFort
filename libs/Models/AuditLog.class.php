<?php

declare(strict_types=1);

class AuditLog
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Maximum event name length in database
     */
    private const MAX_EVENT_LENGTH = 100;

    /**
     * Maximum IP address length (IPv6)
     */
    private const MAX_IP_LENGTH = 45;

    /**
     * Maximum user agent length in database
     */
    private const MAX_USER_AGENT_LENGTH = 500;

    /**
     * Default IP when not available
     */
    private const DEFAULT_IP = null;


    /* =========================================================
       GENERIC AUDIT RECORD
    ========================================================= */

    /**
     * Record a generic audit event.
     */
    public static function record(?int $userId, string $event): void
    {
        $event = trim(strtolower($event));

        if ($event === '') {
            throw new InvalidArgumentException('Audit event is required.');
        }

        // Limit event size to DB column
        if (strlen($event) > self::MAX_EVENT_LENGTH) {
            throw new InvalidArgumentException('Audit event is too long.');
        }

        // Validate user ID
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


    /* =========================================================
       AUTHENTICATION EVENTS
    ========================================================= */

    /**
     * Record successful signup.
     */
    public static function signupSuccess(int $userId): void
    {
        self::record($userId, 'auth.signup.success');
    }

    /**
     * Record successful login.
     */
    public static function loginSuccess(int $userId): void
    {
        self::record($userId, 'auth.login.success');
    }

    /**
     * Record failed login attempt.
     */
    public static function loginFailure(?int $userId = null): void
    {
        self::record($userId, 'auth.login.failed');
    }

    /**
     * Record logout.
     */
    public static function logout(int $userId): void
    {
        self::record($userId, 'auth.logout');
    }


    /* =========================================================
       OTP EVENTS
    ========================================================= */

    /**
     * Record OTP sent.
     */
    public static function otpSent(int $userId): void
    {
        self::record($userId, 'auth.otp.sent');
    }

    /**
     * Record successful OTP verification.
     */
    public static function otpSuccess(int $userId): void
    {
        self::record($userId, 'auth.otp.verified');
    }

    /**
     * Record failed OTP verification.
     */
    public static function otpFailure(int $userId): void
    {
        self::record($userId, 'auth.otp.failed');
    }


    /* =========================================================
       PASSWORD EVENTS
    ========================================================= */

    /**
     * Record password reset request.
     */
    public static function passwordResetRequested(int $userId): void
    {
        self::record($userId, 'auth.password.reset_requested');
    }

    /**
     * Record successful password reset.
     */
    public static function passwordResetSuccess(int $userId): void
    {
        self::record($userId, 'auth.password.reset_success');
    }

    /**
     * Record password change.
     */
    public static function passwordChanged(int $userId): void
    {
        self::record($userId, 'auth.password.changed');
    }


    /* =========================================================
       SESSION EVENTS
    ========================================================= */

    /**
     * Record session creation.
     */
    public static function sessionCreated(int $userId): void
    {
        self::record($userId, 'auth.session.created');
    }

    /**
     * Record session expiration.
     */
    public static function sessionExpired(int $userId): void
    {
        self::record($userId, 'auth.session.expired');
    }

    /**
     * Record session revocation.
     */
    public static function sessionRevoked(int $userId): void
    {
        self::record($userId, 'auth.session.revoked');
    }


    /* =========================================================
       SECURITY EVENTS
    ========================================================= */

    /**
     * Record rate limit event.
     */
    public static function rateLimit(?int $userId = null): void
    {
        self::record($userId, 'security.rate_limit.blocked');
    }

    /**
     * Record CSRF failure.
     */
    public static function csrfFailure(?int $userId = null): void
    {
        self::record($userId, 'security.csrf.failed');
    }


    /* =========================================================
       REMEMBER ME EVENTS
    ========================================================= */

    /**
     * Record Remember Me token creation.
     */
    public static function rememberCreated(int $userId): void
    {
        self::record($userId, 'auth.remember.created');
    }

    /**
     * Record Remember Me token usage.
     */
    public static function rememberUsed(int $userId): void
    {
        self::record($userId, 'auth.remember.used');
    }

    /**
     * Record Remember Me token revocation.
     */
    public static function rememberRevoked(int $userId): void
    {
        self::record($userId, 'auth.remember.revoked');
    }


    /* =========================================================
       PRIVATE HELPER METHODS
    ========================================================= */

    /**
     * Get and validate client IP address.
     */
    private static function clientIp(): ?string
    {
        // For current local/direct setup, REMOTE_ADDR is the safe source
        // Do not blindly trust X-Forwarded-For unless you later configure a trusted proxy

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

        // IPv4 and IPv6 fit VARCHAR(45)
        return substr($ip, 0, self::MAX_IP_LENGTH);
    }

    /**
     * Get and validate user agent.
     */
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

        // Match VARCHAR(500)
        return substr($agent, 0, self::MAX_USER_AGENT_LENGTH);
    }
}