<?php

declare(strict_types=1);

/**
 * CLI installer for The Code Munk backend.
 *
 * Creates the database (if missing), imports the schema and seed data.
 *
 * Usage:
 *   php backend/bin/install.php          # schema + seed
 *   php backend/bin/install.php --fresh  # drop & recreate the database first
 *   php backend/bin/install.php --no-seed
 */

$config = require dirname(__DIR__) . '/config/config.php';
$db = $config['db'];

$fresh = in_array('--fresh', $argv, true);
$seed = !in_array('--no-seed', $argv, true);

if (!extension_loaded('pdo_mysql')) {
    fwrite(STDERR, "[!] The pdo_mysql PHP extension is not enabled.\n");
    fwrite(STDERR, "    Enable it in php.ini (extension=pdo_mysql) and retry.\n");
    exit(1);
}

$dsnServer = sprintf('%s:host=%s;port=%d;charset=%s', $db['driver'], $db['host'], $db['port'], $db['charset']);

try {
    $pdo = new PDO($dsnServer, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, '[!] Could not connect to MySQL: ' . $e->getMessage() . "\n");
    exit(1);
}

$name = $db['database'];

if ($fresh) {
    echo "Dropping database `$name`...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$name`");
}

echo "Creating database `$name` (if not exists)...\n";
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$name`");

$run = static function (PDO $pdo, string $file): void {
    if (!is_file($file)) {
        fwrite(STDERR, "[!] Missing SQL file: $file\n");
        exit(1);
    }
    echo 'Importing ' . basename($file) . "...\n";
    $sql = file_get_contents($file) ?: '';
    $pdo->exec($sql);
};

$run($pdo, dirname(__DIR__) . '/database/schema.sql');

if ($seed) {
    $run($pdo, dirname(__DIR__) . '/database/seed.sql');
}

echo "\n✔ Installation complete.\n";
echo "  Admin   : admin@thecodemunk.com / admin123\n";
echo "  Student : student@thecodemunk.com / student123\n";
