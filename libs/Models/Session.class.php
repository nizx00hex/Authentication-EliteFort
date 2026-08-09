<?php

declare(strict_types=1);

final class Session
{
    private const SESSION_NAME = 'EF_SESSION';
    private const REMEMBER_EMAIL_COOKIE = 'elitefort_user';
    private function __construct() {}


    /*
    |--------------------------------------------------------------------------
    | Start Session
    |--------------------------------------------------------------------------
    */
    public static function start() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_name(self::SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }


    /*
    |--------------------------------------------------------------------------
    | Basic Session Set
    |--------------------------------------------------------------------------
    */
    public static function set(string $key, mixed $value): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Basic Session Get
    |--------------------------------------------------------------------------
    */
    public static function get(string $key): mixed
    {
        self::start();

        return $_SESSION[$key] ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Session Key
    |--------------------------------------------------------------------------
    */
    public static function has(string $key): bool
    {
        self::start();

        return isset($_SESSION[$key]);
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Session Key
    |--------------------------------------------------------------------------
    */
    public static function remove(string $key): void
    {
        self::start();

        unset($_SESSION[$key]);
    }


    /*
    |--------------------------------------------------------------------------
    | Login User
    |--------------------------------------------------------------------------
    */
    public static function login(array $user): void
    {
        self::start();

        /*
         * Prevent session fixation.
         * Create a new session ID after successful login.
         */
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'user_id' => (int) $user['id'],
            // 'isLoggedIn' => true,
            'fullname' => (string) $user['fullname'],
            'username' => (string) $user['username'],
            'email' => (string) $user['email'],
            'login_time' => time(),
        ];
    }

    public static function getAuth(string $key, mixed $default = null) {
        self::start();
        
        return $_SESSION['auth'][$key] ?? $default;
    }


    /*
    |--------------------------------------------------------------------------
    | Check User Logged In
    |--------------------------------------------------------------------------
    */
    public static function isLoggedIn(): bool
    {
        self::start();

        return isset($_SESSION['auth']['user_id']);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Logged In User
    |--------------------------------------------------------------------------
    */
    public static function user(): ?array
    {
        self::start();

        if (
            isset($_SESSION['auth']) &&
            is_array($_SESSION['auth'])
        ) {
            return $_SESSION['auth'];
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Logged In User ID
    |--------------------------------------------------------------------------
    */
    public static function userId(): ?int
    {
        $user = self::user();

        if ($user === null) {
            return null;
        }

        return (int) $user['user_id'];
    }


    /*
    |--------------------------------------------------------------------------
    | Require Login
    |--------------------------------------------------------------------------
    | Use this on protected pages.
    |--------------------------------------------------------------------------
    */
    public static function requireLogin(string $loginPage = 'login.php'): void {
        if (!self::isLoggedIn()) {

            self::flash(
                'error',
                'Please login to access this page.'
            );

            header('Location: ' . $loginPage);
            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Set Pending OTP Verification
    |--------------------------------------------------------------------------
    | Used after signup.
    |--------------------------------------------------------------------------
    */
    public static function setPendingVerification(int $userId, string $email): void {
        self::start();

        $_SESSION['pending_verification'] = [
            'user_id' => $userId,
            'email' => $email,
            'created_at' => time(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Pending Verification
    |--------------------------------------------------------------------------
    */
    public static function pendingVerification(): ?array
    {
        self::start();

        if (
            isset($_SESSION['pending_verification']) &&
            is_array($_SESSION['pending_verification'])
        ) {
            return $_SESSION['pending_verification'];
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Pending User ID
    |--------------------------------------------------------------------------
    */
    public static function pendingUserId(): ?int
    {
        $pending = self::pendingVerification();

        if ($pending === null) {
            return null;
        }

        return (int) $pending['user_id'];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Pending Email
    |--------------------------------------------------------------------------
    */
    public static function pendingEmail(): ?string
    {
        $pending = self::pendingVerification();

        if ($pending === null) {
            return null;
        }

        return (string) $pending['email'];
    }


    /*
    |--------------------------------------------------------------------------
    | Clear Pending Verification
    |--------------------------------------------------------------------------
    | Use after successful OTP verification.
    |--------------------------------------------------------------------------
    */
    public static function clearPendingVerification(): void
    {
        self::start();

        unset($_SESSION['pending_verification']);
    }


    /*
    |--------------------------------------------------------------------------
    | Set Flash Message
    |--------------------------------------------------------------------------
    */
    public static function flash(string $type, string $message ): void {
        self::start();

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Flash Message
    |--------------------------------------------------------------------------
    | Message is deleted immediately after reading.
    |--------------------------------------------------------------------------
    */
    public static function getFlash(): ?array
    {
        self::start();

        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        return is_array($flash)
            ? $flash
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | Generate CSRF Token
    |--------------------------------------------------------------------------
    */
    public static function csrfToken(): string
    {
        self::start();

        if (empty($_SESSION['csrf_token'])) {

            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return (string) $_SESSION['csrf_token'];
    }


    /*
    |--------------------------------------------------------------------------
    | Verify CSRF Token
    |--------------------------------------------------------------------------
    */
    public static function verifyCsrf(?string $token): bool
    {
        self::start();

        if (
            $token === null ||
            empty($_SESSION['csrf_token'])
        ) {
            return false;
        }

        return hash_equals(
            (string) $_SESSION['csrf_token'],
            $token
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Remember Email
    |--------------------------------------------------------------------------
    | This does NOT keep the user logged in.
    | It only remembers the login email.
    |--------------------------------------------------------------------------
    */
    public static function rememberEmail(
        string $email,
        int $days = 30
    ): void {
        setcookie(
            self::REMEMBER_EMAIL_COOKIE,
            $email,
            [
                'expires' => time() + ($days * 86400),
                'path' => '/',
                'domain' => '',
                'secure' => self::isHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Remembered Email
    |--------------------------------------------------------------------------
    */
    public static function rememberedEmail(): string
    {
        if (!isset($_COOKIE[self::REMEMBER_EMAIL_COOKIE])) {
            return '';
        }

        return trim(
            (string) $_COOKIE[self::REMEMBER_EMAIL_COOKIE]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Forget Remembered Email
    |--------------------------------------------------------------------------
    */
    public static function forgetRememberedEmail(): void
    {
        setcookie(
            self::REMEMBER_EMAIL_COOKIE,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => self::isHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Logout User
    |--------------------------------------------------------------------------
    */
    public static function logout(): void
    {
        self::start();

        /*
         * Remove all session variables.
         */
        $_SESSION = [];


        /*
         * Delete session cookie from browser.
         */
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => 'Lax',
                ]
            );
        }


        /*
         * Delete server-side session.
         */
        session_destroy();
    }


    /*
    |--------------------------------------------------------------------------
    | Check HTTPS
    |--------------------------------------------------------------------------
    */
    private static function isHttps(): bool
    {
        return isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== ''
            && $_SERVER['HTTPS'] !== 'off';
    }
}