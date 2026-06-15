<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Simple session-based CSRF token management.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Get the current token, generating one if needed.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validate a submitted token against the session token.
     */
    public static function validate(?string $token): bool
    {
        $stored = $_SESSION[self::SESSION_KEY] ?? '';
        return is_string($token) && $token !== '' && hash_equals($stored, $token);
    }

    /**
     * Validate the token from the current request, aborting on failure for
     * state-changing methods.
     */
    public static function check(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }
        $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        if (!self::validate(is_string($token) ? $token : null)) {
            http_response_code(419);
            echo 'CSRF token mismatch. Please refresh the page and try again.';
            exit;
        }
    }
}
