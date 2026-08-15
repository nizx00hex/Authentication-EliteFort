<?php

declare(strict_types=1);

class Csrf
{
    /* =========================================================
       CONFIGURATION CONSTANTS
    ========================================================= */

    /**
     * Session key for storing CSRF token
     */
    private const KEY = 'csrf_token';

    /**
     * 32 random bytes = 256-bit CSRF token
     */
    private const TOKEN_BYTES = 32;

    /**
     * Token length in characters (64 hex characters)
     */
    private const TOKEN_LENGTH = 64;

    /**
     * HTTP header name for CSRF token in AJAX requests
     */
    private const HEADER_NAME = 'HTTP_X_CSRF_TOKEN';

    /**
     * Form field name for CSRF token
     */
    private const FIELD_NAME = 'csrf_token';


    /* =========================================================
       GET / CREATE TOKEN
    ========================================================= */

    /**
     * Get the current CSRF token or create a new one.
     */
    public static function token(): string
    {
        Session::start();

        // If token already exists, reuse it
        $token = Session::get(self::KEY);

        if (is_string($token) && $token !== '') {
            return $token;
        }

        // No token exists - generate a new secure random token
        $token = self::generate();

        // Store token inside PHP session
        Session::set(self::KEY, $token);

        return $token;
    }


    /* =========================================================
       VERIFY TOKEN
    ========================================================= */

    /**
     * Verify a submitted CSRF token against the stored token.
     */
    public static function verify(?string $token): bool 
    {
        Session::start();

        // Submitted token missing
        if ($token === null || $token === '') {
            return false;
        }

        // Get expected token directly
        // Do NOT call token() here because token() would create a new token if one is missing
        $storedToken = Session::get(self::KEY);

        // No token exists in session
        if (!is_string($storedToken) || $storedToken === '') {
            return false;
        }

        // Prevent unnecessary oversized input
        // Our token is 64 hex characters
        if (strlen($token) !== self::TOKEN_LENGTH) {
            return false;
        }

        // Timing-safe comparison
        return hash_equals($storedToken, $token);
    }


    /* =========================================================
       REQUIRE VALID TOKEN
    ========================================================= */

    /**
     * Require a valid CSRF token, throw exception if invalid.
     */
    public static function requireValid(?string $token): void
    {
        if (self::verify($token)) {
            return;
        }

        // Invalid CSRF request
        http_response_code(403);
        throw new RuntimeException('Invalid or expired security token.');
    }


    /* =========================================================
       REGENERATE TOKEN
    ========================================================= */

    /**
     * Regenerate the CSRF token (useful after login/important actions).
     */
    public static function regenerate(): string
    {
        Session::start();

        // Replace old CSRF token
        $token = self::generate();
        Session::set(self::KEY, $token);

        return $token;
    }


    /* =========================================================
       REMOVE TOKEN
    ========================================================= */

    /**
     * Clear the CSRF token from session.
     */
    public static function clear(): void
    {
        Session::start();
        Session::remove(self::KEY);
    }


    /* =========================================================
       HTML HELPERS
    ========================================================= */

    /**
     * Generate HTML hidden input field for CSRF token.
     */
    public static function input(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . $token . '">';
    }


    /* =========================================================
       REQUEST HELPERS
    ========================================================= */

    /**
     * Get CSRF token from request (POST or header).
     */
    public static function fromRequest(): ?string
    {
        // Standard HTML POST form
        if (isset($_POST[self::FIELD_NAME]) && is_string($_POST[self::FIELD_NAME])) {
            return $_POST[self::FIELD_NAME];
        }

        // AJAX/fetch request with X-CSRF-Token header
        $header = $_SERVER[self::HEADER_NAME] ?? null;

        if (is_string($header) && $header !== '') {
            return $header;
        }

        return null;
    }

    /**
     * Require the request to be a POST request.
     */
    public static function requirePost(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');

        if ($method === 'POST') {
            return;
        }

        http_response_code(405);
        throw new RuntimeException('Method not allowed.');
    }


    /* =========================================================
       PROTECT FORM (POST + CSRF)
    ========================================================= */

    /**
     * Convenience method to protect sensitive forms.
     * Checks: 1. Request must be POST, 2. CSRF token must be valid
     */
    public static function protect(): void
    {
        self::requirePost();
        self::requireValid(self::fromRequest());
    }


    /* =========================================================
       TOKEN GENERATION
    ========================================================= */

    /**
     * Generate a cryptographically secure random token.
     */
    private static function generate(): string
    {
        return bin2hex(random_bytes(self::TOKEN_BYTES));
    }
}