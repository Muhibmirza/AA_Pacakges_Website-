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
        return ['subtotal' => $subtotal, 'delivery' => $delivery, 'discount' => 0, 'total' => $subtotal + $delivery];
    }

    public function count(): int
    {
        return array_sum(array_map(fn ($item) => (int)$item['qty'], $this->cart()));
    }
}
