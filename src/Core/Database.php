<?php

declare(strict_types=1);

namespace TCM\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Thin PDO wrapper providing a shared connection and convenience query helpers.
 */
final class Database
{
    private static ?PDO $pdo = null;

    private function __construct()
    {
    }

    /**
     * Get (and lazily create) the shared PDO connection.
     */
    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $db = $config['db'];

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $db['driver'],
            $db['host'],
            $db['port'],
            $db['database'],
            $db['charset']
        );

        try {
            self::$pdo = new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode()
            );
        }

        return self::$pdo;
    }

    /**
     * Run a prepared statement and return it.
     *
     * @param array<string,mixed>|list<mixed> $params
     */
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row or null.
     *
     * @param array<string,mixed>|list<mixed> $params
     * @return array<string,mixed>|null
     */
    public static function first(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Fetch all rows.
     *
     * @param array<string,mixed>|list<mixed> $params
     * @return list<array<string,mixed>>
     */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single scalar value.
     *
     * @param array<string,mixed>|list<mixed> $params
     */
    public static function scalar(string $sql, array $params = []): mixed
    {
        return self::run($sql, $params)->fetchColumn();
    }

    /**
     * Insert a row and return the new id.
     *
     * @param array<string,mixed> $data
     */
    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );

        self::run($sql, $data);
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Update rows by a simple where clause.
     *
     * @param array<string,mixed> $data
     * @param array<string,mixed> $where
     */
    public static function update(string $table, array $data, array $where): int
    {
        $set = implode(', ', array_map(static fn ($c) => "`$c` = :set_$c", array_keys($data)));
        $cond = implode(' AND ', array_map(static fn ($c) => "`$c` = :where_$c", array_keys($where)));

        $params = [];
        foreach ($data as $k => $v) {
            $params["set_$k"] = $v;
        }
        foreach ($where as $k => $v) {
            $params["where_$k"] = $v;
        }

        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, $set, $cond);
        return self::run($sql, $params)->rowCount();
    }

    /**
     * Delete rows by a simple where clause.
     *
     * @param array<string,mixed> $where
     */
    public static function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(static fn ($c) => "`$c` = :$c", array_keys($where)));
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, $cond);
        return self::run($sql, $where)->rowCount();
    }
}
