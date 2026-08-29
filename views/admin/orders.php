<section class="admin-card">
    <div class="card-head"><h2>Orders</h2><button class="btn btn-outline" onclick="window.print()">Print</button></div>
    <div class="status-tabs"><a class="active">All</a><a>Pending</a><a>Processing</a><a>Out for Delivery</a><a>Delivered</a><a>Cancelled</a></div>
    <div class="table-tools"><input placeholder="Search order or phone"><button class="btn btn-outline">Filter</button></div>
    <table class="admin-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th class="num">Total</th><th>Payment</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr><td><?= e($order['order_no']) ?><small><?= e(date('M d, Y', strtotime($order['created_at']))) ?></small></td><td><?= e($order['customer_name']) ?></td><td><?= e($order['phone']) ?></td><td class="num"><?= money((float)$order['total']) ?></td><td><?= e($order['payment_method']) ?></td><td><span class="status-badge <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span></td><td class="actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="update_order_status"><input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>"><select name="status"><option <?= $order['status']==='pending'?'selected':'' ?>>pending</option><option <?= $order['status']==='processing'?'selected':'' ?>>processing</option><option <?= $order['status']==='preparing'?'selected':'' ?>>preparing</option><option <?= $order['status']==='out-for-delivery'?'selected':'' ?>>out-for-delivery</option><option <?= $order['status']==='delivered'?'selected':'' ?>>delivered</option><option <?= $order['status']==='cancelled'?'selected':'' ?>>cancelled</option></select><button type="submit">Save</button></form></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
