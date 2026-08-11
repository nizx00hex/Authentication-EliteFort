<?php
class Env {
    private static function env(string $key, ?string $default = null): string {

        $value = $_ENV[$key] ?? null;

        if ($value === null || $value === '') {
            if ($default !== null) {
                return $default;
            }

            throw new RuntimeException(
                "Missing environment variable: {$key}"
            );
        }

        return (string) $value;
    }
}