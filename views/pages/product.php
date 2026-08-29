<div class="container product-detail page-wrap">
    <section class="product-gallery reveal">
        <img class="main-product-image" src="<?= uploaded($product['main_image']) ?>" alt="<?= e($product['name']) ?>" data-main-product-image>
        <div class="thumb-grid">
            <button type="button"><img src="<?= uploaded($product['main_image']) ?>" alt=""></button>
            <?php foreach ($product['images'] as $img): ?>
                <button type="button" data-thumb="<?= uploaded($img['image_path']) ?>"><img src="<?= uploaded($img['image_path']) ?>" alt=""></button>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="product-info reveal">
        <span class="eyebrow"><?= e($product['category_name']) ?></span>
        <h1><?= e($product['name']) ?></h1>
        <p class="lead"><?= e($product['short_description']) ?></p>
        <div class="price-line"><?= money((float)$product['base_price']) ?> <span>per pack</span></div>
        <label>Size / material
            <select>
                <option><?= e($product['default_variant']) ?></option>
                <?php foreach ($product['variants'] as $variant): ?>
                    <option><?= e($variant['size_label'] . ' - ' . $variant['material'] . ' - ' . money((float)$variant['price'])) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php if ($product['price_tiers']): ?>
            <div class="tier-box">
                <h3>Quantity-tier pricing</h3>
                <?php foreach ($product['price_tiers'] as $tier): ?>
                    <div><span><?= (int)$tier['min_quantity'] ?>+ packs</span><strong><?= money((float)$tier['unit_price']) ?></strong></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form class="add-to-cart detail-actions" data-add-to-cart>
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <div class="qty-stepper">
                <button type="button" data-qty-minus>-</button>
                <input type="number" name="qty" value="1" min="1">
                <button type="button" data-qty-plus>+</button>
            </div>
            <button class="btn btn-primary" type="submit">Add to Cart</button>
            <a class="btn btn-outline" href="<?= route('quote') ?>">Request Bulk Quote</a>
        </form>
    </section>
</div>
<section class="section">
    <div class="container tabs reveal">
        <button class="active" type="button">Description & Specifications</button>
        <button type="button">Custom Printing Options</button>
        <button type="button">Reviews</button>
        <div class="tab-panel">
            <p><?= nl2br(e($product['description'])) ?></p>
        </div>
    </div>
</section>
<section class="section section-tint">
    <div class="container section-head"><h2>Related products</h2></div>
    <div class="container product-grid">
        <?php foreach ($related as $product) require ROOT_PATH . '/views/layouts/product-card.php'; ?>
    </div>
</section>
