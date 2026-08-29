<?php
declare(strict_types=1);

define('APP_NAME', 'AA Packages');
$baseUrl = getenv('AA_BASE_URL');
define('BASE_URL', $baseUrl === false ? '/aapackages' : rtrim($baseUrl, '/'));
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('SESSION_TIMEOUT', 1800);
define('LOG_PATH', ROOT_PATH . '/storage/logs');
define('RATE_LIMIT_PATH', ROOT_PATH . '/storage/rate_limits');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}
ini_set('error_log', LOG_PATH . '/app.log');

set_exception_handler(function (Throwable $e): void {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo 'Something went wrong. Please try again later.';
});

define('RATE_LIMITS', [
    'auth' => ['limit' => 5, 'window' => 900, 'backoff_base' => 30, 'max_backoff' => 900],
    'public' => ['limit' => 60, 'window' => 300, 'backoff_base' => 5, 'max_backoff' => 120],
    'user' => ['limit' => 120, 'window' => 300, 'backoff_base' => 2, 'max_backoff' => 60],
]);

date_default_timezone_set('Asia/Karachi');
