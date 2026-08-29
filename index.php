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

$settings = (new Setting($pdo))->all();
$page = current_page();

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
    case 'contact':
    case 'faqs':
    case 'terms':
    case 'privacy':
    case 'returns':
        (new FrontendController($pdo, $settings))->staticPage($page);
        break;
    default:
        (new FrontendController($pdo, $settings))->home();
}
