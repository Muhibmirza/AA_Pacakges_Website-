<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $isAdminRequest = preg_match('#/admin(?:/|$)#', $requestPath) === 1;
    session_name($isAdminRequest ? 'aa_packages_admin_session' : 'aa_packages_session');
    $secureCookie = filter_var(getenv('AA_SESSION_SECURE') ?: (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), FILTER_VALIDATE_BOOL);
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => rtrim(BASE_URL, '/') . '/',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_ajax_request(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

function apply_cors(): void
{
    $origin = trim($_SERVER['HTTP_ORIGIN'] ?? '');
    if ($origin !== '' && in_array($origin, ALLOWED_ORIGINS, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(): void
{
    $posted = $_POST['csrf_token'] ?? '';
    if (!$posted || !hash_equals($_SESSION['csrf_token'] ?? '', $posted)) {
        http_response_code(419);
        exit('Security token expired. Please go back and try again.');
    }
}

function throttle_key(string $scope): string
{
    return 'attempts_' . $scope . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'local');
}

function too_many_attempts(string $scope, int $limit = 5, int $window = 900): bool
{
    $key = throttle_key($scope);
    $attempts = array_filter($_SESSION[$key] ?? [], fn ($time) => $time > time() - $window);
    $_SESSION[$key] = $attempts;
    return count($attempts) >= $limit;
}

function record_attempt(string $scope): void
{
    $key = throttle_key($scope);
    $_SESSION[$key][] = time();
}

function clear_attempts(string $scope): void
{
    unset($_SESSION[throttle_key($scope)]);
}

function enforce_rate_limit(string $type, string $account = ''): void
{
    $config = RATE_LIMITS[$type] ?? RATE_LIMITS['public'];
    if (!is_dir(RATE_LIMIT_PATH)) {
        mkdir(RATE_LIMIT_PATH, 0755, true);
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'local';
    $key = hash('sha256', $type . '|' . $ip . '|' . strtolower($account));
    $file = RATE_LIMIT_PATH . '/' . $key . '.json';
    $state = ['hits' => [], 'failures' => 0, 'blocked_until' => 0];
    if (is_file($file)) {
        $loaded = json_decode((string)file_get_contents($file), true);
        if (is_array($loaded)) {
            $state = array_merge($state, $loaded);
        }
    }
    $now = time();
    if (($state['blocked_until'] ?? 0) > $now) {
        http_response_code(429);
        exit('Too many requests. Please wait and try again.');
    }
    $state['hits'] = array_values(array_filter($state['hits'], function ($hit) use ($now, $config) {
        return $hit > $now - (int)$config['window'];
    }));
    if (count($state['hits']) >= (int)$config['limit']) {
        $state['failures'] = (int)$state['failures'] + 1;
        $delay = min((int)$config['max_backoff'], (int)$config['backoff_base'] * (2 ** max(0, $state['failures'] - 1)));
        $state['blocked_until'] = $now + $delay;
        file_put_contents($file, json_encode($state), LOCK_EX);
        http_response_code(429);
        exit('Too many requests. Please wait and try again.');
    }
    $state['hits'][] = $now;
    file_put_contents($file, json_encode($state), LOCK_EX);
}

function clear_rate_limit(string $type, string $account = ''): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'local';
    $file = RATE_LIMIT_PATH . '/' . hash('sha256', $type . '|' . $ip . '|' . strtolower($account)) . '.json';
    if (is_file($file)) {
        unlink($file);
    }
}

function admin_guard(): void
{
    if (empty($_SESSION['admin_id'])) {
        if (is_ajax_request()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Your admin session has expired. Please sign in again.', 'login_url' => url('admin')]);
            exit;
        }
        header('Location: ' . url('admin'));
        exit;
    }
    if (!empty($_SESSION['admin_last_seen']) && time() - $_SESSION['admin_last_seen'] > SESSION_TIMEOUT) {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_last_seen']);
        if (is_ajax_request()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Your admin session has expired. Please sign in again.', 'login_url' => url('admin?timeout=1')]);
            exit;
        }
        header('Location: ' . url('admin?timeout=1'));
        exit;
    }
    $_SESSION['admin_last_seen'] = time();
}

function customer_guard(): void
{
    if (empty($_SESSION['user_id'])) {
        if (is_ajax_request()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Please sign in to continue.', 'login_url' => route('login')]);
            exit;
        }
        redirect(route('login'));
    }
}
