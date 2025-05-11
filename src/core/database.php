<?php
namespace App\core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if (self::$pdo === null) {
            $config = parse_ini_file(__DIR__ . '/../../.env');
            
            try {
                self::$pdo = new PDO(
                    "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4",
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

                // Проверка соединения
                self::$pdo->query('SELECT 1');
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new \RuntimeException('Database connection failed');
            }
        }
        return self::$pdo;
    }

    /**
     * Получение экземпляра PDO (alias для connect)
     */
    public static function getInstance(): PDO {
        return self::connect();
    }
}