<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/models/Setting.php';
require_once __DIR__ . '/controllers/FrontendController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CheckoutController.php';
require_once __DIR__ . '/controllers/QuoteController.php';
require_once __DIR__ . '/controllers/MarketingController.php';

$settings = (new Setting($pdo))->all();
$page = current_page();

if (in_array($page, ['discount-lead', 'contact-submit'], true)) {
    apply_cors();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

switch ($page) {
    case 'shop':
        (new FrontendController($pdo, $settings))->shop();
        break;
    case 'product':
        (new FrontendController($pdo, $settings))->product();
        break;
    case 'cart':
        (new CartController($pdo, $settings))->page();
        break;
    case 'cart-add':
        (new CartController($pdo, $settings))->add();
        break;
    case 'cart-update':
        (new CartController($pdo, $settings))->update();
        break;
    case 'cart-drawer':
        (new CartController($pdo, $settings))->drawer();
        break;
    case 'cart-coupon':
        (new CartController($pdo, $settings))->applyCoupon();
        break;
    case 'checkout':
        (new CheckoutController($pdo, $settings))->checkout();
        break;
    case 'confirmation':
        (new CheckoutController($pdo, $settings))->confirmation();
        break;
    case 'quote':
        (new QuoteController($pdo, $settings))->form();
        break;
    case 'login':
        (new AuthController($pdo, $settings))->login();
        break;
    case 'register':
        (new AuthController($pdo, $settings))->register();
        break;
    case 'logout':
        (new AuthController($pdo, $settings))->logout();
        break;
    case 'account':
        (new AuthController($pdo, $settings))->account();
        break;
    case 'track':
        (new FrontendController($pdo, $settings))->track();
        break;
    case 'about':
        (new FrontendController($pdo, $settings))->about();
        break;
    case 'contact':
        (new FrontendController($pdo, $settings))->contact();
        break;
    case 'discount-lead':
        (new MarketingController($pdo, $settings))->discountLead();
        break;
    case 'contact-submit':
        (new MarketingController($pdo, $settings))->contactMessage();
        break;
    case 'sitemap':
        (new FrontendController($pdo, $settings))->sitemap();
        break;
    case 'robots':
        (new FrontendController($pdo, $settings))->robots();
        break;
    case 'faqs':
    case 'terms':
    case 'privacy':
    case 'returns':
        (new FrontendController($pdo, $settings))->staticPage($page);
        break;
    default:
        (new FrontendController($pdo, $settings))->home();
}
