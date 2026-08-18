<?php
require_once __DIR__ . '/config.php';

/**
 * Returns a shared PDO connection to ktg_billing (prepared statements only —
 * emulated prepares are disabled so parameter binding is handled by MySQL itself).
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'ktg_billing';
        $user = 'root';
        $pass = '';
        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die('Database connection failed. Please try again later.');
        }
    }
    return $pdo;
}
