<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel
{
    public function findForLogin(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM admins WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
}
