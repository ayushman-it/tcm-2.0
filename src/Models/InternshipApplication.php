<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class InternshipApplication extends Model
{
    protected static string $table = 'internship_applications';

    public const STATUSES = [
        'submitted'    => 'Submitted',
        'under_review' => 'Under Review',
        'shortlisted'  => 'Shortlisted',
        'selected'     => 'Selected',
        'rejected'     => 'Rejected',
    ];

    public static function exists(int $userId, int $programId): bool
    {
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM internship_applications WHERE user_id = ? AND program_id = ?',
            [$userId, $programId]
        ) > 0;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT a.*, p.title AS program_title, p.slug AS program_slug
             FROM internship_applications a
             JOIN programs p ON p.id = a.program_id
             WHERE a.user_id = ? ORDER BY a.created_at DESC',
            [$userId]
        );
    }

    /**
     * @param array<string,mixed> $filters status, program_id, search
     * @return list<array<string,mixed>>
     */
    public static function search(array $filters = []): array
    {
        $sql = 'SELECT a.*, p.title AS program_title, u.name AS student_name
                FROM internship_applications a
                JOIN programs p ON p.id = a.program_id
                JOIN users u ON u.id = a.user_id
                WHERE 1';
        $params = [];
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= ' AND a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['program_id'])) {
            $sql .= ' AND a.program_id = ?';
            $params[] = $filters['program_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (a.full_name LIKE ? OR a.email LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= ' ORDER BY FIELD(a.status,\'submitted\',\'under_review\',\'shortlisted\',\'selected\',\'rejected\'), a.created_at DESC';
        return Database::all($sql, $params);
    }
}
