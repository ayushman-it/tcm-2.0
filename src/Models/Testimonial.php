<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Testimonial extends Model
{
    protected static string $table = 'testimonials';

    /**
     * @return list<array<string,mixed>>
     */
    public static function active(): array
    {
        return Database::all(
            "SELECT * FROM testimonials WHERE status = 'active' ORDER BY sort_order, id"
        );
    }
}
