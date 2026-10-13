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

<div class="floating-actions" aria-label="Quick contact and offers">
    <a class="floating-action floating-action--whatsapp" href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= rawurlencode('Hello, I want to know about your packaging products.') ?>" target="_blank" rel="noopener" aria-label="Chat with AA Packages on WhatsApp" title="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.69.25-1.28.17-1.41-.07-.12-.27-.2-.57-.35M12.04 21.5h-.01a9.45 9.45 0 0 1-4.82-1.32l-.35-.21-3.58.94.96-3.49-.23-.36A9.45 9.45 0 1 1 12.04 21.5m8.04-17.52A11.3 11.3 0 0 0 12.05.65 11.28 11.28 0 0 0 2.3 17.6L.7 23.35l5.9-1.55a11.28 11.28 0 0 0 16.68-9.9c0-3.02-1.18-5.86-3.2-7.92"/></svg>
    </a>
    <button class="floating-action floating-action--discount" type="button" data-discount-reopen hidden aria-label="Open 5% discount offer" title="Get 5% OFF"><span aria-hidden="true">5%</span></button>
</div>

<div class="discount-modal" data-discount-modal hidden>
    <div class="discount-modal__panel" role="dialog" aria-modal="true" aria-labelledby="discount-modal-title">
        <button class="discount-modal__close" type="button" aria-label="Close discount offer" data-discount-close>&times;</button>
        <div class="discount-modal__accent"><span>5%</span><small>OFF</small></div>
        <div class="discount-modal__content">
            <span class="eyebrow">Welcome to AA Packages</span>
            <h2 id="discount-modal-title">Get 5% OFF your first order!</h2>
            <p>Leave your details and we will reserve your first-order discount.</p>
            <form data-discount-form novalidate>
                <?= csrf_field() ?>
                <label>Name <small>(optional)</small><input name="name" autocomplete="name" maxlength="160"></label>
                <label>Email<input type="email" name="email" autocomplete="email" maxlength="190" required></label>
                <label>Contact / Phone number<input type="tel" name="phone" autocomplete="tel" maxlength="40" pattern="[+0-9\s-]{7,20}" required></label>
                <div class="form-status" data-discount-status aria-live="polite"></div>
                <button class="btn btn-primary" type="submit">Get My 5% Discount</button>
            </form>
            <div class="discount-success" data-discount-success hidden aria-live="polite">
                <span>Discount confirmed</span><h3>Thank you!</h3><p data-discount-success-message>Use code <strong>WELCOME5</strong> for 5% off your first order.</p>
            </div>
        </div>
    </div>
</div>

<script>
window.AA_PACKAGES = {
    baseUrl: "<?= url('') ?>",
    apiBaseUrl: "<?= e(API_BASE_URL) ?>/",
    csrf: "<?= e(csrf_token()) ?>"
};
</script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
