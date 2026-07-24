<?php

namespace App\Core;

use PDO;

/**
 * Base model providing a small set of Active-Record-style query helpers
 * on top of PDO. Every query uses prepared statements.
 *
 * Child classes set:
 *   protected static string $table       = 'users';
 *   protected static string $primaryKey  = 'id'; // optional, defaults to 'id'
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function query(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(?string $orderBy = null): array
    {
        $sql = 'SELECT * FROM ' . static::$table;

        if ($orderBy !== null) {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        return static::query()->query($sql)->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function find(int|string $id): ?array
    {
        $stmt = static::query()->prepare(
            'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function where(string $column, mixed $operatorOrValue, mixed $value = null): array
    {
        $operator = $value === null ? '=' : $operatorOrValue;
        $value    = $value === null ? $operatorOrValue : $value;

        $stmt = static::query()->prepare(
            'SELECT * FROM ' . static::$table . " WHERE {$column} {$operator} :value"
        );
        $stmt->execute(['value' => $value]);

        return $stmt->fetchAll();
    }

    public static function create(array $data): string
    {
        $db           = static::query();
        $columns      = array_keys($data);
        $placeholders = array_map(fn (string $col): string => ":{$col}", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        // PDO::lastInsertId() only works out of the box on MySQL. On
        // PostgreSQL it needs a sequence name, so we use RETURNING instead
        // and read the new id straight back from the same statement.
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
            $sql .= ' RETURNING ' . static::$primaryKey;
            $stmt = $db->prepare($sql);
            $stmt->execute($data);

            return (string) $stmt->fetchColumn();
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($data);

        return $db->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $assignments = implode(', ', array_map(
            fn (string $col): string => "{$col} = :{$col}",
            array_keys($data)
        ));

        $sql = 'UPDATE ' . static::$table . " SET {$assignments} WHERE " . static::$primaryKey . ' = :id';

        $data['id'] = $id;

        return static::query()->prepare($sql)->execute($data);
    }

    public static function delete(int|string $id): bool
    {
        $stmt = static::query()->prepare(
            'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id'
        );

        return $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) static::query()->query('SELECT COUNT(*) FROM ' . static::$table)->fetchColumn();
    }
}
