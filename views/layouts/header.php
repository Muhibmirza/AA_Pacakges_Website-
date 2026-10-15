<?php
require_once ROOT_PATH . '/models/Category.php';
$navCategories = (new Category($pdo))->all();
$siteLogo = $settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png';
$cartCount = array_sum(array_map(fn ($item) => (int)$item['qty'], $_SESSION['cart'] ?? []));
$routeName = current_page();
$seoTitles = [
    'home' => 'Packaging Boxes in Karachi | AA Packages',
    'shop' => 'Shop Packaging Boxes | AA Packages',
    'quote' => 'Custom Box & Bulk Order Quote | AA Packages',
    'about' => 'About AA Packages | Packaging Supplier Karachi',
    'contact' => 'Contact AA Packages Karachi',
    'faqs' => 'Packaging FAQs | AA Packages',
    'terms' => 'Terms & Conditions | AA Packages',
    'privacy' => 'Privacy Policy | AA Packages',
    'returns' => 'Return & Refund Policy | AA Packages',
    'track' => 'Track Your Order | AA Packages',
];
$seoDescriptions = [
    'home' => 'Shop cake, mithai, pizza, gift, perfume and custom printed packaging boxes from AA Packages in Karachi. Retail and wholesale orders available.',
    'shop' => 'Browse reliable packaging boxes for bakeries, sweet shops, restaurants, retailers and brands. Retail, wholesale and custom sizes available.',
    'quote' => 'Request a quote for custom-sized, bulk or branded packaging boxes from AA Packages Karachi.',
    'about' => 'Learn about AA Packages, a Karachi supplier of practical retail, wholesale and custom printed packaging boxes.',
    'contact' => 'Contact AA Packages at B 307 Sector 11-E, Karachi for packaging products, wholesale pricing and custom printing.',
];
$seoTitle = $seoTitles[$routeName] ?? (($settings['site_name'] ?? APP_NAME) . ' | Packaging Solutions');
$seoDescription = $seoDescriptions[$routeName] ?? 'AA Packages supplies dependable packaging boxes for retail, wholesale and custom printed orders in Karachi, Pakistan.';
$seoImage = absolute_url(uploaded($siteLogo));
$seoType = 'website';
if (!empty($category)) {
    $seoTitle = $category['name'] . ' in Karachi | AA Packages';
    $seoDescription = $category['description'];
    $seoImage = absolute_url(uploaded($category['banner_image'] ?: $category['image']));
}
if (!empty($product)) {
    $seoTitle = $product['name'] . ' | AA Packages';
    $seoDescription = $product['short_description'];
    $seoImage = absolute_url(uploaded($product['main_image']));
    $seoType = 'product';
}
$noIndexRoutes = ['cart', 'checkout', 'confirmation', 'login', 'register', 'logout', 'account', 'cart-add', 'cart-update', 'cart-drawer', 'cart-coupon', 'discount-lead', 'contact-submit'];
$robotsContent = in_array($routeName, $noIndexRoutes, true) ? 'noindex, nofollow, noarchive' : 'index, follow, max-image-preview:large';
$organizationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $settings['site_name'] ?? APP_NAME,
    'url' => absolute_url(url('')),
    'logo' => absolute_url(uploaded($siteLogo)),
    'email' => $settings['contact_email'] ?? 'contact@aapackageskarachi.com',
    'telephone' => $settings['contact_phone'] ?? '+92 310 8504003',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $settings['contact_address'] ?? 'B 307 Sector 11-E',
        'addressLocality' => 'Karachi',
        'postalCode' => $settings['postal_code'] ?? '75850',
        'addressCountry' => 'PK',
    ],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($seoTitle) ?></title>
    <meta name="description" content="<?= e($seoDescription) ?>">
    <meta name="robots" content="<?= e($robotsContent) ?>">
    <meta name="theme-color" content="<?= e($settings['theme_primary'] ?? '#1565C0') ?>">
    <link rel="canonical" href="<?= e(canonical_url()) ?>">
    <meta property="og:site_name" content="<?= e($settings['site_name'] ?? APP_NAME) ?>">
    <meta property="og:type" content="<?= e($seoType) ?>">
    <meta property="og:title" content="<?= e($seoTitle) ?>">
    <meta property="og:description" content="<?= e($seoDescription) ?>">
    <meta property="og:url" content="<?= e(canonical_url()) ?>">
    <meta property="og:image" content="<?= e($seoImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json"><?= json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php if (!empty($product)): ?>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['name'],
        'description' => $product['short_description'],
        'image' => [$seoImage],
        'brand' => ['@type' => 'Brand', 'name' => 'AA Packages'],
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => 'PKR',
            'price' => (string)$product['base_price'],
            'availability' => (int)$product['stock_quantity'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => canonical_url(),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php endif; ?>
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
