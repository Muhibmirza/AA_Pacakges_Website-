<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    public function findForLogin(string $login): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, phone, password_hash, address, status) VALUES (?, ?, ?, ?, ?, "active")');
        $stmt->execute([$data['name'], $data['email'], $data['phone'], password_hash($data['password'], PASSWORD_DEFAULT), $data['address'] ?? '']);
        return (int)$this->db->lastInsertId();
    }

    public function orders(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT o.*, COUNT(oi.id) AS item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function orderDetail(int $userId, int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $items = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $items->execute([$orderId]);
        $order['items'] = $items->fetchAll();
        return $order;
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?');
        $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address'] ?? '', $id]);
    }

    public function changePassword(int $id, string $current, string $new): bool
    {
        $user = $this->find($id);
        if (!$user || !password_verify($current, $user['password_hash'])) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $id]);
        return true;
    }
}
