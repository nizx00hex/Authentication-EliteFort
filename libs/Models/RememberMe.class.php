<?php

declare(strict_types=1);

class RememberMe
{
    private const COOKIE_NAME = 'ef_remember';
    private const LIFETIME_DAYS = 30;
    private const SELECTOR_BYTES = 16;
    private const VALIDATOR_BYTES = 32;
    private const SAME_SITE = 'Lax';
    private const SELECTOR_LENGTH = 32;
    private const VALIDATOR_LENGTH = 64;
    private const SECONDS_PER_DAY = 86400;
    private const COOKIE_DELETE_OFFSET = 3600;

    // Creates a new remember-me token for a user.
    public static function create(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        $user = Auth::findById($userId);
        if ($user === null) {
            throw new RuntimeException('User does not exist.');
        }
        if ((int) $user['is_verified'] !== 1) {
            throw new RuntimeException('Account must be verified first.');
        }
        $selector = self::generateSelector();
        $validator = self::generateValidator();
        $validatorHash = self::hashValidator($validator);
        $expiresTimestamp = time() + (self::LIFETIME_DAYS * self::SECONDS_PER_DAY);
        $expiresAt = date('Y-m-d H:i:s', $expiresTimestamp);
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            INSERT INTO remember_tokens
            (
                user_id,
                selector,
                validator_hash,
                expires_at
            )
            VALUES
            (
                :user_id,
                :selector,
                :validator_hash,
                :expires_at
            )
        ');
        $stmt->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'validator_hash' => $validatorHash,
            'expires_at' => $expiresAt,
        ]);
        self::setCookie($selector, $validator, $expiresTimestamp);
    }

    // Authenticates a user using the remember-me cookie.
    public static function authenticate(): ?array
    {
        Session::start();
        if (Session::isAuthenticated()) {
            return Session::user();
        }
        $cookie = self::parseCookie();
        if ($cookie === null) {
            return null;
        }
        $selector = $cookie['selector'];
        $validator = $cookie['validator'];
        $token = self::findToken($selector);
        if ($token === null) {
            self::deleteCookie();
            return null;
        }
        $expiresAt = strtotime((string) $token['expires_at']);
        if ($expiresAt === false || time() >= $expiresAt) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }
        $submittedHash = self::hashValidator($validator);
        if (!hash_equals((string) $token['validator_hash'], $submittedHash)) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }
        $user = Auth::findById((int) $token['user_id']);
        if ($user === null) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }
        if ((int) $user['is_verified'] !== 1) {
            self::deleteToken((int) $token['id']);
            self::deleteCookie();
            return null;
        }
        unset($user['password']);
        Session::login($user);
        self::rotate((int) $token['id'], (int) $token['user_id']);
        return $user;
    }

    // Checks if the remember-me cookie exists.
    public static function exists(): bool
    {
        return isset($_COOKIE[self::COOKIE_NAME])
            && is_string($_COOKIE[self::COOKIE_NAME])
            && $_COOKIE[self::COOKIE_NAME] !== '';
    }

    // Rotates a remember-me token (replaces with new one).
    public static function rotate(int $tokenId, int $userId): void
    {
        if ($tokenId <= 0 || $userId <= 0) {
            throw new InvalidArgumentException('Invalid remember token.');
        }
        $selector = self::generateSelector();
        $validator = self::generateValidator();
        $validatorHash = self::hashValidator($validator);
        $expiresTimestamp = time() + (self::LIFETIME_DAYS * self::SECONDS_PER_DAY);
        $expiresAt = date('Y-m-d H:i:s', $expiresTimestamp);
        $conn = Database::getConnection();
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare('
                UPDATE remember_tokens
                SET
                    selector = :selector,
                    validator_hash = :validator_hash,
                    expires_at = :expires_at,
                    created_at = NOW()
                WHERE id = :id
                AND user_id = :user_id
                LIMIT 1
            ');
            $stmt->execute([
                'selector' => $selector,
                'validator_hash' => $validatorHash,
                'expires_at' => $expiresAt,
                'id' => $tokenId,
                'user_id' => $userId,
            ]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Unable to rotate remember token.');
            }
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            self::deleteCookie();
            throw $e;
        }
        self::setCookie($selector, $validator, $expiresTimestamp);
    }

    // Forgets the current remember-me token.
    public static function forget(): void
    {
        $cookie = self::parseCookie();
        if ($cookie !== null) {
            $token = self::findToken($cookie['selector']);
            if ($token !== null) {
                self::deleteToken((int) $token['id']);
            }
        }
        self::deleteCookie();
    }

    // Revokes all remember-me tokens for a user.
    public static function revokeAll(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            DELETE FROM remember_tokens
            WHERE user_id = :user_id
        ');
        $stmt->execute(['user_id' => $userId]);
        $cookie = self::parseCookie();
        if ($cookie === null) {
            return;
        }
        $token = self::findToken($cookie['selector']);
        self::deleteCookie();
    }

    // Generates a random selector.
    private static function generateSelector(): string
    {
        return bin2hex(random_bytes(self::SELECTOR_BYTES));
    }

    // Generates a random validator.
    private static function generateValidator(): string
    {
        return bin2hex(random_bytes(self::VALIDATOR_BYTES));
    }

    // Hashes the validator for database storage.
    private static function hashValidator(string $validator): string
    {
        return hash('sha256', $validator);
    }

    // Parses the remember-me cookie.
    private static function parseCookie(): ?array
    {
        if (!self::exists()) {
            return null;
        }
        $cookie = (string) $_COOKIE[self::COOKIE_NAME];
        $parts = explode(':', $cookie, 2);
        if (count($parts) !== 2) {
            self::deleteCookie();
            return null;
        }
        [$selector, $validator] = $parts;
        if (!preg_match('/^[a-f0-9]{' . self::SELECTOR_LENGTH . '}$/i', $selector)
            || !preg_match('/^[a-f0-9]{' . self::VALIDATOR_LENGTH . '}$/i', $validator)) {
            self::deleteCookie();
            return null;
        }
        return [
            'selector' => strtolower($selector),
            'validator' => strtolower($validator),
        ];
    }

    // Finds a token by selector.
    private static function findToken(string $selector): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            SELECT
                id,
                user_id,
                selector,
                validator_hash,
                expires_at,
                created_at
            FROM remember_tokens
            WHERE selector = :selector
            LIMIT 1
        ');
        $stmt->execute(['selector' => $selector]);
        $token = $stmt->fetch(PDO::FETCH_ASSOC);
        return $token ?: null;
    }

    // Deletes a token by ID.
    private static function deleteToken(int $tokenId): void
    {
        if ($tokenId <= 0) {
            return;
        }
        $conn = Database::getConnection();
        $stmt = $conn->prepare('
            DELETE FROM remember_tokens
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $tokenId]);
    }

    // Sets the remember-me cookie.
    private static function setCookie(string $selector, string $validator, int $expires): void
    {
        if (headers_sent()) {
            throw new RuntimeException('Cannot create remember cookie after headers have been sent.');
        }
        $value = $selector . ':' . $validator;
        setcookie(
            self::COOKIE_NAME,
            $value,
            [
                'expires' => $expires,
                'path' => '/',
                'secure' => self::isHttps(),
                'httponly' => true,
                'samesite' => self::SAME_SITE,
            ]
        );
        $_COOKIE[self::COOKIE_NAME] = $value;
    }

    // Deletes the remember-me cookie.
    private static function deleteCookie(): void
    {
        if (!headers_sent()) {
            setcookie(
                self::COOKIE_NAME,
                '',
                [
                    'expires' => time() - self::COOKIE_DELETE_OFFSET,
                    'path' => '/',
                    'secure' => self::isHttps(),
                    'httponly' => true,
                    'samesite' => self::SAME_SITE,
                ]
            );
        }
        unset($_COOKIE[self::COOKIE_NAME]);
    }

    // Checks if the connection is using HTTPS.
    public static function isHttps(): bool
    {
        return isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== ''
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    }
}