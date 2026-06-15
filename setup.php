<?php

declare(strict_types=1);

/**
 * The Code Munk — Web Installer (for Hostinger / shared hosting without SSH).
 *
 * 1. Create a MySQL database + user in hPanel and put the credentials in .env
 * 2. Open https://your-domain/setup.php in a browser
 * 3. Click "Run installation"
 * 4. DELETE this file afterwards (it self-locks, but deleting is safest)
 */

$config = require __DIR__ . '/config/config.php';
$db = $config['db'];
$lockFile = __DIR__ . '/storage/installed.lock';
$alreadyInstalled = is_file($lockFile);

$messages = [];
$errors = [];
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyInstalled) {
    $fresh = isset($_POST['fresh']);
    try {
        if (!extension_loaded('pdo_mysql')) {
            throw new RuntimeException('The pdo_mysql PHP extension is not enabled. In hPanel set PHP to 8.1+ and enable pdo_mysql.');
        }

        $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $db['host'], $db['port'], $db['charset']);
        $pdo = new PDO($dsn, $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $name = $db['database'];
        if ($fresh) {
            $pdo->exec("DROP DATABASE IF EXISTS `$name`");
            $messages[] = "Dropped existing database `$name`.";
        }
        // On Hostinger the DB is pre-created; CREATE may fail due to privileges,
        // so only attempt it and ignore a privilege error.
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            // Ignore — database already exists / no create privilege on shared hosting.
        }
        $pdo->exec("USE `$name`");

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        if ($schema === false) {
            throw new RuntimeException('Could not read database/schema.sql');
        }
        $pdo->exec($schema);
        $messages[] = 'Schema imported (tables created).';

        if (isset($_POST['seed'])) {
            $seed = file_get_contents(__DIR__ . '/database/seed.sql');
            if ($seed !== false) {
                $pdo->exec($seed);
                $messages[] = 'Demo data seeded.';
            }
        }

        @file_put_contents($lockFile, date('c') . " installed\n");
        $done = true;
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Code Munk — Setup</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0b0f1a; color: #e7ecf5; display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .card { background: #121826; border: 1px solid #232c40; border-radius: 16px; padding: 32px; max-width: 560px; width: 92%; }
        h1 { margin: 0 0 4px; font-size: 1.4rem; }
        .muted { color: #8b97ad; font-size: .9rem; }
        code { background: #0b0f1a; padding: 2px 6px; border-radius: 6px; }
        .row { margin: 14px 0; }
        label { display: block; font-size: .9rem; margin: 8px 0; }
        button { background: linear-gradient(135deg,#6c5ce7,#8b7bff); color: #fff; border: 0; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: .95rem; }
        .ok { background: rgba(0,210,168,.12); color: #00d2a8; padding: 10px 14px; border-radius: 10px; margin: 6px 0; }
        .err { background: rgba(255,90,90,.12); color: #ff6b6b; padding: 10px 14px; border-radius: 10px; margin: 6px 0; }
        .kv { font-size: .85rem; color: #8b97ad; }
        a { color: #8b7bff; }
    </style>
</head>
<body>
<div class="card">
    <h1>The Code Munk — Setup</h1>
    <p class="muted">One-time database installer for shared hosting.</p>

    <?php foreach ($messages as $m): ?><div class="ok"><?= htmlspecialchars($m) ?></div><?php endforeach; ?>
    <?php foreach ($errors as $e): ?><div class="err"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

    <?php if ($alreadyInstalled): ?>
        <div class="ok">Already installed. For safety, please <strong>delete setup.php</strong> from your server.</div>
        <p class="muted">Admin: <code>admin@thecodemunk.com</code> / <code>admin123</code> — change this password after logging in.</p>
        <p><a href="/">Go to website</a> · <a href="/auth/login">Sign in</a></p>
    <?php elseif ($done): ?>
        <div class="ok"><strong>Installation complete!</strong></div>
        <p class="muted">Now <strong>delete this <code>setup.php</code></strong> file from your server.</p>
        <p class="muted">Demo logins — Admin: <code>admin@thecodemunk.com</code> / <code>admin123</code>,
            Student: <code>student@thecodemunk.com</code> / <code>student123</code>. Change these after first login.</p>
        <p><a href="/">Go to website</a> · <a href="/auth/login">Sign in</a> · <a href="/admin">Admin dashboard</a></p>
    <?php else: ?>
        <div class="kv">
            Using DB host <code><?= htmlspecialchars((string) $db['host']) ?></code>,
            database <code><?= htmlspecialchars((string) $db['database']) ?></code>,
            user <code><?= htmlspecialchars((string) $db['username']) ?></code>.
            <br>Edit <code>.env</code> if these are wrong.
        </div>
        <form method="post">
            <div class="row">
                <label><input type="checkbox" name="seed" checked> Load demo data (recommended for first run)</label>
                <label><input type="checkbox" name="fresh"> Drop &amp; recreate the database first (erases everything)</label>
            </div>
            <button type="submit">Run installation</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
