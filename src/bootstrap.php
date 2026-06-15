<?php

declare(strict_types=1);

/**
 * Application bootstrap: autoloader, config, session, helpers.
 * Included by the front controller before any routing happens.
 */

// --- PSR-4 style autoloader for the TCM\ namespace ---------------------
spl_autoload_register(static function (string $class): void {
    $prefix = 'TCM\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// --- Helpers (plain functions, loaded eagerly) -------------------------
require __DIR__ . '/Core/helpers.php';

// --- Configuration & environment --------------------------------------
$config = require dirname(__DIR__) . '/config/config.php';

date_default_timezone_set((string) $config['app']['timezone']);

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

// --- Session ------------------------------------------------------------
if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    session_name((string) $config['session']['name']);
    session_set_cookie_params([
        'lifetime' => (int) $config['session']['lifetime'],
        'path'     => '/',
        'httponly' => true,
        'secure'   => $https,
        'samesite' => 'Lax',
    ]);
    session_start();
}

return $config;
