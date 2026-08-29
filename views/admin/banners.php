<?php
$editing = !empty($editBanner);
$formBanner = $editBanner ?: [
    'id' => 0,
    'position' => 'home_hero',
    'image_path' => '',
    'kicker' => '',
    'heading' => '',
    'subtext' => '',
    'button_text' => 'Shop Now',
    'button_link' => route('shop'),
    'sort_order' => 10,
    'is_active' => 1,
];
?>
<section class="admin-card">
    <div class="card-head"><h2>Homepage Hero Slider & Promo Banners</h2><a class="btn btn-primary" href="<?= admin_route('banners') ?>#banner-form">Add Slide</a></div>
    <table class="admin-table">
        <thead><tr><th class="thumb-col">Image</th><th>Heading</th><th>Position</th><th class="num">Order</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($banners as $banner): ?>
            <tr>
                <td><img class="thumb" src="<?= uploaded($banner['image_path']) ?>" alt=""></td>
                <td><?= e($banner['heading']) ?><small><?= e($banner['subtext']) ?></small></td>
                <td><?= e($banner['position']) ?></td>
                <td class="num"><?= (int)$banner['sort_order'] ?></td>
                <td><span class="status-badge <?= $banner['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $banner['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a class="mini-action" href="<?= admin_route('banners') ?>?edit=<?= (int)$banner['id'] ?>#banner-form">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this banner?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_row">
                        <input type="hidden" name="table" value="banners">
                        <input type="hidden" name="id" value="<?= (int)$banner['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<form class="admin-card" id="banner-form" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editing ? 'update_banner' : 'create_banner' ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$formBanner['id'] ?>"><?php endif; ?>
    <div class="card-head">
        <h2><?= $editing ? 'Edit Hero Slide / Promo Banner' : 'Add Hero Slide / Promo Banner' ?></h2>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= admin_route('banners') ?>">Cancel Edit</a><?php endif; ?>
    </div>
    <div class="admin-form-grid">
        <fieldset>
            <legend>Content</legend>
            <label>Position<input name="position" value="<?= e($formBanner['position']) ?>"></label>
            <label>Kicker<input name="kicker" value="<?= e($formBanner['kicker']) ?>"></label>
            <label>Heading<input name="heading" value="<?= e($formBanner['heading']) ?>" required></label>
            <label>Subtext<textarea name="subtext" required><?= e($formBanner['subtext']) ?></textarea></label>
        </fieldset>
        <fieldset>
            <legend>Action & Image</legend>
            <?php if ($editing && $formBanner['image_path']): ?><img class="admin-current-image" src="<?= uploaded($formBanner['image_path']) ?>" alt="Current banner image"><?php endif; ?>
            <label>Button text<input name="button_text" value="<?= e($formBanner['button_text']) ?>"></label>
            <label>Button link<input name="button_link" value="<?= e($formBanner['button_link']) ?>"></label>
            <label>Sort order<input type="number" name="sort_order" value="<?= (int)$formBanner['sort_order'] ?>"></label>
            <label class="checkbox-line"><input type="checkbox" name="is_active" value="1" <?= !empty($formBanner['is_active']) ? 'checked' : '' ?>> Active</label>
            <label>Image<input type="file" name="image_path" accept=".jpg,.jpeg,.png,.webp" <?= $editing ? '' : 'required' ?> data-image-preview></label>
            <div class="preview-grid upload-preview" data-preview-box></div>
        </fieldset>
    </div>
    <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Banner' : 'Save Banner' ?></button>
</form>
