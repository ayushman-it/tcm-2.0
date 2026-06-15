<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Post extends Model
{
    protected static string $table = 'posts';

    /**
     * @return list<array<string,mixed>>
     */
    public static function published(): array
    {
        return Database::all(
            "SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC, id DESC"
        );
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::findBy(['slug' => $slug]);
    }
}
