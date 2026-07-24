<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Singleton PDO connection built from config/database.php.
 */
class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $driver   = env('DB_CONNECTION', 'pgsql');
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = env('DB_PORT', $driver === 'pgsql' ? '5432' : '5433');
        $database = env('DB_DATABASE', '');
        $username = env('DB_USERNAME', 'postgres');
        $password = env('DB_PASSWORD', '');

        // PostgreSQL's DSN has no charset segment (encoding is a connection
        // property, not a DSN param); MySQL needs one. Build per driver.
        $dsn = match ($driver) {
            'pgsql' => "pgsql:host={$host};port={$port};dbname={$database}",
            'mysql' => "mysql:host={$host};port={$port};dbname={$database};charset=" . env('DB_CHARSET', 'utf8mb4'),
            default => "{$driver}:host={$host};port={$port};dbname={$database}",
        };

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            Logger::error('Database connection failed: ' . $e->getMessage());
            throw new PDOException('Could not connect to the database. See storage/logs/app.log for details.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }
}
