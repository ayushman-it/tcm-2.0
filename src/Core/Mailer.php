<?php

declare(strict_types=1);

namespace TCM\Core;

use RuntimeException;

/**
 * Minimal SMTP mailer (no external dependency) supporting Gmail via SSL (465)
 * or STARTTLS (587) with AUTH LOGIN. Sends HTML email with a plain-text part.
 */
final class Mailer
{
    /**
     * Send an email. Returns true on success; never throws to the caller —
     * failures are logged so user-facing flows can decide how to react.
     */
    public static function send(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool
    {
        try {
            return self::deliver($to, $subject, $htmlBody, $textBody);
        } catch (\Throwable $e) {
            error_log('[Mailer] ' . $e->getMessage());
            return false;
        }
    }

    public static function isConfigured(): bool
    {
        $m = config('mail');
        return !empty($m['username']) && !empty($m['password']);
    }

    private static function deliver(string $to, string $subject, string $htmlBody, ?string $textBody): bool
    {
        $m = config('mail');
        if (empty($m['username']) || empty($m['password'])) {
            throw new RuntimeException('Mail is not configured (.env MAIL_USERNAME/MAIL_PASSWORD).');
        }

        $host = (string) $m['host'];
        $port = (int) $m['port'];
        $encryption = strtolower((string) $m['encryption']);
        $useImplicitSsl = $encryption === 'ssl';

        $transport = $useImplicitSsl ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
        $ctx = stream_context_create([
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false],
        ]);

        $fp = @stream_socket_client($transport, $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            throw new RuntimeException("SMTP connect failed: {$errstr} ({$errno})");
        }
        stream_set_timeout($fp, 20);

        $read = static function () use ($fp): string {
            $data = '';
            while (($line = fgets($fp, 515)) !== false) {
                $data .= $line;
                // Last line of a reply has a space at position 4 (e.g. "250 OK").
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            return $data;
        };
        $cmd = static function (string $command, array $expect) use ($fp, $read): string {
            fwrite($fp, $command . "\r\n");
            $resp = $read();
            $code = (int) substr($resp, 0, 3);
            if (!in_array($code, $expect, true)) {
                throw new RuntimeException("SMTP error after '" . explode(' ', $command)[0] . "': " . trim($resp));
            }
            return $resp;
        };

        $read(); // server greeting
        $ehloHost = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $cmd("EHLO {$ehloHost}", [250]);

        if (!$useImplicitSsl && $encryption === 'tls') {
            $cmd('STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('STARTTLS negotiation failed.');
            }
            $cmd("EHLO {$ehloHost}", [250]);
        }

        $cmd('AUTH LOGIN', [334]);
        $cmd(base64_encode((string) $m['username']), [334]);
        $cmd(base64_encode((string) $m['password']), [235]);

        $from = (string) $m['from_email'];
        $fromName = (string) $m['from_name'];
        $cmd("MAIL FROM:<{$from}>", [250]);
        $cmd("RCPT TO:<{$to}>", [250, 251]);
        $cmd('DATA', [354]);

        $textBody ??= trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody)));
        $boundary = 'tcm-' . bin2hex(random_bytes(8));

        $headers = [
            'From: =?UTF-8?B?' . base64_encode($fromName) . "?= <{$from}>",
            "To: <{$to}>",
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'MIME-Version: 1.0',
            'Date: ' . date('r'),
            'Message-ID: <' . bin2hex(random_bytes(12)) . "@{$ehloHost}>",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
        ];

        $body = implode("\r\n", $headers) . "\r\n\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($textBody)) . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($htmlBody)) . "\r\n"
            . "--{$boundary}--\r\n";

        // Dot-stuffing for SMTP DATA.
        $body = preg_replace('/^\./m', '..', $body) ?? $body;
        $cmd($body . "\r\n.", [250]);
        $cmd('QUIT', [221]);
        fclose($fp);

        return true;
    }

    /**
     * Wrap content in a simple branded HTML template.
     */
    public static function template(string $heading, string $bodyHtml): string
    {
        return '<div style="font-family:Arial,sans-serif;background:#f4f5f8;padding:24px;">'
            . '<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #ececf1;">'
            . '<div style="background:linear-gradient(135deg,#6c5ce7,#8b7bff);padding:20px 24px;color:#fff;font-size:20px;font-weight:700;">The Code Munk</div>'
            . '<div style="padding:24px;color:#1f2430;font-size:15px;line-height:1.6;">'
            . '<h2 style="margin:0 0 12px;font-size:18px;">' . htmlspecialchars($heading, ENT_QUOTES) . '</h2>'
            . $bodyHtml
            . '</div>'
            . '<div style="padding:16px 24px;color:#9ca3af;font-size:12px;border-top:1px solid #f0f0f4;">© ' . date('Y') . ' The Code Munk · This is an automated message.</div>'
            . '</div></div>';
    }
}
