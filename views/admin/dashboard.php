<section class="stat-grid">
    <article class="stat-card"><span>O</span><div><strong><?= (int)$stats['orders'] ?></strong><p>Total Orders</p></div></article>
    <article class="stat-card"><span>R</span><div><strong><?= money((float)$stats['revenue']) ?></strong><p>Total Revenue</p></div></article>
    <article class="stat-card"><span>C</span><div><strong><?= (int)$stats['customers'] ?></strong><p>Total Customers</p></div></article>
    <article class="stat-card"><span>P</span><div><strong><?= (int)$stats['pending'] ?></strong><p>Pending Orders</p></div></article>
</section>
<section class="admin-grid two-col">
    <article class="admin-card">
        <div class="card-head"><h2>Orders - Last 7 Days</h2></div>
        <div class="chart-bars">
            <?php $maxOrders = max(1, ...array_column($salesLastSevenDays, 'count')); ?>
            <?php foreach ($salesLastSevenDays as $day): ?>
                <span style="height:<?= max(18, (int)round(((int)$day['count'] / $maxOrders) * 100)) ?>%" data-value="<?= (int)$day['count'] ?>" data-label="<?= e($day['label']) ?>"></span>
            <?php endforeach; ?>
        </div>
    </article>
    <article class="admin-card">
        <div class="card-head"><h2>Low Stock Products</h2></div>
        <?php if (!$lowStock): ?><p class="empty-state">No low-stock products.</p><?php endif; ?>
        <?php foreach ($lowStock as $item): ?><div class="mini-row"><span><?= e($item['name']) ?></span><strong><?= (int)$item['stock_quantity'] ?></strong></div><?php endforeach; ?>
    </article>
</section>
<section class="admin-grid two-col">
    <article class="admin-card">
        <div class="card-head"><h2>Recent Orders</h2><a href="<?= admin_route('orders') ?>">View all</a></div>
        <table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody>
        <?php if (!$recentOrders): ?><tr><td colspan="4" class="empty-state">No orders yet.</td></tr><?php endif; ?>
        <?php foreach ($recentOrders as $order): ?><tr><td><?= e($order['order_no']) ?></td><td><?= e($order['customer_name']) ?></td><td class="num"><?= money((float)$order['total']) ?></td><td><span class="status-badge <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span></td></tr><?php endforeach; ?>
        </tbody></table>
    </article>
    <article class="admin-card">
        <div class="card-head"><h2>New Quote Requests</h2><a href="<?= admin_route('quotes') ?>">View all</a></div>
        <?php if (!$recentQuotes): ?><p class="empty-state">No quote requests yet.</p><?php endif; ?>
        <?php foreach ($recentQuotes as $quote): ?>
            <div class="mini-row">
                <span><?= e($quote['business_name'] ?: $quote['contact_name']) ?><small><?= e($quote['box_category']) ?> &middot; <?= (int)$quote['quantity'] ?> pcs</small></span>
                <strong><?= e($quote['status']) ?></strong>
            </div>
        <?php endforeach; ?>
    </article>
</section>
