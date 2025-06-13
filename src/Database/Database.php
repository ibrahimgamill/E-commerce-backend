<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    public static function getConnection(): PDO
    {
        try {
            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'];
            $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
}
