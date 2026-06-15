<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

/**
 * Aggregates a student's portfolio: projects, skills, achievements, certificates.
 */
final class Portfolio
{
    /**
     * @return list<array<string,mixed>>
     */
    public static function projects(int $userId): array
    {
        return Database::all(
            'SELECT * FROM portfolio_projects WHERE user_id = ? ORDER BY is_featured DESC, sort_order, id DESC',
            [$userId]
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function skills(int $userId): array
    {
        return Database::all(
            'SELECT * FROM portfolio_skills WHERE user_id = ? ORDER BY level DESC, name',
            [$userId]
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function achievements(int $userId): array
    {
        return Database::all(
            'SELECT * FROM portfolio_achievements WHERE user_id = ? ORDER BY achieved_on DESC, id DESC',
            [$userId]
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function certificates(int $userId): array
    {
        return Database::all(
            'SELECT cert.*, c.title AS course_title FROM certificates cert
             LEFT JOIN courses c ON c.id = cert.course_id
             WHERE cert.user_id = ? ORDER BY cert.issued_at DESC',
            [$userId]
        );
    }

    /**
     * Completeness score (0-100) to encourage students to grow their portfolio.
     */
    public static function strength(int $userId): int
    {
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$userId]);
        $score = 0;

        if ($profile) {
            if (!empty($profile['headline'])) {
                $score += 10;
            }
            if (!empty($profile['bio'])) {
                $score += 10;
            }
            if (!empty($profile['github_url']) || !empty($profile['linkedin_url'])) {
                $score += 10;
            }
        }
        if (self::projects($userId) !== []) {
            $score += 30;
        }
        if (self::skills($userId) !== []) {
            $score += 20;
        }
        if (self::achievements($userId) !== [] || self::certificates($userId) !== []) {
            $score += 20;
        }

        return min(100, $score);
    }
}
