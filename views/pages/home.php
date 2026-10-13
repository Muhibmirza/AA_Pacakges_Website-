<section class="hero-slider" data-hero-slider>
    <?php foreach ($banners as $i => $banner): ?>
        <article class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" style="background-image: linear-gradient(90deg, rgba(7,39,84,.80), rgba(21,101,192,.20)), url('<?= uploaded($banner['image_path']) ?>')">
            <div class="container hero-content reveal">
                <span class="eyebrow"><?= e($banner['kicker'] ?? 'Wholesale Packaging') ?></span>
                <h1><?= e($banner['heading']) ?></h1>
                <p><?= e($banner['subtext']) ?></p>
                <a class="btn btn-primary" href="<?= e(content_url($banner['button_link'])) ?>"><?= e($banner['button_text']) ?></a>
            </div>
        </article>
    <?php endforeach; ?>
    <button class="slider-arrow prev" type="button" data-slide-prev aria-label="Previous slide">‹</button>
    <button class="slider-arrow next" type="button" data-slide-next aria-label="Next slide">›</button>
    <div class="slider-dots" data-slider-dots></div>
</section>

<section class="section">
    <div class="container section-head">
        <span class="eyebrow">Shop by box type</span>
        <h2>Packaging categories built for retail and wholesale buyers</h2>
    </div>
    <div class="container category-grid">
        <?php foreach ($categories as $cat): ?>
            <a class="category-card reveal" href="<?= route('category', ['slug' => $cat['slug']]) ?>">
                <img src="<?= uploaded($cat['image']) ?>" alt="<?= e($cat['name']) ?>">
                <div>
                    <h3><?= e($cat['name']) ?></h3>
                    <p><?= e($cat['description']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-tint">
    <div class="container section-head inline-head">
        <div>
            <span class="eyebrow">Best sellers</span>
            <h2>Fast-moving boxes for everyday packaging needs</h2>
        </div>
        <a class="btn btn-outline" href="<?= route('shop') ?>">View All</a>
    </div>
    <div class="container product-grid" id="products">
        <?php foreach ($products as $product) require ROOT_PATH . '/views/layouts/product-card.php'; ?>
    </div>
</section>

<section class="usp-strip">
    <div class="container usp-grid">
        <?php foreach ($uspBlocks as $usp): ?>
            <article class="usp-item reveal">
                <span><?= e($usp['icon_label']) ?></span>
                <h3><?= e($usp['title']) ?></h3>
                <p><?= e($usp['description']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="bulk-banner reveal">
    <div class="container bulk-inner">
        <div>
            <span class="eyebrow">Custom printing</span>
            <h2>Branded boxes for bakeries, restaurants, retailers, and product launches</h2>
            <p>Share your size, quantity, board type, and logo. We will prepare a practical quote for custom printed packaging.</p>
        </div>
        <a class="btn btn-primary" href="<?= route('quote') ?>">Request a Quote</a>
    </div>
</section>

<section class="section">
    <div class="container section-head">
        <span class="eyebrow">Trusted by trade buyers</span>
        <h2>Packaging that holds up in daily business use</h2>
    </div>
    <div class="container testimonial-grid">
        <?php foreach ($testimonials as $item): ?>
            <article class="testimonial-card reveal">
                <div class="stars"><?= str_repeat('★', (int)$item['rating']) ?></div>
                <p>“<?= e($item['text']) ?>”</p>
                <strong><?= e($item['name']) ?></strong>
                <span><?= e($item['company_name']) ?></span>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="newsletter section-tint">
    <div class="container newsletter-inner reveal">
        <div>
            <span class="eyebrow">Trade updates</span>
            <h2>Get pricing updates and new packaging releases</h2>
        </div>
        <form>
            <input type="email" placeholder="Your email address" required>
            <button class="btn btn-primary" type="submit">Subscribe</button>
        </form>
    </div>
</section>
