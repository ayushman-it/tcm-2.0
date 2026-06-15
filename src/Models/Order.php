<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Order extends Model
{
    protected static string $table = 'orders';

    /**
     * Create an order record for a course or event purchase.
     */
    public static function place(
        int $userId,
        string $itemType,
        int $itemId,
        string $itemTitle,
        float $amount,
        string $status = 'paid',
        string $paymentMethod = 'demo'
    ): int {
        return self::create([
            'user_id'        => $userId,
            'order_number'   => self::generateNumber(),
            'item_type'      => $itemType,
            'item_id'        => $itemId,
            'item_title'     => $itemTitle,
            'amount'         => $amount,
            'currency'       => 'INR',
            'status'         => $status,
            'payment_method' => $paymentMethod,
            'payment_ref'    => strtoupper(substr(bin2hex(random_bytes(8)), 0, 12)),
        ]);
    }

    public static function generateNumber(): string
    {
        return 'TCM-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }
}
