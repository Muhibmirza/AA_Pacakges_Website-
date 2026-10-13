<div class="container page-wrap">
    <section class="page-title reveal"><h1>Your cart</h1><p>Review quantities, apply a coupon, then proceed to checkout.</p></section>
    <div class="cart-page">
        <div class="cart-list reveal" data-cart-page-list>
            <?php if (!$cart): ?>
                <div class="empty-state">Your cart is empty.</div>
            <?php endif; ?>
            <?php foreach ($cart as $item): ?>
                <article class="cart-line">
                    <img src="<?= uploaded($item['image']) ?>" alt="<?= e($item['name']) ?>">
                    <div><h3><?= e($item['name']) ?></h3><p><?= e($item['variant']) ?></p></div>
                    <strong><?= money((float)$item['price']) ?></strong>
                    <form data-cart-update>
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                        <input type="number" name="qty" value="<?= (int)$item['qty'] ?>" min="0">
                    </form>
                    <strong><?= money((float)$item['price'] * (int)$item['qty']) ?></strong>
                    <form data-cart-remove>
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                        <input type="hidden" name="qty" value="0">
                        <button class="remove-btn" type="submit">Remove</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
        <aside class="summary-card reveal">
            <h2>Order Summary</h2>
            <div class="summary-row"><span>Subtotal</span><strong><?= money($totals['subtotal']) ?></strong></div>
            <div class="summary-row"><span>Delivery</span><strong><?= money($totals['delivery']) ?></strong></div>
            <?php if ($totals['discount'] > 0): ?><div class="summary-row"><span>Discount (<?= e($totals['coupon_code']) ?>)</span><strong>-<?= money($totals['discount']) ?></strong></div><?php endif; ?>
            <form class="coupon-row" data-coupon-form>
                <?= csrf_field() ?><input name="code" value="<?= e($totals['coupon_code']) ?>" placeholder="Coupon code" maxlength="60" required><button class="btn btn-outline" type="submit">Apply</button>
            </form>
            <div class="form-status" data-coupon-status aria-live="polite"></div>
            <div class="summary-row total"><span>Total</span><strong><?= money($totals['total']) ?></strong></div>
            <a class="btn btn-primary full" href="<?= route('checkout') ?>">Proceed to Checkout</a>
            <a class="text-link" href="<?= route('shop') ?>">Continue Shopping</a>
        </aside>
    </div>
</div>
