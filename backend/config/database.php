<?php
require_once __DIR__ . '/environment.php';

class DatabaseConfig
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $database = getenv('DB_NAME') ?: 'zad';
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        if ($username === false || $username === '') {
            throw new RuntimeException('Missing required environment variable: DB_USER.');
        }
        if ($password === false) {
            throw new RuntimeException('Missing required environment variable: DB_PASSWORD.');
        }

        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$connection = new PDO($dsn, $username, $password, $options);
            return self::$connection;
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.', 0, $e);
        }
    }
}
