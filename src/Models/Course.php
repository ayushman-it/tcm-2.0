<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Course extends Model
{
    protected static string $table = 'courses';

    /**
     * List courses with their category name, optionally filtered.
     *
     * @param array<string,mixed> $filters status, category_id, audience, search, featured
     * @return list<array<string,mixed>>
     */
    public static function listWithCategory(array $filters = []): array
    {
        $sql = 'SELECT c.*, cat.name AS category_name, cat.audience
                FROM courses c
                LEFT JOIN categories cat ON cat.id = c.category_id
                WHERE 1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND c.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['category_id'])) {
            $sql .= ' AND c.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['audience'])) {
            $sql .= ' AND cat.audience = ?';
            $params[] = $filters['audience'];
        }
        if (!empty($filters['featured'])) {
            $sql .= ' AND c.is_featured = 1';
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (c.title LIKE ? OR c.subtitle LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' ORDER BY c.is_featured DESC, c.id DESC';
        return Database::all($sql, $params);
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            'SELECT c.*, cat.name AS category_name FROM courses c
             LEFT JOIN categories cat ON cat.id = c.category_id
             WHERE c.slug = ? LIMIT 1',
            [$slug]
        );
    }

    /**
     * Full curriculum (modules with their lessons) for a course.
     *
     * @return list<array<string,mixed>>
     */
    public static function curriculum(int $courseId): array
    {
        $modules = Database::all(
            'SELECT * FROM course_modules WHERE course_id = ? ORDER BY position, id',
            [$courseId]
        );
        foreach ($modules as &$module) {
            $module['lessons'] = Database::all(
                'SELECT * FROM course_lessons WHERE module_id = ? ORDER BY position, id',
                [$module['id']]
            );
        }
        return $modules;
    }

    /**
     * Recalculate the discount percentage for display.
     */
    public static function discountPercent(array $course): int
    {
        $price = (float) $course['price'];
        $original = (float) ($course['original_price'] ?? 0);
        if ($original <= 0 || $original <= $price) {
            return 0;
        }
        return (int) round((($original - $price) / $original) * 100);
    }

    /**
     * Calculate seats_filled from total_seats - seats_left (backward compat).
     */
    public static function seatsFilled(array $course): int
    {
        if (isset($course['seats_filled'])) {
            return (int) $course['seats_filled'];
        }
        return max(0, (int) $course['total_seats'] - (int) $course['seats_left']);
    }
}
