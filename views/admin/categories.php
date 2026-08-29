<?php
$editing = !empty($editCategory);
$formCategory = $editCategory ?: [
    'id' => 0,
    'name' => '',
    'slug' => '',
    'description' => '',
    'image' => '',
    'sort_order' => 10,
    'is_active' => 1,
    'tagline' => '',
    'cta_text' => 'Shop This Category',
    'cta_link' => '#products',
    'banner_image' => '',
];
?>
<section class="admin-card">
    <div class="card-head"><h2>Categories</h2><a class="btn btn-primary" href="<?= admin_route('categories') ?>#category-form">Add Category</a></div>
    <table class="admin-table">
        <thead><tr><th class="thumb-col">Image</th><th>Name</th><th>Slug</th><th class="num">Order</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><img class="thumb" src="<?= uploaded($cat['image']) ?>" alt=""></td>
                <td><?= e($cat['name']) ?><small><?= e($cat['description']) ?></small></td>
                <td><?= e($cat['slug']) ?></td>
                <td class="num"><?= (int)$cat['sort_order'] ?></td>
                <td><span class="status-badge <?= $cat['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $cat['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a class="mini-action" href="<?= admin_route('categories') ?>?edit=<?= (int)$cat['id'] ?>#category-form">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this category? If it has products, it will be hidden instead.')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_row">
                        <input type="hidden" name="table" value="categories">
                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<form class="admin-card" id="category-form" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editing ? 'update_category' : 'create_category' ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$formCategory['id'] ?>"><?php endif; ?>
    <div class="card-head">
        <h2><?= $editing ? 'Edit Category & 3D Banner' : 'Add Category & 3D Banner' ?></h2>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= admin_route('categories') ?>">Cancel Edit</a><?php endif; ?>
    </div>
    <div class="admin-form-grid">
        <fieldset>
            <legend>Category</legend>
            <label>Name<input name="name" value="<?= e($formCategory['name']) ?>" required></label>
            <label>Slug<input name="slug" value="<?= e($formCategory['slug']) ?>" placeholder="auto if blank"></label>
            <label>Description<textarea name="description" required><?= e($formCategory['description']) ?></textarea></label>
            <label>Sort order<input type="number" name="sort_order" value="<?= (int)$formCategory['sort_order'] ?>"></label>
            <label class="checkbox-line"><input type="checkbox" name="is_active" value="1" <?= !empty($formCategory['is_active']) ? 'checked' : '' ?>> Active</label>
        </fieldset>
        <fieldset>
            <legend>Banner Content</legend>
            <?php if ($editing && $formCategory['image']): ?><img class="admin-current-image" src="<?= uploaded($formCategory['image']) ?>" alt="Current category image"><?php endif; ?>
            <label>Category image<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-image-preview></label>
            <label>3D banner image<input type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp"></label>
            <div class="preview-grid upload-preview" data-preview-box></div>
            <label>Tagline<textarea name="tagline"><?= e($formCategory['tagline']) ?></textarea></label>
            <label>CTA text<input name="cta_text" value="<?= e($formCategory['cta_text'] ?: 'Shop This Category') ?>"></label>
            <label>CTA link<input name="cta_link" value="<?= e($formCategory['cta_link'] ?: '#products') ?>"></label>
        </fieldset>
    </div>
    <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Category' : 'Save Category' ?></button>
</form>
