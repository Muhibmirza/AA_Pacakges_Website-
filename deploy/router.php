<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$root = realpath(dirname(__DIR__));
$file = $root !== false ? realpath($root . str_replace('/', DIRECTORY_SEPARATOR, $path)) : false;
$publicDemo = filter_var(getenv('AA_PUBLIC_DEMO') ?: 'false', FILTER_VALIDATE_BOOL);

if ($publicDemo && ($path === '/admin' || str_starts_with($path, '/admin/'))) {
    http_response_code(404);
    echo 'Page not found.';
    return true;
}

if ($path === '/health.php') {
    require $root . '/health.php';
    return true;
}

$isPublicAsset = str_starts_with($path, '/public/') || str_starts_with($path, '/uploads/');
if ($isPublicAsset && $file !== false && $root !== false && str_starts_with($file, $root) && is_file($file)) {
    return false;
}

if ($isPublicAsset) {
    http_response_code(404);
    echo 'File not found.';
    return true;
}

if ($file !== false && $root !== false && str_starts_with($file, $root) && is_file($file) && $path !== '/index.php') {
    http_response_code(404);
    echo 'Page not found.';
    return true;
}

if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require $root . '/admin/index.php';
    return true;
}

require $root . '/index.php';
return true;
