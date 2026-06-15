<?php
/**
 * The Code Munk - Application Configuration
 *
 * Values are read from environment variables (see .env.example) with sane
 * defaults so the app can boot in development. Never commit a real .env file.
 */

declare(strict_types=1);

if (isset($GLOBALS['__TCM_CONFIG']) && is_array($GLOBALS['__TCM_CONFIG'])) {
    return $GLOBALS['__TCM_CONFIG'];
}

// ---------------------------------------------------------------------------
// Lightweight .env loader (no external dependency)
// ---------------------------------------------------------------------------
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Strip surrounding quotes
        if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'")) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Read an environment value with a fallback default.
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return match (strtolower($value)) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => $value,
        };
    }
}

return $GLOBALS['__TCM_CONFIG'] = [
    'app' => [
        'name'      => env('APP_NAME', 'The Code Munk'),
        'env'       => env('APP_ENV', 'development'),
        'debug'     => (bool) env('APP_DEBUG', true),
        // Base URL path where the app is served, e.g. "" or "/tcm-2.0/backend/public"
        'base_path' => rtrim((string) env('APP_BASE_PATH', ''), '/'),
        'url'       => env('APP_URL', 'http://localhost:8000'),
        'timezone'  => env('APP_TIMEZONE', 'Asia/Kolkata'),
        'key'       => env('APP_KEY', 'change-this-secret-key-in-production'),
    ],
    'db' => [
        'driver'   => env('DB_DRIVER', 'mysql'),
        'host'     => env('DB_HOST', '127.0.0.1'),
        'port'     => (int) env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'tcm'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset'  => env('DB_CHARSET', 'utf8mb4'),
    ],
    'session' => [
        'name'     => env('SESSION_NAME', 'tcm_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
    ],
    'mail' => [
        'host'       => env('MAIL_HOST', 'smtp.gmail.com'),
        'port'       => (int) env('MAIL_PORT', 465),
        'username'   => env('MAIL_USERNAME', ''),
        'password'   => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'ssl'), // ssl (465) or tls (587)
        'from_email' => env('MAIL_FROM', env('MAIL_USERNAME', 'no-reply@thecodemunk.com')),
        'from_name'  => env('MAIL_FROM_NAME', 'The Code Munk'),
        // Where contact/lead/application notifications are sent.
        'admin_email' => env('MAIL_ADMIN', env('MAIL_USERNAME', '')),
    ],
    'uploads' => [
        // Absolute path on disk where public uploaded files are stored
        'path'     => dirname(__DIR__) . '/uploads',
        // Public URL prefix to reach them
        'url'      => '/uploads',
        // Private storage (not web-accessible) for sensitive files like resumes
        'private_path' => dirname(__DIR__) . '/storage',
        'max_size' => (int) env('UPLOAD_MAX_SIZE', 5 * 1024 * 1024), // 5MB
        'allowed'  => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'],
        'resume_allowed' => ['pdf', 'doc', 'docx'],
    ],
];
