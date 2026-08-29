<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{
    public function all(bool $activeOnly = true): array
    {
        $sql = 'SELECT c.*, cb.tagline, cb.cta_text, cb.cta_link, cb.banner_image FROM categories c LEFT JOIN category_banners cb ON cb.category_id = c.id';
        if ($activeOnly) {
            $sql .= ' WHERE c.is_active = 1';
        }
        $sql .= ' ORDER BY c.sort_order, c.name';
        return $this->db->query($sql)->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT c.*, cb.tagline, cb.cta_text, cb.cta_link, cb.banner_image FROM categories c LEFT JOIN category_banners cb ON cb.category_id = c.id WHERE c.slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}

