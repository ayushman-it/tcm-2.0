<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class EventRegistration extends Model
{
    protected static string $table = 'event_registrations';

    public static function exists(int $eventId, int $userId): bool
    {
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM event_registrations WHERE event_id = ? AND user_id = ?',
            [$eventId, $userId]
        ) > 0;
    }

    /**
     * Register a user for an event and bump the filled-seat counter.
     */
    public static function register(int $eventId, int $userId): int
    {
        if (self::exists($eventId, $userId)) {
            $row = Database::first(
                'SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?',
                [$eventId, $userId]
            );
            return (int) ($row['id'] ?? 0);
        }

        $id = self::create([
            'event_id' => $eventId,
            'user_id'  => $userId,
            'status'   => 'registered',
        ]);

        Database::run(
            'UPDATE events SET seats_filled = LEAST(total_seats, seats_filled + 1) WHERE id = ?',
            [$eventId]
        );
        return $id;
    }

    /**
     * Events a user has registered for, with event details.
     *
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT r.*, e.title, e.slug, e.event_date, e.event_time, e.status AS event_status, e.type, e.recording_url
             FROM event_registrations r
             JOIN events e ON e.id = r.event_id
             WHERE r.user_id = ?
             ORDER BY e.event_date DESC',
            [$userId]
        );
    }
}
