<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Lightweight request abstraction over PHP superglobals.
 */
final class Request
{
    public static function method(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        // Support method spoofing via _method for HTML forms.
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper((string) $_POST['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $spoofed;
            }
        }
        return $method;
    }

    /**
     * The request path without the configured base path or query string.
     */
    public static function path(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = (string) config('app.base_path', '');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }

    public static function isJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($accept, 'application/json')
            || str_contains($contentType, 'application/json')
            || str_starts_with(self::path(), '/api');
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? self::json()[$key] ?? $default;
    }

    public static function string(string $key, string $default = ''): string
    {
        $value = self::input($key, $default);
        return is_scalar($value) ? trim((string) $value) : $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = self::input($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Decoded JSON body, cached per request.
     *
     * @return array<string,mixed>
     */
    public static function json(): array
    {
        static $data = null;
        if ($data === null) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            $data = is_array($decoded) ? $decoded : [];
        }
        return $data;
    }

    /**
     * All request input (query + body + json).
     *
     * @return array<string,mixed>
     */
    public static function all(): array
    {
        return array_merge($_GET, $_POST, self::json());
    }
}
