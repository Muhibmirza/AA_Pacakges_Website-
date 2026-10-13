<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Product.php';

class CartController extends BaseController
{
    public function page(): void
    {
        $this->render('cart', ['cart' => $this->cart(), 'totals' => $this->totals()]);
    }

    public function add(): void
    {
        verify_csrf();
        enforce_rate_limit('user', 'cart');
        $data = require_fields($_POST, [
            'product_id' => ['required' => true, 'type' => 'int', 'min_value' => 1, 'label' => 'Product'],
            'qty' => ['required' => true, 'type' => 'int', 'min_value' => 1, 'max_value' => 999, 'label' => 'Quantity'],
        ]);
        $product = (new Product($this->db))->find((int)$data['product_id']);
        if (!$product) {
            $this->json(['ok' => false, 'message' => 'Product not found.'], 404);
        }
        $qty = (int)$data['qty'];
        $key = (string)$product['id'];
        $_SESSION['cart'][$key] ??= [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'image' => $product['main_image'],
            'variant' => trim((string)$product['default_variant']),
            'price' => (float)$product['base_price'],
            'qty' => 0,
        ];
        $_SESSION['cart'][$key]['qty'] += $qty;
        $this->json(['ok' => true, 'cart' => $this->cart(), 'totals' => $this->totals(), 'count' => $this->count()]);
    }

    public function update(): void
    {
        verify_csrf();
        enforce_rate_limit('user', 'cart');
        $id = (string)($_POST['product_id'] ?? '');
        $qty = max(0, (int)($_POST['qty'] ?? 1));
        if (isset($_SESSION['cart'][$id])) {
            if ($qty === 0) {
                unset($_SESSION['cart'][$id]);
                if (!$this->cart()) {
                    unset($_SESSION['coupon']);
                }
            } else {
                $_SESSION['cart'][$id]['qty'] = $qty;
            }
        }
        $this->json(['ok' => true, 'cart' => $this->cart(), 'totals' => $this->totals(), 'count' => $this->count()]);
    }

    public function drawer(): void
    {
        $this->json(['ok' => true, 'cart' => $this->cart(), 'totals' => $this->totals(), 'count' => $this->count()]);
    }

    public function applyCoupon(): void
    {
        verify_csrf();
        enforce_rate_limit('public', 'coupon');
        try {
            $data = require_fields($_POST, [
                'code' => ['required' => true, 'min' => 3, 'max' => 60, 'pattern' => '/^[A-Za-z0-9_-]+$/', 'label' => 'Coupon code'],
            ]);
            $stmt = $this->db->prepare('SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE()) AND (usage_limit = 0 OR used_count < usage_limit) LIMIT 1');
            $stmt->execute([strtoupper($data['code'])]);
            $coupon = $stmt->fetch();
            if (!$coupon) {
                $this->json(['ok' => false, 'message' => 'This coupon is invalid, expired, or has reached its usage limit.'], 422);
            }
            $_SESSION['coupon'] = [
                'id' => (int)$coupon['id'],
                'code' => $coupon['code'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => (float)$coupon['discount_value'],
            ];
            $this->json(['ok' => true, 'message' => $coupon['code'] . ' applied successfully.', 'coupon' => $_SESSION['coupon'], 'totals' => $this->totals()]);
        } catch (InvalidArgumentException $exception) {
            $this->json(['ok' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function cart(): array
    {
        return array_values($_SESSION['cart'] ?? []);
    }

    public function totals(): array
    {
        $subtotal = 0.0;
        foreach ($this->cart() as $item) {
            $subtotal += (float)$item['price'] * (int)$item['qty'];
        }
        $delivery = $subtotal > 0 && $subtotal < 25000 ? 350 : 0;
        $discount = 0.0;
        $coupon = $_SESSION['coupon'] ?? null;
        if ($coupon && $subtotal > 0) {
            $discount = $coupon['discount_type'] === 'percentage'
                ? $subtotal * min(100, (float)$coupon['discount_value']) / 100
                : min($subtotal, (float)$coupon['discount_value']);
        }
        return [
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'discount' => round($discount, 2),
            'total' => max(0, $subtotal + $delivery - $discount),
            'coupon_id' => $coupon['id'] ?? null,
            'coupon_code' => $coupon['code'] ?? '',
        ];
    }

    public function count(): int
    {
        return array_sum(array_map(fn ($item) => (int)$item['qty'], $this->cart()));
    }
}
