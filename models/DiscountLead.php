<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class DiscountLead extends BaseModel
{
    public function subscribe(array $data): array
    {
        $existing = $this->findByEmail($data['email']);
        $stmt = $this->db->prepare(
            'INSERT INTO discount_leads (name, email, phone, source, updated_at)
             VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
             ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), source = VALUES(source), updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([$data['name'], strtolower($data['email']), $data['phone'], 'first_order_popup']);

        return [
            'duplicate' => $existing !== null,
            'id' => $existing ? (int)$existing['id'] : (int)$this->db->lastInsertId(),
        ];
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM discount_leads WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower($email)]);
        return $stmt->fetch() ?: null;
    }
}
