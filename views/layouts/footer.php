<?php $siteLogo = $settings['site_logo'] ?? 'public/assets/images/logo/aa-packages-logo.png'; ?>
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <img class="footer-logo" src="<?= uploaded($siteLogo) ?>" alt="AA Packages logo">
            <p><?= e($settings['footer_about'] ?? 'AA Packages supplies reliable, print-ready packaging for bakeries, sweet shops, restaurants, perfume brands, retailers, and wholesale buyers.') ?></p>
        </div>
        <div>
            <h3>Quick Links</h3>
            <a href="<?= route('shop') ?>">Shop</a>
            <a href="<?= route('quote') ?>">Bulk Orders</a>
            <a href="<?= route('track') ?>">Order Tracking</a>
            <a href="<?= route('returns') ?>">Return Policy</a>
        </div>
        <div>
            <h3>Company</h3>
            <a href="<?= route('about') ?>">About Us</a>
            <a href="<?= route('contact') ?>">Contact</a>
            <a href="<?= route('faqs') ?>">FAQs</a>
            <a href="<?= route('privacy') ?>">Privacy Policy</a>
        </div>
        <div>
            <h3>Contact</h3>
            <p><strong>Office.</strong> <?= e($settings['contact_address'] ?? 'B 307 Sector 11-E, Karachi, Pakistan') ?></p>
            <p><strong>Postal Code.</strong> <?= e($settings['postal_code'] ?? '75850') ?></p>
            <p><strong>Call us.</strong> <?= e($settings['contact_phone'] ?? '+92 310 8504003') ?></p>
            <p><strong>Email.</strong> <?= e($settings['contact_email'] ?? 'contact@aapackageskarachi.com') ?></p>
            <p><?= e($settings['business_hours'] ?? 'Mon-Sat, 10:00 AM - 7:00 PM') ?></p>
            <div class="payment-icons" aria-label="Accepted payment methods">
                <img src="<?= asset('assets/images/payments/cod.svg') ?>" alt="Cash on Delivery">
                <img src="<?= asset('assets/images/payments/visa.svg') ?>" alt="Visa">
                <img src="<?= asset('assets/images/payments/mastercard.svg') ?>" alt="Mastercard">
                <img src="<?= asset('assets/images/payments/debit-card.svg') ?>" alt="Debit Card">
                <img src="<?= asset('assets/images/payments/jazzcash.svg') ?>" alt="JazzCash">
                <img src="<?= asset('assets/images/payments/easypaisa.svg') ?>" alt="Easypaisa">
                <img src="<?= asset('assets/images/payments/bank-transfer.svg') ?>" alt="Bank Transfer">
            </div>
        </div>
    </div>
    <div class="container copyright">&copy; <?= date('Y') ?> AA Packages. All rights reserved.</div>
</footer>

<div class="cart-overlay" data-cart-close></div>
<aside class="mini-cart" aria-label="Shopping cart" data-mini-cart>
    <div class="mini-cart-head">
        <h2>Your Cart</h2>
        <button type="button" class="icon-btn" data-cart-close aria-label="Close cart">&times;</button>
    </div>
    <div class="mini-cart-body" data-cart-items></div>
    <div class="mini-cart-foot">
        <div class="summary-row"><span>Subtotal</span><strong data-cart-subtotal><?= money(0) ?></strong></div>
        <div class="summary-actions">
            <a class="btn btn-outline" href="<?= route('cart') ?>">View Full Cart</a>
            <a class="btn btn-primary" href="<?= route('checkout') ?>">Checkout</a>
        </div>
    </div>
</aside>

<script>
window.AA_PACKAGES = {
    baseUrl: "<?= url('') ?>",
    csrf: "<?= e(csrf_token()) ?>"
};
</script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
