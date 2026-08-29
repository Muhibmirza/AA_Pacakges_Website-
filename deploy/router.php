<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath('/var/www/html' . $path);
$root = realpath('/var/www/html');

if ($file !== false && $root !== false && str_starts_with($file, $root) && is_file($file)) {
    return false;
}

if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require '/var/www/html/admin/index.php';
    return true;
}

require '/var/www/html/index.php';
return true;
