<?php
$titles = [
    'dashboard' => 'Dashboard',
    'products' => 'Products',
    'categories' => 'Categories',
    'orders' => 'Orders',
    'quotes' => 'Quote Requests',
    'leads' => 'Discount Leads',
    'contact-messages' => 'Contact Messages',
    'pages' => 'About & Contact',
    'customers' => 'Customers',
    'coupons' => 'Coupons',
    'reports' => 'Reports',
    'banners' => 'Banners',
    'settings' => 'Settings',
    'admins' => 'Admins',
];
$current = $_GET['page'] ?? 'dashboard';
$siteLogo = $settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title><?= e($titles[$view] ?? 'Admin') ?> - AA Packages</title>
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>?v=<?= (int)filemtime(ROOT_PATH . '/public/assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>?v=<?= (int)filemtime(ROOT_PATH . '/public/assets/css/admin.css') ?>">
</head>
<body class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="<?= admin_route('dashboard') ?>"><img src="<?= uploaded($siteLogo) ?>" alt="AA Packages"></a>
        <nav>
            <?php foreach ($titles as $key => $label): ?>
                <a class="<?= $current === $key || ($key === 'dashboard' && $current === 'login') ? 'active' : '' ?>" href="<?= admin_route($key) ?>"><span><?= substr($label, 0, 1) ?></span><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div><span class="eyebrow">AA Packages Admin</span><h1><?= e($titles[$view] ?? 'Dashboard') ?></h1></div>
            <div class="admin-user"><span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span><button class="btn btn-outline" type="button" data-admin-logout-open>Logout</button></div>
        </header>
        <main class="admin-content">
            <?php if (!empty($_SESSION['admin_flash'])): ?>
                <div class="alert success"><?= e($_SESSION['admin_flash']) ?></div>
                <?php unset($_SESSION['admin_flash']); ?>
            <?php endif; ?>
            <?php require ROOT_PATH . '/views/admin/' . $view . '.php'; ?>
        </main>
    </div>
    <dialog class="admin-confirm-dialog" data-admin-logout-modal>
        <div class="confirm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="admin-logout-title">
            <h2 id="admin-logout-title">Are you sure you want to logout?</h2>
            <p>You will need to sign in again to manage the website.</p>
            <div class="confirm-modal__actions">
                <button class="btn btn-outline" type="button" data-admin-logout-cancel>Cancel</button>
                <form method="post" action="<?= admin_route('logout') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </dialog>
    <script src="<?= asset('assets/js/app.js') ?>?v=<?= (int)filemtime(ROOT_PATH . '/public/assets/js/app.js') ?>"></script>
</body>
</html>
