<?php
$activeTab = $tab ?? 'orders';
$accountTabs = [
    'orders' => 'Order History',
    'profile' => 'Profile Info',
    'addresses' => 'Saved Addresses',
    'password' => 'Change Password',
    'tracking' => 'Tracking',
];
?>
<div class="container page-wrap account-layout">
    <aside class="account-side reveal">
        <strong><?= e($_SESSION['user_name'] ?? 'Customer') ?></strong>
        <?php foreach ($accountTabs as $key => $label): ?>
            <a class="<?= $activeTab === $key ? 'active' : '' ?>" href="<?= route('account', ['tab' => $key]) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="<?= route('logout') ?>" data-logout-confirm>Logout</a>
    </aside>

    <section class="table-card reveal">
        <?php if ($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>

        <?php if ($activeTab === 'orders'): ?>
            <h1>Order history</h1>
            <table class="data-table">
                <thead><tr><th>Order ID</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['order_no']) ?></td>
                        <td><?= e(date('M d, Y', strtotime($order['created_at']))) ?></td>
                        <td><?= (int)$order['item_count'] ?></td>
                        <td><?= money((float)$order['total']) ?></td>
                        <td><span class="status-badge <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span></td>
                        <td><a class="text-link" href="<?= route('account', ['tab' => 'orders', 'order' => (int)$order['id']]) ?>">View Details</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($orderDetail): ?>
                <div class="order-detail-panel">
                    <h2>Order <?= e($orderDetail['order_no']) ?></h2>
                    <div class="tracking-card compact">
                        <p><strong>Status:</strong> <span class="status-badge <?= status_class($orderDetail['status']) ?>"><?= e($orderDetail['status']) ?></span></p>
                        <p><strong>Delivery:</strong> <?= e($orderDetail['address']) ?>, <?= e($orderDetail['area']) ?>, <?= e($orderDetail['city']) ?></p>
                    </div>
                    <table class="data-table">
                        <thead><tr><th>Item</th><th>Variant</th><th>Qty</th><th>Subtotal</th></tr></thead>
                        <tbody>
                        <?php foreach ($orderDetail['items'] as $item): ?>
                            <tr><td><?= e($item['product_name']) ?></td><td><?= e($item['variant_label']) ?></td><td><?= (int)$item['quantity'] ?></td><td><?= money((float)$item['subtotal']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($activeTab === 'profile'): ?>
            <h1>Profile info</h1>
            <form class="form-grid two" method="post">
                <?= csrf_field() ?><input type="hidden" name="action" value="update_profile">
                <label>Name<input name="name" value="<?= e($user['name'] ?? '') ?>" required></label>
                <label>Email<input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required></label>
                <label>Phone<input name="phone" value="<?= e($user['phone'] ?? '') ?>" required></label>
                <label class="wide">Address<textarea name="address"><?= e($user['address'] ?? '') ?></textarea></label>
                <button class="btn btn-primary" type="submit">Save Profile</button>
            </form>
        <?php endif; ?>

        <?php if ($activeTab === 'addresses'): ?>
            <h1>Saved addresses</h1>
            <form class="form-grid" method="post">
                <?= csrf_field() ?><input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="name" value="<?= e($user['name'] ?? '') ?>">
                <input type="hidden" name="email" value="<?= e($user['email'] ?? '') ?>">
                <input type="hidden" name="phone" value="<?= e($user['phone'] ?? '') ?>">
                <label>Default delivery address<textarea name="address" rows="5"><?= e($user['address'] ?? '') ?></textarea></label>
                <button class="btn btn-primary" type="submit">Save Address</button>
            </form>
        <?php endif; ?>

        <?php if ($activeTab === 'password'): ?>
            <h1>Change password</h1>
            <form class="form-grid" method="post">
                <?= csrf_field() ?><input type="hidden" name="action" value="change_password">
                <label>Current password<input type="password" name="current_password" required></label>
                <label>New password<input type="password" name="new_password" minlength="8" required></label>
                <button class="btn btn-primary" type="submit">Change Password</button>
            </form>
        <?php endif; ?>

        <?php if ($activeTab === 'tracking'): ?>
            <h1>Order tracking</h1>
            <p>Select an order to view its live status timeline.</p>
            <div class="tracking-list">
                <?php foreach ($orders as $order): ?>
                    <a class="tracking-row" href="<?= route('track') ?>">
                        <span><?= e($order['order_no']) ?></span>
                        <span class="status-badge <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="modal-overlay" data-logout-modal hidden>
    <div class="confirm-modal">
        <h2>Logout?</h2>
        <p>Are you sure you want to logout from your account?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" type="button" data-logout-cancel>No</button>
            <a class="btn btn-primary" href="<?= route('logout') ?>">Yes, Logout</a>
        </div>
    </div>
</div>
