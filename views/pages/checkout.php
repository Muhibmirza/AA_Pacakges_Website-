<div class="container page-wrap">
    <section class="stepper reveal"><span>Cart</span><span class="active">Details</span><span>Payment</span><span>Confirmation</span></section>
    <form class="checkout-grid" method="post">
        <?= csrf_field() ?>
        <section class="form-card reveal">
            <h1>Delivery details</h1>
            <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
            <div class="form-grid two">
                <label>Name<input name="name" required value="<?= e($_SESSION['user_name'] ?? '') ?>"></label>
                <label>Phone<input name="phone" required></label>
                <label>Email<input type="email" name="email" required></label>
                <label>Business name <span>optional</span><input name="business_name"></label>
                <label class="wide">Address<textarea name="address" required></textarea></label>
                <label>City<input name="city" required></label>
                <label>Area<input name="area" required></label>
                <label class="wide">Order notes<textarea name="notes"></textarea></label>
            </div>
            <h2>Payment method</h2>
            <div class="payment-grid payment-grid--cod">
                <div class="payment-option payment-option--selected">
                    <img src="<?= asset('assets/images/payments/cod.svg') ?>" alt="Cash on Delivery">
                    <span><strong>Cash on Delivery</strong><small>Pay in cash when your order is delivered.</small></span>
                </div>
            </div>
        </section>
        <aside class="summary-card sticky reveal">
            <h2>Order Summary</h2>
            <?php foreach ($cart as $item): ?>
                <div class="checkout-line"><span><?= e($item['name']) ?> x <?= (int)$item['qty'] ?></span><strong><?= money((float)$item['price'] * (int)$item['qty']) ?></strong></div>
            <?php endforeach; ?>
            <div class="summary-row"><span>Subtotal</span><strong><?= money($totals['subtotal']) ?></strong></div>
            <div class="summary-row"><span>Delivery</span><strong><?= money($totals['delivery']) ?></strong></div>
            <div class="summary-row total"><span>Total</span><strong><?= money($totals['total']) ?></strong></div>
            <button class="btn btn-primary full" type="submit">Place Order</button>
        </aside>
    </form>
</div>
