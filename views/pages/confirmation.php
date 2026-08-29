<div class="container page-wrap narrow">
    <section class="success-card reveal">
        <?php if ($order): ?>
            <span class="success-mark">✓</span>
            <h1>Order confirmed</h1>
            <p>Your order <strong><?= e($order['order_no']) ?></strong> has been received. Estimated delivery details will be confirmed by our team.</p>
            <a class="btn btn-primary" href="<?= route('track') ?>">Track Order</a>
        <?php else: ?>
            <h1>Order not found</h1>
            <p>We could not find that order confirmation.</p>
        <?php endif; ?>
    </section>
</div>
