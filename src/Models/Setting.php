<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

/**
 * Key/value site settings with a per-request cache.
 */
final class Setting
{
    /** @var array<string,string>|null */
    private static ?array $cache = null;

    /**
     * @return array<string,string>
     */
    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all('SELECT `key`, `value` FROM settings') as $row) {
                self::$cache[$row['key']] = (string) $row['value'];
            }
        }
        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::all()[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        Database::run(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value]
        );
        self::$cache = null;
    }
}
