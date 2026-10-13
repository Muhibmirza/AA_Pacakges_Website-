<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$root = realpath(dirname(__DIR__));
$file = $root !== false ? realpath($root . str_replace('/', DIRECTORY_SEPARATOR, $path)) : false;

if ($file !== false && $root !== false && str_starts_with($file, $root) && is_file($file)) {
    return false;
}

if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require $root . '/admin/index.php';
    return true;
}

require $root . '/index.php';
return true;
