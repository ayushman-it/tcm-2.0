<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Program extends Model
{
    protected static string $table = 'programs';

    public const TYPES = [
        'live_classes'   => 'Live Classes',
        'learning_track' => 'Learning Track',
        'internship'     => 'Internship',
        'summer_campus'  => 'Summer Campus',
        'bootcamp'       => 'Bootcamp',
        'bundle'         => 'Bundle',
    ];

    /**
     * @param array<string,mixed> $filters status, type, search, featured
     * @return list<array<string,mixed>>
     */
    public static function search(array $filters = []): array
    {
        $sql = 'SELECT * FROM programs WHERE 1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $sql .= ' AND type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['featured'])) {
            $sql .= ' AND is_featured = 1';
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (title LIKE ? OR subtitle LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' ORDER BY is_featured DESC, id DESC';
        return Database::all($sql, $params);
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::findBy(['slug' => $slug]);
    }

    /**
     * Courses bundled into a program.
     *
     * @return list<array<string,mixed>>
     */
    public static function courses(int $programId): array
    {
        return Database::all(
            'SELECT c.* FROM program_courses pc
             JOIN courses c ON c.id = pc.course_id
             WHERE pc.program_id = ? ORDER BY c.id',
            [$programId]
        );
    }

    /**
     * Live sessions attached to a program.
     *
     * @return list<array<string,mixed>>
     */
    public static function sessions(int $programId): array
    {
        return Database::all(
            'SELECT * FROM live_sessions WHERE program_id = ? ORDER BY session_date, start_time',
            [$programId]
        );
    }

    /**
     * @param list<int> $courseIds
     */
    public static function syncCourses(int $programId, array $courseIds): void
    {
        Database::delete('program_courses', ['program_id' => $programId]);
        foreach (array_unique(array_map('intval', $courseIds)) as $courseId) {
            if ($courseId > 0) {
                Database::run(
                    'INSERT IGNORE INTO program_courses (program_id, course_id) VALUES (?, ?)',
                    [$programId, $courseId]
                );
            }
        }
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function highlightList(?string $highlights): array
    {
        if ($highlights === null || trim($highlights) === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $highlights) ?: [])));
    }
}
