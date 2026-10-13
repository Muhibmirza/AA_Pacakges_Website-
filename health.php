<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');
$pdo->query('SELECT 1');
echo json_encode(['ok' => true, 'service' => 'aa-packages']);
