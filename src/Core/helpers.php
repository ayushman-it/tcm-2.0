<?php

declare(strict_types=1);

/**
 * Global helper functions used across the application.
 */

if (!function_exists('config')) {
    /**
     * Read a value from the config array using dot notation, e.g. config('app.name').
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $config = null;
        if ($config === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
        }
        if ($key === null) {
            return $config;
        }
        $segments = explode('.', $key);
        $value = $config;
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('e')) {
    /**
     * Escape a string for safe HTML output.
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('base_url')) {
    /**
     * Build an absolute-ish URL respecting the configured base path.
     */
    function base_url(string $path = ''): string
    {
        $base = (string) config('app.base_path', '');
        $path = '/' . ltrim($path, '/');
        return $base . ($path === '/' ? '' : $path);
    }
}

if (!function_exists('redirect')) {
    /**
     * Send a redirect header and stop execution.
     */
    function redirect(string $path): never
    {
        $location = str_starts_with($path, 'http') ? $path : base_url($path);
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve previously submitted form input flashed to the session.
     */
    function old(string $key, string $default = ''): string
    {
        return (string) ($_SESSION['_old'][$key] ?? $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Set or get a one-time flash message.
     */
    function flash(string $key, ?string $message = null): ?string
    {
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return null;
        }
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
}

if (!function_exists('slugify')) {
    /**
     * Create a URL-friendly slug from a string.
     */
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
        $text = trim($text, '-');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text) ?? '';
        return $text === '' ? 'item-' . substr(md5((string) microtime(true)), 0, 8) : $text;
    }
}

if (!function_exists('money')) {
    /**
     * Format an amount as INR currency.
     */
    function money(float|int|string $amount): string
    {
        return '₹' . number_format((float) $amount, 0);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF input field for forms.
     */
    function csrf_field(): string
    {
        $token = \TCM\Core\Csrf::token();
        return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
    }
}
