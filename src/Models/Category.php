<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Category extends Model
{
    protected static string $table = 'categories';

    /**
     * @return list<array<string,mixed>>
     */
    public static function active(): array
    {
        return Database::all(
            "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order, name"
        );
    }
}
