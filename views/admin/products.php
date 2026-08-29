<?php
$editing = !empty($editProduct);
$formProduct = $editProduct ?: [
    'id' => 0,
    'category_id' => $categories[0]['id'] ?? 0,
    'name' => '',
    'slug' => '',
    'short_description' => '',
    'description' => '',
    'default_variant' => '',
    'base_price' => '',
    'stock_quantity' => '',
    'main_image' => '',
    'is_featured' => 0,
    'is_active' => 1,
];
?>
<section class="admin-card">
    <div class="card-head"><h2>Products</h2><a class="btn btn-primary" href="<?= admin_route('products') ?>#product-form">Add Product</a></div>
    <div class="table-tools"><input placeholder="Search products"><select><option>All categories</option></select><button class="btn btn-outline">Filter</button></div>
    <table class="admin-table">
        <thead><tr><th class="thumb-col">Image</th><th>Name</th><th>Category</th><th class="num">Price</th><th class="num">Stock</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img class="thumb" src="<?= uploaded($product['main_image']) ?>" alt=""></td>
                <td><?= e($product['name']) ?><small><?= e($product['default_variant']) ?></small></td>
                <td><?= e($product['category_name']) ?></td>
                <td class="num"><?= money((float)$product['base_price']) ?></td>
                <td class="num"><?= (int)$product['stock_quantity'] ?></td>
                <td><span class="status-badge <?= $product['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $product['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a class="mini-action" href="<?= admin_route('products') ?>?edit=<?= (int)$product['id'] ?>#product-form">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this product?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_row">
                        <input type="hidden" name="table" value="products">
                        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<form class="admin-card" id="product-form" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editing ? 'update_product' : 'create_product' ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$formProduct['id'] ?>"><?php endif; ?>
    <div class="card-head">
        <h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= admin_route('products') ?>">Cancel Edit</a><?php endif; ?>
    </div>
    <div class="admin-form-grid">
        <fieldset>
            <legend>Basic Info</legend>
            <label>Name<input name="name" value="<?= e($formProduct['name']) ?>" required></label>
            <label>Slug<input name="slug" value="<?= e($formProduct['slug']) ?>" placeholder="auto if blank"></label>
            <label>Short description<textarea name="short_description" required><?= e($formProduct['short_description']) ?></textarea></label>
            <label>Full description<textarea name="description" required><?= e($formProduct['description']) ?></textarea></label>
            <label>Category<select name="category_id"><?php foreach ($categories as $cat): ?><option value="<?= (int)$cat['id'] ?>" <?= (int)$formProduct['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option><?php endforeach; ?></select></label>
        </fieldset>
        <fieldset>
            <legend>Pricing & Stock</legend>
            <label>Default variant<input name="default_variant" value="<?= e($formProduct['default_variant']) ?>" required></label>
            <label>Base price<input name="base_price" type="number" step="0.01" value="<?= e((string)$formProduct['base_price']) ?>" required></label>
            <label>Stock<input name="stock_quantity" type="number" value="<?= e((string)$formProduct['stock_quantity']) ?>" required></label>
            <label class="checkbox-line"><input type="checkbox" name="is_featured" value="1" <?= !empty($formProduct['is_featured']) ? 'checked' : '' ?>> Mark as featured</label>
            <label class="checkbox-line"><input type="checkbox" name="is_active" value="1" <?= !empty($formProduct['is_active']) ? 'checked' : '' ?>> Active</label>
        </fieldset>
        <fieldset>
            <legend>Images</legend>
            <?php if ($editing && $formProduct['main_image']): ?><img class="admin-current-image" src="<?= uploaded($formProduct['main_image']) ?>" alt="Current product image"><?php endif; ?>
            <label>Main image<input name="main_image" type="file" accept=".jpg,.jpeg,.png,.webp" data-image-preview></label>
            <div class="preview-grid upload-preview" data-preview-box></div>
        </fieldset>
        <fieldset>
            <legend>Variants</legend>
            <label>Variant size<input name="variant_size" placeholder="8 x 8 x 3 inch"></label>
            <label>Variant material<input name="variant_material" placeholder="Rigid board"></label>
        </fieldset>
    </div>
    <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Product' : 'Save Product' ?></button>
</form>
