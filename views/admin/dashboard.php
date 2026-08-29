<section class="stat-grid">
    <article class="stat-card"><span>O</span><div><strong><?= (int)$stats['orders'] ?></strong><p>Total Orders</p></div></article>
    <article class="stat-card"><span>R</span><div><strong><?= money((float)$stats['revenue']) ?></strong><p>Total Revenue</p></div></article>
    <article class="stat-card"><span>C</span><div><strong><?= (int)$stats['customers'] ?></strong><p>Total Customers</p></div></article>
    <article class="stat-card"><span>P</span><div><strong><?= (int)$stats['pending'] ?></strong><p>Pending Orders</p></div></article>
</section>
<section class="admin-grid two-col">
    <article class="admin-card">
        <div class="card-head"><h2>Sales Snapshot</h2><div class="segmented"><button>Daily</button><button class="active">Weekly</button><button>Monthly</button></div></div>
        <div class="chart-bars">
            <span style="height:45%" data-value="4" data-label="Mon"></span>
            <span style="height:72%" data-value="7" data-label="Tue"></span>
            <span style="height:55%" data-value="5" data-label="Wed"></span>
            <span style="height:88%" data-value="9" data-label="Thu"></span>
            <span style="height:64%" data-value="6" data-label="Fri"></span>
            <span style="height:92%" data-value="10" data-label="Sat"></span>
            <span style="height:78%" data-value="8" data-label="Sun"></span>
        </div>
    </article>
    <article class="admin-card">
        <div class="card-head"><h2>Low Stock Products</h2></div>
        <?php foreach ($lowStock as $item): ?><div class="mini-row"><span><?= e($item['name']) ?></span><strong><?= (int)$item['stock_quantity'] ?></strong></div><?php endforeach; ?>
    </article>
</section>
<section class="admin-grid two-col">
    <article class="admin-card">
        <div class="card-head"><h2>Recent Orders</h2><a href="<?= admin_route('orders') ?>">View all</a></div>
        <table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($recentOrders as $order): ?><tr><td><?= e($order['order_no']) ?></td><td><?= e($order['customer_name']) ?></td><td class="num"><?= money((float)$order['total']) ?></td><td><span class="status-badge <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span></td></tr><?php endforeach; ?>
        </tbody></table>
    </article>
    <article class="admin-card">
        <div class="card-head"><h2>New Quote Requests</h2><a href="<?= admin_route('quotes') ?>">View all</a></div>
        <?php foreach ($recentQuotes as $quote): ?><div class="mini-row"><span><?= e($quote['business_name'] ?: $quote['contact_name']) ?><small><?= e($quote['box_category']) ?> · <?= (int)$quote['quantity'] ?> pcs</small></span><strong><?= e($quote['status']) ?></strong></div><?php endforeach; ?>
    </article>
</section>
