<section class="category-hero reveal category-3d" data-category-banner>
    <div class="category-hero-copy">
        <span class="eyebrow">AA Packages Category</span>
        <h1><?= e($category['name']) ?></h1>
        <p><?= e($category['tagline'] ?? 'Premium packaging engineered for daily retail use and wholesale supply.') ?></p>
        <a class="btn btn-primary" href="<?= e($category['cta_link'] ?? '#products') ?>"><?= e($category['cta_text'] ?? 'Shop This Category') ?></a>
    </div>
    <div class="category-hero-stage">
        <div class="float-shadow"></div>
        <img data-parallax-card src="<?= uploaded($category['banner_image'] ?: $category['image']) ?>" alt="<?= e($category['name']) ?>">
    </div>
</section>

