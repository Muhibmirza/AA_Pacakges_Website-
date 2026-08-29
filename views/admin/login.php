<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - AA Packages</title>
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="admin-login-body">
    <form class="admin-login-card" method="post">
        <?= csrf_field() ?>
        <img src="<?= uploaded($settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png') ?>" alt="AA Packages">
        <h1>Admin login</h1>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button class="btn btn-primary full" type="submit">Login</button>
    </form>
</body>
</html>

