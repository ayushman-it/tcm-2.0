<?php

declare(strict_types=1);

/**
 * Router script for PHP's built-in web server (development only).
 *
 * Usage:
 *   php -S 127.0.0.1:8000 router.php
 *
 * Serves the original static site + assets directly and routes dynamic
 * application paths through the front controller (app.php).
 */

$root = __DIR__;
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$uri = '/' . ltrim($uri, '/');

// Block access to application internals.
if (preg_match('#^/(src|config|database|storage|views|bin)(/|$)#', $uri)
    || preg_match('#(^|/)\.(env|git)#', $uri)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// Homepage -> original static design.
if ($uri === '/' && is_file($root . '/index.html')) {
    readfile($root . '/index.html');
    return true;
}

// Serve existing static files (html, css, js, images, uploads) directly.
$file = $root . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}

// Everything else -> front controller.
require $root . '/app.php';
