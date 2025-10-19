<?php
namespace App\Infrastructure;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(bool $reset = false, bool $isTest = false): PDO
    {
        if ($reset) {
            self::$instance = null;
        }

        if (self::$instance === null) {
            Env::load();

            $host = getenv('DB_HOST') ?: 'db';
            $port = getenv('DB_PORT') ?: '3306';
            $db   = $isTest ? getenv('DB_DATABASE_TEST') ?: 'books_db_test' : (getenv('DB_DATABASE') ?: 'books_db');
            $user = getenv('DB_USERNAME') ?: 'root';
            $pass = getenv('DB_PASSWORD') ?: 'root';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            for ($i = 0; $i < 5; $i++) {
                try {
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    break;
                } catch (PDOException $e) {
                    if ($i === 4) {
                        throw new \RuntimeException('Database connection error: ' . $e->getMessage());
                    }
                    sleep(2);
                }
            }
        }

        return self::$instance;
    }
}
