<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class ContactMessage extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['name'], strtolower($data['email']), $data['phone'], $data['message']]);
        return (int)$this->db->lastInsertId();
    }
}
