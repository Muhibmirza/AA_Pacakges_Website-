<section class="about-hero page-hero">
    <div class="container page-hero__inner reveal">
        <span class="eyebrow">Built for everyday business</span>
        <h1><?= e($page['title'] ?? 'Packaging that protects your product and presents your brand well.') ?></h1>
        <p>AA Packages supplies dependable bakery, food, gift, retail, and custom-printed packaging to businesses and individual buyers across Karachi and beyond.</p>
        <a class="btn btn-primary" href="<?= route('quote') ?>">Request a Custom Quote</a>
    </div>
</section>

<section class="content-band">
    <div class="container story-grid reveal">
        <div>
            <span class="eyebrow">Our story</span>
            <h2>Practical packaging, supplied with care.</h2>
        </div>
        <div class="managed-page-copy"><?= nl2br(e(page_text($page['content'] ?? 'AA Packages began with a simple focus: help businesses find packaging that is sturdy, presentable, and sensibly priced.'))) ?></div>
    </div>
</section>

<section class="content-band content-band--tint">
    <div class="container">
        <div class="section-heading reveal"><span class="eyebrow">Why choose us</span><h2>A reliable packaging partner</h2></div>
        <div class="value-grid reveal">
            <article><span>01</span><h3>Quality Materials</h3><p>Food-safe, rigid, folding, and corrugated board options selected for dependable daily use.</p></article>
            <article><span>02</span><h3>Custom Sizes</h3><p>Packaging dimensions and structures tailored to cakes, sweets, gifts, food, and retail products.</p></article>
            <article><span>03</span><h3>Affordable Pricing</h3><p>Clear retail and wholesale options with practical quantity tiers for growing businesses.</p></article>
            <article><span>04</span><h3>Fast Delivery</h3><p>Responsive quoting, careful order coordination, and reliable turnaround for repeat supply.</p></article>
        </div>
    </div>
</section>

<section class="content-band">
    <div class="container about-stats reveal">
        <article><strong><?= (int)$stats['products'] ?>+</strong><span>Packaging products</span></article>
        <article><strong><?= (int)$stats['categories'] ?></strong><span>Product categories</span></article>
        <article><strong>COD</strong><span>Pay on delivery</span></article>
        <article><strong>1</strong><span>Standard: dependable quality</span></article>
    </div>
</section>
