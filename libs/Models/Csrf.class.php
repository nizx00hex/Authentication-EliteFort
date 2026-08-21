<?php

declare(strict_types=1);

class Csrf
{
    //i make this as a public for testing
    public const KEY = 'csrf_token';
    private const TOKEN_BYTES = 32;
    private const TOKEN_LENGTH = 64;
    private const HEADER_NAME = 'HTTP_X_CSRF_TOKEN';
    private const FIELD_NAME = 'csrf_token';

    // Gets the current CSRF token or creates a new one.
    public static function token(): string
    {
        Session::start();
        $token = Session::get(self::KEY);
        if (is_string($token) && $token !== '') {
            return $token;
        }
        $token = self::generate();
        Session::set(self::KEY, $token);
        return $token;
    }

    // Verifies a submitted CSRF token against the stored token.
    public static function verify(?string $token): bool 
    {
        Session::start();
        if ($token === null || $token === '') {
            return false;
        }
        $storedToken = Session::get(self::KEY);
        if (!is_string($storedToken) || $storedToken === '') {
            return false;
        }
        if (strlen($token) !== self::TOKEN_LENGTH) {
            return false;
        }
        return hash_equals($storedToken, $token);
    }

    // Requires a valid CSRF token, throws exception if invalid.
    public static function requireValid(?string $token): void
    {
        if (self::verify($token)) {
            //
            // echo 1;
            return;
        }
        http_response_code(403);
        throw new RuntimeException('Invalid or expired security token.');
    }

    // Regenerates the CSRF token (useful after login/important actions).
    public static function regenerate(): string
    {
        Session::start();
        $token = self::generate();
        Session::set(self::KEY, $token);
        return $token;
    }

    // Clears the CSRF token from session.
    public static function clear(): void
    {
        Session::start();
        Session::remove(self::KEY);
    }

    // Generates HTML hidden input field for CSRF token.
    public static function input(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . $token . '">';
    }

    // Gets CSRF token from request (POST or header).
    public static function fromRequest(): ?string
    {
        if (isset($_POST[self::FIELD_NAME]) && is_string($_POST[self::FIELD_NAME])) {
            return $_POST[self::FIELD_NAME];
        }
        $header = $_SERVER[self::HEADER_NAME] ?? null;
        if (is_string($header) && $header !== '') {
            return $header;
        }
        return null;
    }

    // Requires the request to be a POST request.
    public static function requirePost(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
        if ($method === 'POST') {
            return;
        }
        http_response_code(405);
        throw new RuntimeException('Method not allowed.');
    }

    // Convenience method to protect sensitive forms (POST + CSRF).
    public static function protect(): void
    {
        self::requirePost();
        self::requireValid(self::fromRequest());
    }

    // Generates a cryptographically secure random token.
    private static function generate(): string
    {
        return bin2hex(random_bytes(self::TOKEN_BYTES));
    }
}