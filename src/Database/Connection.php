<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\Env;
use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        Env::load(dirname(__DIR__, 2) . '/.env');

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $dbName = Env::get('DB_NAME', 'perpustakaan');
        $username = Env::get('DB_USERNAME', 'root');
        $password = Env::get('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

        try {
            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Koneksi database gagal: ' . $e->getMessage(), 0, $e);
        }

        return self::$instance;
    }
}