<?php
$editing = !empty($editCoupon);
$formCoupon = $editCoupon ?: [
    'id' => 0,
    'code' => '',
    'discount_type' => 'percentage',
    'discount_value' => '',
    'expires_at' => '',
    'usage_limit' => 100,
    'is_active' => 1,
];
?>
<section class="admin-card">
    <div class="card-head"><h2>Coupons</h2><a class="btn btn-primary" href="<?= admin_route('coupons') ?>#coupon-form">Add Coupon</a></div>
    <table class="admin-table">
        <thead><tr><th>Code</th><th>Type</th><th class="num">Value</th><th>Expiry</th><th class="num">Usage Limit</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?= e($coupon['code']) ?></td>
                <td><?= e($coupon['discount_type']) ?></td>
                <td class="num"><?= e((string)$coupon['discount_value']) ?></td>
                <td><?= e($coupon['expires_at']) ?></td>
                <td class="num"><?= (int)$coupon['usage_limit'] ?></td>
                <td><span class="status-badge <?= $coupon['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $coupon['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td class="actions">
                    <a class="mini-action" href="<?= admin_route('coupons') ?>?edit=<?= (int)$coupon['id'] ?>#coupon-form">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this coupon?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_row">
                        <input type="hidden" name="table" value="coupons">
                        <input type="hidden" name="id" value="<?= (int)$coupon['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<form class="admin-card" id="coupon-form" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editing ? 'update_coupon' : 'create_coupon' ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$formCoupon['id'] ?>"><?php endif; ?>
    <div class="card-head">
        <h2><?= $editing ? 'Edit Coupon' : 'Add Coupon' ?></h2>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= admin_route('coupons') ?>">Cancel Edit</a><?php endif; ?>
    </div>
    <div class="admin-form-grid">
        <fieldset>
            <legend>Discount</legend>
            <label>Code<input name="code" value="<?= e($formCoupon['code']) ?>" required></label>
            <label>Type<select name="discount_type"><option value="percentage" <?= $formCoupon['discount_type'] === 'percentage' ? 'selected' : '' ?>>Percentage</option><option value="fixed" <?= $formCoupon['discount_type'] === 'fixed' ? 'selected' : '' ?>>Fixed</option></select></label>
            <label>Value<input type="number" step="0.01" name="discount_value" value="<?= e((string)$formCoupon['discount_value']) ?>" required></label>
        </fieldset>
        <fieldset>
            <legend>Limits</legend>
            <label>Expiry<input type="date" name="expires_at" value="<?= e((string)$formCoupon['expires_at']) ?>"></label>
            <label>Usage limit<input type="number" name="usage_limit" value="<?= (int)$formCoupon['usage_limit'] ?>"></label>
            <label class="checkbox-line"><input type="checkbox" name="is_active" value="1" <?= !empty($formCoupon['is_active']) ? 'checked' : '' ?>> Active</label>
        </fieldset>
    </div>
    <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Coupon' : 'Save Coupon' ?></button>
</form>
