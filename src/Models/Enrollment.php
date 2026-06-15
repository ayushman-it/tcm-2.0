<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Enrollment extends Model
{
    protected static string $table = 'enrollments';

    public static function exists(int $userId, int $courseId): bool
    {
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND course_id = ?',
            [$userId, $courseId]
        ) > 0;
    }

    /**
     * Enroll a user in a course (idempotent).
     */
    public static function enroll(int $userId, int $courseId, ?int $orderId = null): int
    {
        if (self::exists($userId, $courseId)) {
            $row = Database::first(
                'SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?',
                [$userId, $courseId]
            );
            return (int) ($row['id'] ?? 0);
        }

        $id = self::create([
            'user_id'   => $userId,
            'course_id' => $courseId,
            'order_id'  => $orderId,
            'status'    => 'active',
        ]);

        Database::run('UPDATE courses SET students_count = students_count + 1 WHERE id = ?', [$courseId]);
        return $id;
    }

    /**
     * Courses a user is enrolled in, with course details.
     *
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT e.*, c.title, c.slug, c.icon, c.thumbnail, c.duration, cat.name AS category_name
             FROM enrollments e
             JOIN courses c ON c.id = e.course_id
             LEFT JOIN categories cat ON cat.id = c.category_id
             WHERE e.user_id = ?
             ORDER BY e.enrolled_at DESC',
            [$userId]
        );
    }
}
