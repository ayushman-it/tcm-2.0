<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

/**
 * Tiny active-record-ish base providing common table operations.
 */
abstract class Model
{
    /** Table name, set by each child model. */
    protected static string $table = '';

    /**
     * @return array<string,mixed>|null
     */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM ' . static::$table . ' WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @param array<string,mixed> $conditions column => value
     * @return array<string,mixed>|null
     */
    public static function findBy(array $conditions): ?array
    {
        $where = implode(' AND ', array_map(static fn ($c) => "`$c` = ?", array_keys($conditions)));
        return Database::first(
            'SELECT * FROM ' . static::$table . " WHERE $where LIMIT 1",
            array_values($conditions)
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::all('SELECT * FROM ' . static::$table . " ORDER BY $orderBy");
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function create(array $data): int
    {
        return Database::insert(static::$table, $data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function update(int $id, array $data): int
    {
        return Database::update(static::$table, $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete(static::$table, ['id' => $id]);
    }

    public static function count(string $where = '1', array $params = []): int
    {
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM ' . static::$table . " WHERE $where",
            $params
        );
    }
}
