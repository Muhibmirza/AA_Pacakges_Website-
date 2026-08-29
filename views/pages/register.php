<div class="auth-shell">
    <form class="auth-card reveal" method="post">
        <?= csrf_field() ?>
        <img src="<?= uploaded($settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png') ?>" alt="AA Packages">
        <h1>Create account</h1>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
        <label>Name<input name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Phone<input name="phone" required></label>
        <label>Address<textarea name="address"></textarea></label>
        <label>Password<input type="password" name="password" required minlength="8"></label>
        <button class="btn btn-primary full" type="submit">Sign Up</button>
        <p><a href="<?= route('login') ?>">Already have an account?</a></p>
    </form>
</div>
