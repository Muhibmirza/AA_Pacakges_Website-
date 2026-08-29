<section class="admin-card">
    <div class="card-head"><h2>Payments</h2></div>
    <table class="admin-table"><thead><tr><th>Order</th><th>Transaction</th><th>Method</th><th class="num">Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>
    <?php foreach ($payments as $payment): ?><tr><td><?= e($payment['order_no']) ?></td><td><?= e($payment['transaction_ref']) ?></td><td><?= e($payment['method']) ?></td><td class="num"><?= money((float)$payment['amount']) ?></td><td><span class="status-badge <?= status_class($payment['status']) ?>"><?= e($payment['status']) ?></span></td><td><?= e(date('M d, Y', strtotime($payment['created_at']))) ?></td></tr><?php endforeach; ?>
    </tbody></table>
</section>

