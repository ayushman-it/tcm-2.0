<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

/**
 * Email one-time-password codes for passwordless login and password reset.
 */
final class Otp
{
    private const TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    /**
     * Generate, store and return a fresh 6-digit code for an email + purpose.
     * Any previous codes for the same email/purpose are invalidated.
     */
    public static function issue(string $email, string $purpose = 'login'): string
    {
        $email = strtolower(trim($email));
        Database::run('DELETE FROM email_otps WHERE email = ? AND purpose = ?', [$email, $purpose]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Database::insert('email_otps', [
            'email'      => $email,
            'code_hash'  => password_hash($code, PASSWORD_DEFAULT),
            'purpose'    => $purpose,
            'expires_at' => date('Y-m-d H:i:s', time() + self::TTL_MINUTES * 60),
        ]);

        return $code;
    }

    /**
     * Verify a submitted code. Returns true on success and consumes the code.
     */
    public static function verify(string $email, string $code, string $purpose = 'login'): bool
    {
        $email = strtolower(trim($email));
        $row = Database::first(
            'SELECT * FROM email_otps WHERE email = ? AND purpose = ? ORDER BY id DESC LIMIT 1',
            [$email, $purpose]
        );
        if ($row === null) {
            return false;
        }
        if (strtotime($row['expires_at']) < time() || (int) $row['attempts'] >= self::MAX_ATTEMPTS) {
            Database::delete('email_otps', ['id' => $row['id']]);
            return false;
        }

        if (!password_verify(trim($code), $row['code_hash'])) {
            Database::run('UPDATE email_otps SET attempts = attempts + 1 WHERE id = ?', [$row['id']]);
            return false;
        }

        Database::delete('email_otps', ['id' => $row['id']]);
        return true;
    }
}
