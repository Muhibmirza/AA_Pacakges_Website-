<div class="auth-shell">
    <form class="auth-card reveal" method="post">
        <?= csrf_field() ?>
        <img src="<?= uploaded($settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png') ?>" alt="AA Packages">
        <h1>Customer login</h1>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
        <label>Email or phone<input name="login" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button class="btn btn-primary full" type="submit">Login</button>
        <p><a href="<?= route('register') ?>">Create an account</a> | <a href="#">Forgot password?</a></p>
    </form>
</div>
