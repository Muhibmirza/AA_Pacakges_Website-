<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class QuoteRequest extends BaseModel
{
    public function create(array $data, ?string $logoPath): int
    {
        $stmt = $this->db->prepare('INSERT INTO quote_requests (business_name, contact_name, phone, email, box_category, box_size, quantity, custom_printing, logo_upload_path, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "new")');
        $stmt->execute([
            $data['business_name'] ?? '',
            $data['contact_name'],
            $data['phone'],
            $data['email'],
            $data['box_category'],
            $data['box_size'] ?? '',
            (int)$data['quantity'],
            !empty($data['custom_printing']) ? 1 : 0,
            $logoPath,
            $data['message'] ?? '',
        ]);
        return (int)$this->db->lastInsertId();
    }
}

