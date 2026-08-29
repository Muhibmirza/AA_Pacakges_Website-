<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    public function featured(int $limit = 8): array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(array $filters = [], int $limit = 24, int $offset = 0): array
    {
        $where = ['p.is_active = 1'];
        $params = [];
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE ? OR p.short_description LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        switch ($filters['sort'] ?? '') {
            case 'price-low':
                $order = 'p.base_price ASC';
                break;
            case 'price-high':
                $order = 'p.base_price DESC';
                break;
            case 'newest':
                $order = 'p.created_at DESC';
                break;
            default:
                $order = 'p.is_featured DESC, p.name ASC';
        }
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where) . " ORDER BY {$order} LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.is_active = 1 LIMIT 1');
        $stmt->execute([$slug]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }
        $product['images'] = $this->images((int)$product['id']);
        $product['variants'] = $this->variants((int)$product['id']);
        $product['price_tiers'] = $this->priceTiers((int)$product['id']);
        return $product;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function images(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function variants(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_variants WHERE product_id = ? ORDER BY id');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function priceTiers(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM quantity_price_tiers WHERE product_id = ? ORDER BY min_quantity');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
}
