<?php

declare(strict_types=1);

/**
 * Send a test email to verify SMTP settings.
 *
 * Usage:  php bin/test-mail.php [recipient@example.com]
 */

require dirname(__DIR__) . '/src/bootstrap.php';

use TCM\Core\Mailer;

$to = $argv[1] ?? (string) config('mail.admin_email');
if ($to === '') {
    fwrite(STDERR, "No recipient. Set MAIL_ADMIN in .env or pass an address.\n");
    exit(1);
}

echo "Sending test email to {$to} via " . config('mail.host') . ':' . config('mail.port') . " ...\n";

$ok = Mailer::send(
    $to,
    'The Code Munk — SMTP test',
    Mailer::template('SMTP is working 🎉', '<p>This is a test email sent at ' . date('r') . '.</p>')
);

echo $ok ? "✔ Sent successfully.\n" : "✘ Failed — check credentials and that the host allows outbound SMTP.\n";
exit($ok ? 0 : 1);
