<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function route(string $name = 'home', array $params = []): string
{
    $path = '';
    switch ($name) {
        case 'shop':
            $path = 'shop';
            break;
        case 'product':
            $path = 'product/' . rawurlencode((string)($params['slug'] ?? ''));
            unset($params['slug']);
            break;
        case 'category':
            $path = 'category/' . rawurlencode((string)($params['slug'] ?? ''));
            unset($params['slug']);
            break;
        case 'quote':
        case 'cart':
        case 'checkout':
        case 'confirmation':
        case 'login':
        case 'register':
        case 'logout':
        case 'account':
        case 'track':
        case 'about':
        case 'contact':
        case 'faqs':
        case 'terms':
        case 'privacy':
        case 'returns':
        case 'cart-add':
        case 'cart-update':
        case 'cart-drawer':
        case 'cart-coupon':
        case 'discount-lead':
        case 'contact-submit':
            $path = $name;
            break;
        default:
            $path = '';
    }
    $query = $params ? '?' . http_build_query($params) : '';
    return url($path) . $query;
}

function admin_route(string $page = 'dashboard'): string
{
    return url(ADMIN_PATH . '/' . trim($page, '/'));
}

function request_origin(): string
{
    if (SITE_URL !== '') {
        return SITE_URL;
    }
    $forwardedProto = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')[0]));
    $isHttps = $forwardedProto === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $host = preg_replace('/[^a-z0-9.\-:\[\]]/i', '', $_SERVER['HTTP_HOST'] ?? '') ?: 'localhost';
    return ($isHttps ? 'https' : 'http') . '://' . $host;
}

function absolute_url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return request_origin() . '/' . ltrim($path, '/');
}

function canonical_url(): string
{
    $page = current_page();
    if ($page === 'home') {
        return absolute_url(url(''));
    }
    if ($page === 'product' && !empty($_GET['slug'])) {
        return absolute_url(route('product', ['slug' => (string)$_GET['slug']]));
    }
    if ($page === 'shop' && !empty($_GET['category'])) {
        return absolute_url(route('category', ['slug' => (string)$_GET['category']]));
    }
    return absolute_url(route($page));
}

function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}

function content_url(string $path): string
{
    $path = trim($path);
    if ($path === '' || $path[0] === '#' || preg_match('#^(https?:)?//#i', $path)) {
        return $path ?: '#';
    }
    if (strpos($path, '/aapackages/') === 0) {
        $path = substr($path, strlen('/aapackages/'));
    }
    return url(ltrim($path, '/'));
}

function uploaded(string $path): string
{
    if ($path === '') {
        return asset('assets/images/categories/custom-printed-boxes.png');
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (strpos($path, 'public/') === 0 || strpos($path, 'uploads/') === 0) {
        return url($path);
    }
    if (UPLOAD_URL !== 'uploads' && strpos($path, UPLOAD_URL . '/') === 0) {
        return rtrim(UPLOAD_URL, '/') . '/' . ltrim(substr($path, strlen(UPLOAD_URL)), '/');
    }
    return url(ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function page_text(?string $value): string
{
    $value = preg_replace('#<(br\s*/?|/p|/li|/h[1-6])>#i', "\n", (string)$value) ?? '';
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace("/[ \t]+/", ' ', $value) ?? $value;
    $value = preg_replace("/\n{3,}/", "\n\n", $value) ?? $value;
    return trim($value);
}

function money(float $amount): string
{
    return 'Rs. ' . number_format($amount, 0);
}

function redirect(string $path): never
{
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . url($path));
    }
    exit;
}

function current_page(): string
{
    if (!empty($_GET['page'])) {
        return $_GET['page'];
    }
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = rtrim(BASE_URL, '/');
    if ($base && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    $parts = array_values(array_filter(explode('/', trim($uri, '/'))));
    if (!$parts || $parts[0] === 'index.php') {
        return 'home';
    }
    if ($parts[0] === 'category') {
        $_GET['category'] = $parts[1] ?? '';
        return 'shop';
    }
    if ($parts[0] === 'product') {
        $_GET['slug'] = $parts[1] ?? '';
        return 'product';
    }
    return $parts[0];
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function require_fields(array $data, array $rules): array
{
    $clean = [];
    foreach ($rules as $field => $rule) {
        $value = trim((string)($data[$field] ?? ''));
        $label = $rule['label'] ?? $field;
        if (($rule['required'] ?? false) && $value === '') {
            throw new InvalidArgumentException($label . ' is required.');
        }
        if ($value === '') {
            $clean[$field] = '';
            continue;
        }
        $min = $rule['min'] ?? 0;
        $max = $rule['max'] ?? 1000;
        if (strlen($value) < $min || strlen($value) > $max) {
            throw new InvalidArgumentException($label . ' length is invalid.');
        }
        if (($rule['type'] ?? 'text') === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }
        if (($rule['type'] ?? 'text') === 'phone' && !preg_match('/^\+?[0-9\s-]{7,20}$/', $value)) {
            throw new InvalidArgumentException('Please enter a valid phone number.');
        }
        if (($rule['type'] ?? 'text') === 'int') {
            if (!preg_match('/^\d+$/', $value)) {
                throw new InvalidArgumentException($label . ' must be a number.');
            }
            $num = (int)$value;
            if (isset($rule['min_value']) && $num < $rule['min_value']) {
                throw new InvalidArgumentException($label . ' is too low.');
            }
            if (isset($rule['max_value']) && $num > $rule['max_value']) {
                throw new InvalidArgumentException($label . ' is too high.');
            }
            $clean[$field] = $num;
            continue;
        }
        if (!empty($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
            throw new InvalidArgumentException($label . ' format is invalid.');
        }
        $clean[$field] = $value;
    }
    return $clean;
}

function status_class(string $status): string
{
    return 'status-' . strtolower(str_replace([' ', '_'], '-', $status));
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string)$value, '-');
    return $value !== '' ? $value : bin2hex(random_bytes(4));
}

function upload_image(string $field, string $folder): ?string
{
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tmp = $_FILES[$field]['tmp_name'];
    $mime = mime_content_type($tmp);
    if (!isset($allowed[$mime]) || $_FILES[$field]['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Upload must be a JPG, PNG, or WEBP image under 3MB.');
    }
    if (!@getimagesize($tmp)) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }
    $dir = UPLOAD_PATH . '/' . trim($folder, '/');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $target = $dir . '/' . $name;
    if (!move_uploaded_file($tmp, $target)) {
        throw new RuntimeException('Could not save upload.');
    }
    return trim(UPLOAD_URL, '/') . '/' . trim($folder, '/') . '/' . $name;
}

function safe_content(string $html): string
{
    return strip_tags($html, '<p><br><strong><b><em><i><ul><ol><li><h2><h3>');
}
