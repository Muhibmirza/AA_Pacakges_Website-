<article class="product-card reveal">
    <a class="product-media" href="<?= route('product', ['slug' => $product['slug']]) ?>">
        <img src="<?= uploaded($product['main_image']) ?>" alt="<?= e($product['name']) ?>">
    </a>
    <div class="product-body">
        <span class="eyebrow"><?= e($product['category_name'] ?? '') ?></span>
        <h3><a href="<?= route('product', ['slug' => $product['slug']]) ?>"><?= e($product['name']) ?></a></h3>
        <p><?= e($product['short_description']) ?></p>
        <div class="product-meta">
            <span><?= e($product['default_variant']) ?></span>
            <strong><?= money((float)$product['base_price']) ?></strong>
        </div>
        <form class="add-to-cart product-actions" data-add-to-cart>
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <div class="qty-stepper">
                <button type="button" data-qty-minus>-</button>
                <input type="number" name="qty" value="1" min="1" max="999">
                <button type="button" data-qty-plus>+</button>
            </div>
            <button class="btn btn-primary" type="submit">Add to Cart</button>
        </form>
    </div>
</article>
