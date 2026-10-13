<?php
require_once ROOT_PATH . '/models/Category.php';
$navCategories = (new Category($pdo))->all();
$siteLogo = $settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png';
$cartCount = array_sum(array_map(fn ($item) => (int)$item['qty'], $_SESSION['cart'] ?? []));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($settings['site_name'] ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>?v=<?= (int) filemtime(ROOT_PATH . '/public/assets/css/style.css') ?>">
</head>
<body style="--color-primary: <?= e($settings['theme_primary'] ?? '#1565C0') ?>; --color-primary-light: <?= e($settings['theme_primary_light'] ?? '#4FC3F7') ?>; --color-secondary: <?= e($settings['theme_secondary'] ?? '#0D47A1') ?>;">
<div class="announcement">Bulk & Wholesale Orders Welcome - Custom Printing Available</div>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= route() ?>" aria-label="AA Packages home">
            <img src="<?= uploaded($siteLogo) ?>" alt="AA Packages logo">
        </a>
        <nav class="main-nav" id="site-navigation" data-nav aria-label="Main navigation">
            <div class="mobile-drawer-head">
                <strong>Menu</strong>
                <button class="nav-close" type="button" aria-label="Close menu" data-nav-close>&times;</button>
            </div>
            <a href="<?= route() ?>">Home</a>
            <a href="<?= route('shop') ?>">Shop</a>
            <a href="<?= route('quote') ?>">Bulk Orders</a>
            <a href="<?= route('about') ?>">About</a>
            <a href="<?= route('contact') ?>">Contact</a>
            <a class="mobile-account-link" href="<?= route(!empty($_SESSION['user_id']) ? 'account' : 'login') ?>"><?= !empty($_SESSION['user_id']) ? 'My Account' : 'Login / Account' ?></a>
        </nav>
        <form class="search-form" action="<?= route('shop') ?>" method="get">
            <input type="search" name="q" placeholder="Search boxes, size, material" value="<?= e($_GET['q'] ?? '') ?>">
        </form>
        <div class="header-actions">
            <a class="icon-link" href="<?= route(!empty($_SESSION['user_id']) ? 'account' : 'login') ?>" aria-label="<?= !empty($_SESSION['user_id']) ? 'My Account' : 'Account' ?>"><?= !empty($_SESSION['user_id']) ? 'My Account' : 'Account' ?></a>
            <button class="cart-trigger" type="button" data-cart-open aria-label="Open cart">
                Cart <span class="cart-badge" data-cart-count><?= $cartCount ?></span>
            </button>
        </div>
        <button class="nav-toggle" type="button" aria-label="Open menu" aria-controls="site-navigation" aria-expanded="false" data-nav-toggle><span></span><span></span><span></span></button>
    </div>
    <button class="nav-overlay" type="button" aria-label="Close menu" data-nav-close></button>
</header>
<?php if (current_page() !== 'home'): ?>
<div class="mobile-category-bar">
    <?php foreach ($navCategories as $cat): ?>
        <a href="<?= route('category', ['slug' => $cat['slug']]) ?>"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<main>
