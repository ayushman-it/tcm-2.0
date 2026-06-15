<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class LiveSession extends Model
{
    protected static string $table = 'live_sessions';

    /**
     * Upcoming sessions across programs/courses a user is enrolled in.
     *
     * @return list<array<string,mixed>>
     */
    public static function upcomingForUser(int $userId): array
    {
        return Database::all(
            "SELECT ls.*, p.title AS program_title, c.title AS course_title
             FROM live_sessions ls
             LEFT JOIN programs p ON p.id = ls.program_id
             LEFT JOIN courses c ON c.id = ls.course_id
             WHERE ls.status IN ('scheduled','live')
               AND (
                    ls.program_id IN (SELECT program_id FROM program_enrollments WHERE user_id = ?)
                 OR ls.course_id IN (SELECT course_id FROM enrollments WHERE user_id = ?)
               )
             ORDER BY ls.session_date, ls.start_time",
            [$userId, $userId]
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function all(string $orderBy = 'session_date DESC, start_time DESC'): array
    {
        return Database::all(
            "SELECT ls.*, p.title AS program_title, c.title AS course_title
             FROM live_sessions ls
             LEFT JOIN programs p ON p.id = ls.program_id
             LEFT JOIN courses c ON c.id = ls.course_id
             ORDER BY $orderBy"
        );
    }
}
