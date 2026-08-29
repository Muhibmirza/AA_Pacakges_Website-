<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Setting extends BaseModel
{
    public function all(): array
    {
        $rows = $this->db->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        return array_column($rows, 'setting_value', 'setting_key');
    }

    public function get(string $key, string $fallback = ''): string
    {
        $stmt = $this->db->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        return (string)($stmt->fetchColumn() ?: $fallback);
    }

    public function put(string $key, string $value): void
    {
        $stmt = $this->db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $stmt->execute([$key, $value]);
    }
}

