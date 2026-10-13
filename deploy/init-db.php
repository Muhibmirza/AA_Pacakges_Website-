<?php
declare(strict_types=1);

$host = getenv('AA_DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
$port = getenv('AA_DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
$database = getenv('AA_DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'aapackages';
$username = getenv('AA_DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$password = getenv('AA_DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';

$pdo = null;
for ($attempt = 1; $attempt <= 30; $attempt++) {
    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true,
        ];
        $sslCa = getenv('AA_DB_SSL_CA');
        if ($sslCa && defined('PDO::MYSQL_ATTR_SSL_CA')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
        }
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            $options
        );
        break;
    } catch (PDOException $exception) {
        if ($attempt === 30) {
            fwrite(STDERR, "MySQL did not become ready: {$exception->getMessage()}\n");
            exit(1);
        }
        sleep(2);
    }
}

$statement = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
);
$statement->execute([$database, 'settings']);

if ((int) $statement->fetchColumn() === 0) {
    fwrite(STDOUT, "Initializing AA Packages database...\n");
    $sql = file_get_contents('/var/www/html/database/aapackages.sql');
    if ($sql === false) {
        throw new RuntimeException('Unable to read database seed file.');
    }
    $sql = preg_replace('/\ACREATE DATABASE[^\n]*\nUSE [^;]+;\s*/i', '', $sql, 1);
    $pdo->exec($sql);
    fwrite(STDOUT, "Database initialization complete.\n");
}
