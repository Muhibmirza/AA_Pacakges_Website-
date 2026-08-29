<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Content extends BaseModel
{
    public function banners(string $position = 'home_hero'): array
    {
        $stmt = $this->db->prepare('SELECT * FROM banners WHERE position = ? AND is_active = 1 ORDER BY sort_order, id');
        $stmt->execute([$position]);
        return $stmt->fetchAll();
    }

    public function testimonials(): array
    {
        return $this->db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id LIMIT 8')->fetchAll();
    }

    public function uspBlocks(): array
    {
        return $this->db->query('SELECT * FROM usp_blocks WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
    }

    public function page(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }
}

