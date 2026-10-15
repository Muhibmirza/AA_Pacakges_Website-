<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/models/Setting.php';
require_once ROOT_PATH . '/controllers/AdminController.php';

$settings = (new Setting($pdo))->all();
$controller = new AdminController($pdo, $settings);
$page = $_GET['page'] ?? '';
if ($page === '') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = rtrim(BASE_URL, '/') . '/' . ADMIN_PATH;
    if (strpos($uri, $base) === 0) {
        $page = trim(substr($uri, strlen($base)), '/');
    }
}
$page = $page ?: 'login';
$_GET['page'] = $page;

if ($page === 'login') {
    $controller->login();
} else {
    $controller->dispatch($page);
}
