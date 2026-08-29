<div class="container page-wrap narrow">
    <section class="form-card reveal">
        <h1>Track your order</h1>
        <p>Enter your order ID and phone number to view the latest status.</p>
        <?php if ($message): ?><div class="alert danger"><?= e($message) ?></div><?php endif; ?>
        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <label>Order ID<input name="order_no" placeholder="AAP-260717-1234" required></label>
            <label>Phone<input name="phone" required></label>
            <button class="btn btn-primary" type="submit">Track Order</button>
        </form>
        <?php if ($order): ?>
            <div class="tracking-card">
                <h2><?= e($order['order_no']) ?></h2>
                <p>Total: <?= money((float)$order['total']) ?></p>
                <?php $steps = ['pending'=>'Order Placed','processing'=>'Confirmed','preparing'=>'Preparing','out-for-delivery'=>'Out for Delivery','delivered'=>'Delivered']; $current = array_search($order['status'], array_keys($steps), true); ?>
                <div class="timeline">
                    <?php $i = 0; foreach ($steps as $key => $label): ?>
                        <span class="<?= $i++ <= (int)$current ? 'done' : '' ?>"><?= e($label) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
