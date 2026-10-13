<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

// Edit these values after importing database/aapackages.sql.
$dbHost = getenv('AA_DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
$dbPort = getenv('AA_DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
$dbName = getenv('AA_DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'aapackages';
$dbUser = getenv('AA_DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$dbPass = getenv('AA_DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';

try {
    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $sslCa = getenv('AA_DB_SSL_CA');
    if ($sslCa && defined('PDO::MYSQL_ATTR_SSL_CA')) {
        $pdoOptions[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    }
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        $pdoOptions
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Import database/aapackages.sql and update includes/db.php.');
}
