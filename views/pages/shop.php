<div class="container page-wrap">
    <?php if ($category): ?>
        <?php require ROOT_PATH . '/views/layouts/category-banner.php'; ?>
    <?php else: ?>
        <section class="page-title reveal">
            <span class="eyebrow">All boxes</span>
            <h1>Shop packaging boxes</h1>
            <p>Filter by category, material, size, pack quantity, and budget.</p>
        </section>
    <?php endif; ?>

    <div class="shop-layout" id="products">
        <aside class="filter-panel reveal">
            <h2>Filters</h2>
            <a class="<?= empty($_GET['category']) ? 'active' : '' ?>" href="<?= route('shop') ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
                <a class="<?= ($_GET['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>" href="<?= route('category', ['slug' => $cat['slug']]) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
            <label>Board Type<select><option>Food Grade Board</option><option>Corrugated</option><option>Rigid Board</option></select></label>
            <label>Pack Quantity<select><option>25 pcs</option><option>50 pcs</option><option>100 pcs</option><option>500+ pcs</option></select></label>
        </aside>
        <div>
            <form class="shop-toolbar reveal" method="get">
                <?php if (!empty($_GET['category'])): ?><input type="hidden" name="category" value="<?= e($_GET['category']) ?>"><?php endif; ?>
                <input type="search" name="q" placeholder="Search this shop" value="<?= e($_GET['q'] ?? '') ?>">
                <select name="sort" onchange="this.form.submit()">
                    <option value="">Popularity</option>
                    <option value="price-low" <?= $sort === 'price-low' ? 'selected' : '' ?>>Price Low-High</option>
                    <option value="price-high" <?= $sort === 'price-high' ? 'selected' : '' ?>>Price High-Low</option>
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                </select>
                <button class="btn btn-outline" type="submit">Apply</button>
            </form>
            <div class="product-grid">
                <?php foreach ($products as $product) require ROOT_PATH . '/views/layouts/product-card.php'; ?>
            </div>
            <div class="pagination"><a class="active" href="#">1</a><a href="#">2</a><a href="#">Next</a></div>
        </div>
    </div>
</div>
