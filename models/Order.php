<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel
{
    public function create(array $customer, array $cart, string $paymentMethod, ?int $userId = null): int
    {
        $this->db->beginTransaction();
        try {
            $subtotal = 0.0;
            foreach ($cart as $item) {
                $subtotal += (float)$item['price'] * (int)$item['qty'];
            }
            $delivery = $subtotal >= 25000 ? 0 : 350;
            $total = $subtotal + $delivery;
            $orderNo = 'AAP-' . date('ymd') . '-' . random_int(1000, 9999);
            $stmt = $this->db->prepare('INSERT INTO orders (order_no, user_id, customer_name, phone, email, business_name, address, city, area, notes, subtotal, delivery_fee, discount, total, status, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, "pending", ?, "pending")');
            $stmt->execute([$orderNo, $userId, $customer['name'], $customer['phone'], $customer['email'], $customer['business_name'] ?? '', $customer['address'], $customer['city'], $customer['area'], $customer['notes'] ?? '', $subtotal, $delivery, $total, $paymentMethod]);
            $orderId = (int)$this->db->lastInsertId();
            $itemStmt = $this->db->prepare('INSERT INTO order_items (order_id, product_id, product_name, variant_label, unit_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stockStmt = $this->db->prepare('UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE id = ?');
            foreach ($cart as $item) {
                $line = (float)$item['price'] * (int)$item['qty'];
                $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['variant'] ?? '', $item['price'], $item['qty'], $line]);
                $stockStmt->execute([$item['qty'], $item['id']]);
            }
            $pay = $this->db->prepare('INSERT INTO payments (order_id, transaction_ref, method, amount, status) VALUES (?, ?, ?, ?, "pending")');
            $pay->execute([$orderId, 'SANDBOX-' . $orderNo, $paymentMethod, $total]);
            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->items($id);
        return $order;
    }

    public function findForTracking(string $orderNo, string $phone): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_no = ? AND phone = ? LIMIT 1');
        $stmt->execute([$orderNo, $phone]);
        return $stmt->fetch() ?: null;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

