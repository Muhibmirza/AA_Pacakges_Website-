<section class="contact-hero page-hero">
    <div class="container page-hero__inner reveal">
        <span class="eyebrow">Talk to our packaging team</span>
        <h1><?= e($page['title'] ?? 'Tell us what you need. We will help you find the right box.') ?></h1>
        <p><?= e(page_text($page['content'] ?? 'Share your product, size, and quantity. Our team will respond with suitable packaging options and pricing.')) ?></p>
    </div>
</section>

<section class="content-band contact-section">
    <div class="container contact-layout">
        <div class="contact-details reveal">
            <span class="eyebrow">Contact details</span>
            <h2>AA Packages Karachi</h2>
            <div class="contact-detail-list">
                <article><span>Phone</span><a href="tel:<?= e(preg_replace('/\s+/', '', $settings['contact_phone'] ?? '+923108504003')) ?>"><?= e($settings['contact_phone'] ?? '+92 310 8504003') ?></a></article>
                <article><span>Email</span><a href="mailto:<?= e($settings['contact_email'] ?? 'contact@aapackageskarachi.com') ?>"><?= e($settings['contact_email'] ?? 'contact@aapackageskarachi.com') ?></a></article>
                <article><span>Office</span><p><?= e($settings['contact_address'] ?? 'B 307 Sector 11-E, Karachi, Pakistan') ?>, <?= e($settings['postal_code'] ?? '75850') ?></p></article>
                <article><span>Hours</span><p><?= e($settings['business_hours'] ?? 'Mon-Sat, 10:00 AM - 7:00 PM') ?></p></article>
            </div>
            <a class="btn btn-outline" target="_blank" rel="noopener" href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= rawurlencode('Hello, I want to know about your packaging products.') ?>">Chat on WhatsApp</a>
        </div>

        <form class="contact-form reveal" data-contact-form novalidate>
            <?= csrf_field() ?>
            <div class="section-heading section-heading--compact"><span class="eyebrow">Send a message</span><h2>How can we help?</h2></div>
            <div class="field-row">
                <label>Name<input name="name" autocomplete="name" maxlength="160" required></label>
                <label>Email<input type="email" name="email" autocomplete="email" maxlength="190" required></label>
            </div>
            <label>Phone<input type="tel" name="phone" autocomplete="tel" maxlength="40" pattern="[+0-9\s-]{7,20}" required></label>
            <label>Message<textarea name="message" rows="6" minlength="10" maxlength="3000" required placeholder="Tell us the box type, size, and quantity you need."></textarea></label>
            <div class="form-status" data-contact-status aria-live="polite"></div>
            <button class="btn btn-primary" type="submit">Send Message</button>
        </form>
    </div>
</section>

<section class="map-band">
    <div class="container">
        <div class="map-placeholder reveal" aria-label="Google Maps location placeholder">
            <div><span class="eyebrow">Visit our office</span><h2>Sector 11-E, Karachi</h2><p><?= e($settings['contact_address'] ?? 'B 307 Sector 11-E, Karachi, Pakistan') ?>, <?= e($settings['postal_code'] ?? '75850') ?></p><a class="btn btn-outline" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode(($settings['contact_address'] ?? 'B 307 Sector 11-E, Karachi, Pakistan') . ' ' . ($settings['postal_code'] ?? '75850')) ?>">Open in Google Maps</a></div>
        </div>
    </div>
</section>
