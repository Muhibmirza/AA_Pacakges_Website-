<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Category.php';
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/Setting.php';

class AdminController
{
    private PDO $db;
    private array $settings;

    public function __construct(PDO $db, array $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    public function login(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            header('Location: ' . admin_route('dashboard'));
            exit;
        }
        $error = !empty($_GET['timeout']) ? 'Your admin session expired after inactivity. Please sign in again.' : '';
        if (is_post()) {
            verify_csrf();
            enforce_rate_limit('auth', $_POST['email'] ?? '');
            if (too_many_attempts('admin_login')) {
                $error = 'Too many admin login attempts. Please try again later.';
            } else {
                $admin = (new Admin($this->db))->findForLogin(trim($_POST['email'] ?? ''));
                if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = (int)$admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_last_seen'] = time();
                    clear_attempts('admin_login');
                    clear_rate_limit('auth', $_POST['email'] ?? '');
                    header('Location: ' . admin_route('dashboard'));
                    exit;
                }
                record_attempt('admin_login');
                $error = 'Invalid admin credentials.';
            }
        }
        $settings = $this->settings;
        require ROOT_PATH . '/views/admin/login.php';
    }

    public function dispatch(string $page): void
    {
        if ($page === 'logout') {
            admin_guard();
            if (!is_post()) {
                header('Location: ' . admin_route('dashboard'));
                exit;
            }
            verify_csrf();
            unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_last_seen']);
            session_regenerate_id(true);
            header('Location: ' . url(ADMIN_PATH));
            exit;
        }
        admin_guard();
        if (is_post()) {
            $this->handlePost($page);
        }
        switch ($page) {
            case 'products':
                $data = [
                    'products' => $this->db->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC')->fetchAll(),
                    'categories' => (new Category($this->db))->all(false),
                    'editProduct' => !empty($_GET['edit']) ? $this->findRow('products', (int)$_GET['edit']) : null,
                ];
                break;
            case 'categories':
                $data = [
                    'categories' => (new Category($this->db))->all(false),
                    'editCategory' => !empty($_GET['edit']) ? $this->findCategoryWithBanner((int)$_GET['edit']) : null,
                ];
                break;
            case 'orders':
                $data = ['orders' => $this->db->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll()];
                break;
            case 'quotes':
                $data = ['quotes' => $this->db->query('SELECT * FROM quote_requests ORDER BY created_at DESC')->fetchAll()];
                break;
            case 'leads':
                if (($_GET['export'] ?? '') === 'csv') {
                    $this->exportDiscountLeads();
                }
                $data = ['leads' => $this->discountLeads()];
                break;
            case 'contact-messages':
                $data = ['messages' => $this->contactMessages()];
                break;
            case 'pages':
                $stmt = $this->db->query("SELECT * FROM pages WHERE slug IN ('about', 'contact') ORDER BY FIELD(slug, 'about', 'contact')");
                $data = ['managedPages' => $stmt->fetchAll()];
                break;
            case 'customers':
                $data = ['customers' => $this->db->query('SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total),0) AS total_spent FROM users u LEFT JOIN orders o ON o.user_id = u.id GROUP BY u.id ORDER BY u.created_at DESC')->fetchAll()];
                break;
            case 'coupons':
                $data = [
                    'coupons' => $this->db->query('SELECT * FROM coupons ORDER BY created_at DESC')->fetchAll(),
                    'editCoupon' => !empty($_GET['edit']) ? $this->findRow('coupons', (int)$_GET['edit']) : null,
                ];
                break;
            case 'reports':
                $data = ['orders' => $this->db->query('SELECT status, COUNT(*) AS count_orders, SUM(total) AS revenue FROM orders GROUP BY status')->fetchAll()];
                break;
            case 'banners':
                $data = [
                    'banners' => $this->db->query('SELECT * FROM banners ORDER BY position, sort_order')->fetchAll(),
                    'editBanner' => !empty($_GET['edit']) ? $this->findRow('banners', (int)$_GET['edit']) : null,
                ];
                break;
            case 'settings':
                $data = ['settingsRows' => $this->db->query('SELECT * FROM settings ORDER BY setting_key')->fetchAll()];
                break;
            case 'admins':
                $data = ['admins' => $this->db->query('SELECT id, name, email, role, is_active, created_at FROM admins ORDER BY id')->fetchAll()];
                break;
            default:
                $data = $this->dashboardData();
        }
        $view = in_array($page, ['products','categories','orders','quotes','leads','contact-messages','pages','customers','coupons','reports','banners','settings','admins'], true) ? $page : 'dashboard';
        $settings = $this->settings;
        extract($data);
        require ROOT_PATH . '/views/admin/layout.php';
    }

    private function dashboardData(): array
    {
        $stats = [
            'orders' => (int)$this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'revenue' => (float)$this->db->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn(),
            'customers' => (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'pending' => (int)$this->db->query('SELECT COUNT(*) FROM orders WHERE status = "pending"')->fetchColumn(),
        ];
        $dailyRows = $this->db->query('SELECT DATE(created_at) AS order_date, COUNT(*) AS order_count FROM orders WHERE created_at >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(created_at)')->fetchAll();
        $dailyCounts = [];
        foreach ($dailyRows as $row) {
            $dailyCounts[$row['order_date']] = (int)$row['order_count'];
        }
        $salesLastSevenDays = [];
        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = date('Y-m-d', strtotime("-{$daysAgo} days"));
            $salesLastSevenDays[] = [
                'label' => date('D', strtotime($date)),
                'count' => $dailyCounts[$date] ?? 0,
            ];
        }
        return [
            'stats' => $stats,
            'salesLastSevenDays' => $salesLastSevenDays,
            'recentOrders' => (new Order($this->db))->recent(8),
            'lowStock' => $this->db->query('SELECT name, stock_quantity FROM products WHERE stock_quantity <= 100 ORDER BY stock_quantity ASC LIMIT 8')->fetchAll(),
            'recentQuotes' => $this->db->query('SELECT * FROM quote_requests WHERE status IN ("new","contacted") ORDER BY created_at DESC LIMIT 6')->fetchAll(),
        ];
    }

    private function handlePost(string $page): never
    {
        verify_csrf();
        $action = $_POST['action'] ?? '';
        try {
            switch ($action) {
                case 'save_settings':
                    $this->saveSettings();
                    break;
                case 'create_product':
                    $this->createProduct();
                    break;
                case 'update_product':
                    $this->updateProduct();
                    break;
                case 'create_category':
                    $this->createCategory();
                    break;
                case 'update_category':
                    $this->updateCategory();
                    break;
                case 'update_order_status':
                    $stmt = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');
                    $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
                    break;
                case 'update_quote':
                    $stmt = $this->db->prepare('UPDATE quote_requests SET status = ?, internal_notes = ? WHERE id = ?');
                    $stmt->execute([$_POST['status'], $_POST['internal_notes'] ?? '', (int)$_POST['quote_id']]);
                    break;
                case 'update_contact_message':
                    $status = in_array($_POST['status'] ?? '', ['new', 'read', 'replied', 'closed'], true) ? $_POST['status'] : 'new';
                    $stmt = $this->db->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
                    $stmt->execute([$status, (int)$_POST['message_id']]);
                    break;
                case 'save_page':
                    $this->savePage();
                    break;
                case 'update_customer_status':
                    $stmt = $this->db->prepare('UPDATE users SET status = ? WHERE id = ?');
                    $stmt->execute([$_POST['status'] === 'blocked' ? 'blocked' : 'active', (int)$_POST['user_id']]);
                    break;
                case 'update_admin_status':
                    $stmt = $this->db->prepare('UPDATE admins SET is_active = ? WHERE id = ? AND id <> ?');
                    $stmt->execute([!empty($_POST['is_active']) ? 1 : 0, (int)$_POST['admin_id'], (int)$_SESSION['admin_id']]);
                    break;
                case 'create_banner':
                    $this->createBanner();
                    break;
                case 'update_banner':
                    $this->updateBanner();
                    break;
                case 'create_coupon':
                    $stmt = $this->db->prepare('INSERT INTO coupons (code, discount_type, discount_value, expires_at, usage_limit, is_active) VALUES (?, ?, ?, ?, ?, 1)');
                    $stmt->execute([strtoupper(trim($_POST['code'])), $_POST['discount_type'], (float)$_POST['discount_value'], $_POST['expires_at'] ?: null, (int)$_POST['usage_limit']]);
                    break;
                case 'update_coupon':
                    $stmt = $this->db->prepare('UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, expires_at = ?, usage_limit = ?, is_active = ? WHERE id = ?');
                    $stmt->execute([strtoupper(trim($_POST['code'])), $_POST['discount_type'], (float)$_POST['discount_value'], $_POST['expires_at'] ?: null, (int)$_POST['usage_limit'], !empty($_POST['is_active']) ? 1 : 0, (int)$_POST['id']]);
                    break;
                case 'delete_row':
                    $this->deleteRow($_POST['table'] ?? '', (int)($_POST['id'] ?? 0));
                    break;
            }
            $_SESSION['admin_flash'] = 'Changes saved.';
        } catch (Throwable $e) {
            $_SESSION['admin_flash'] = $e->getMessage();
        }
        header('Location: ' . admin_route($page));
        exit;
    }

    private function saveSettings(): void
    {
        $setting = new Setting($this->db);
        foreach (['site_name','theme_primary','theme_primary_light','theme_secondary','contact_phone','contact_address','business_hours','footer_about','map_embed'] as $key) {
            if (isset($_POST[$key])) {
                $setting->put($key, trim((string)$_POST[$key]));
            }
        }
        foreach (['contact_email','postal_code'] as $key) {
            if (isset($_POST[$key])) {
                $setting->put($key, trim((string)$_POST[$key]));
            }
        }
        $logo = upload_image('site_logo', 'logo');
        if ($logo) {
            $setting->put('site_logo', $logo);
        }
        $pattern = upload_image('background_pattern', 'banners');
        if ($pattern) {
            $setting->put('background_pattern', $pattern);
        }
    }

    private function savePage(): void
    {
        $slug = strtolower(trim((string)($_POST['slug'] ?? '')));
        if (!in_array($slug, ['about', 'contact'], true)) {
            throw new RuntimeException('This page cannot be edited here.');
        }
        $data = require_fields($_POST, [
            'title' => ['required' => true, 'min' => 5, 'max' => 190, 'label' => 'Page heading'],
            'content' => ['required' => true, 'min' => 10, 'max' => 10000, 'label' => 'Page content'],
        ]);
        $content = trim(strip_tags($data['content']));
        $stmt = $this->db->prepare(
            'INSERT INTO pages (slug, title, content) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([$slug, trim($data['title']), $content]);
    }

    private function createProduct(): void
    {
        $categoryId = (int)$_POST['category_id'];
        $data = require_fields($_POST, [
            'name' => ['required' => true, 'min' => 2, 'max' => 190, 'label' => 'Product name'],
            'short_description' => ['required' => true, 'min' => 5, 'max' => 255, 'label' => 'Short description'],
            'description' => ['required' => true, 'min' => 5, 'max' => 3000, 'label' => 'Description'],
            'default_variant' => ['required' => true, 'min' => 2, 'max' => 120, 'label' => 'Default variant'],
            'base_price' => ['required' => true, 'pattern' => '/^\d+(\.\d{1,2})?$/', 'max' => 12, 'label' => 'Base price'],
            'stock_quantity' => ['required' => true, 'type' => 'int', 'min_value' => 0, 'max_value' => 1000000, 'label' => 'Stock'],
        ]);
        $category = (new Category($this->db))->find($categoryId);
        if (!$category) {
            throw new RuntimeException('Please choose a valid category.');
        }
        $image = upload_image('main_image', 'products') ?: $category['image'];
        $slug = slugify($_POST['slug'] ?: $data['name']);
        $stmt = $this->db->prepare('INSERT INTO products (category_id, name, slug, short_description, description, default_variant, base_price, stock_quantity, main_image, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
        $stmt->execute([$categoryId, $data['name'], $slug, $data['short_description'], $data['description'], $data['default_variant'], (float)$data['base_price'], (int)$data['stock_quantity'], $image, !empty($_POST['is_featured']) ? 1 : 0]);
        $productId = (int)$this->db->lastInsertId();
        $this->db->prepare('INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?, ?, 1)')->execute([$productId, $image]);
        $this->db->prepare('INSERT INTO product_variants (product_id, size_label, material, price, stock_quantity) VALUES (?, ?, ?, ?, ?)')->execute([$productId, $_POST['variant_size'] ?: 'Standard', $_POST['variant_material'] ?: $data['default_variant'], (float)$data['base_price'], (int)$data['stock_quantity']]);
        foreach ([50 => .96, 100 => .91, 500 => .82] as $qty => $factor) {
            $this->db->prepare('INSERT INTO quantity_price_tiers (product_id, min_quantity, unit_price) VALUES (?, ?, ?)')->execute([$productId, $qty, round((float)$data['base_price'] * $factor, 2)]);
        }
    }

    private function updateProduct(): void
    {
        $id = (int)$_POST['id'];
        $current = $this->findRow('products', $id);
        if (!$current) {
            throw new RuntimeException('Product not found.');
        }
        $categoryId = (int)$_POST['category_id'];
        $data = require_fields($_POST, [
            'name' => ['required' => true, 'min' => 2, 'max' => 190, 'label' => 'Product name'],
            'short_description' => ['required' => true, 'min' => 5, 'max' => 255, 'label' => 'Short description'],
            'description' => ['required' => true, 'min' => 5, 'max' => 3000, 'label' => 'Description'],
            'default_variant' => ['required' => true, 'min' => 2, 'max' => 120, 'label' => 'Default variant'],
            'base_price' => ['required' => true, 'pattern' => '/^\d+(\.\d{1,2})?$/', 'max' => 12, 'label' => 'Base price'],
            'stock_quantity' => ['required' => true, 'type' => 'int', 'min_value' => 0, 'max_value' => 1000000, 'label' => 'Stock'],
        ]);
        $image = upload_image('main_image', 'products') ?: $current['main_image'];
        $slug = slugify($_POST['slug'] ?: $data['name']);
        $stmt = $this->db->prepare('UPDATE products SET category_id = ?, name = ?, slug = ?, short_description = ?, description = ?, default_variant = ?, base_price = ?, stock_quantity = ?, main_image = ?, is_featured = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$categoryId, $data['name'], $slug, $data['short_description'], $data['description'], $data['default_variant'], (float)$data['base_price'], (int)$data['stock_quantity'], $image, !empty($_POST['is_featured']) ? 1 : 0, !empty($_POST['is_active']) ? 1 : 0, $id]);
        $this->db->prepare('UPDATE product_images SET image_path = ? WHERE product_id = ? AND sort_order = 1')->execute([$image, $id]);
        $variant = $this->db->prepare('SELECT id FROM product_variants WHERE product_id = ? ORDER BY id LIMIT 1');
        $variant->execute([$id]);
        $variantId = $variant->fetchColumn();
        if ($variantId) {
            $this->db->prepare('UPDATE product_variants SET size_label = ?, material = ?, price = ?, stock_quantity = ? WHERE id = ?')->execute([$data['default_variant'], $_POST['variant_material'] ?: $data['default_variant'], (float)$data['base_price'], (int)$data['stock_quantity'], (int)$variantId]);
        } else {
            $this->db->prepare('INSERT INTO product_variants (product_id, size_label, material, price, stock_quantity) VALUES (?, ?, ?, ?, ?)')->execute([$id, $data['default_variant'], $_POST['variant_material'] ?: $data['default_variant'], (float)$data['base_price'], (int)$data['stock_quantity']]);
        }
    }

    private function createCategory(): void
    {
        $image = upload_image('image', 'categories') ?: 'public/assets/images/categories/custom-printed-boxes.png';
        $data = require_fields($_POST, [
            'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Category name'],
            'description' => ['required' => true, 'min' => 5, 'max' => 255, 'label' => 'Description'],
            'sort_order' => ['required' => true, 'type' => 'int', 'min_value' => 0, 'max_value' => 999, 'label' => 'Sort order'],
        ]);
        $slug = slugify($_POST['slug'] ?: $data['name']);
        $stmt = $this->db->prepare('INSERT INTO categories (name, slug, description, image, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 1)');
        $stmt->execute([$data['name'], $slug, $data['description'], $image, (int)$data['sort_order']]);
        $categoryId = (int)$this->db->lastInsertId();
        $this->db->prepare('INSERT INTO category_banners (category_id, banner_image, tagline, cta_text, cta_link) VALUES (?, ?, ?, ?, ?)')->execute([$categoryId, $image, $_POST['tagline'] ?: $_POST['description'], $_POST['cta_text'] ?: 'Shop This Category', $_POST['cta_link'] ?: '#products']);
    }

    private function updateCategory(): void
    {
        $id = (int)$_POST['id'];
        $current = $this->findRow('categories', $id);
        if (!$current) {
            throw new RuntimeException('Category not found.');
        }
        $data = require_fields($_POST, [
            'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Category name'],
            'description' => ['required' => true, 'min' => 5, 'max' => 255, 'label' => 'Description'],
            'sort_order' => ['required' => true, 'type' => 'int', 'min_value' => 0, 'max_value' => 999, 'label' => 'Sort order'],
        ]);
        $image = upload_image('image', 'categories') ?: $current['image'];
        $slug = slugify($_POST['slug'] ?: $data['name']);
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, slug = ?, description = ?, image = ?, sort_order = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$data['name'], $slug, $data['description'], $image, (int)$data['sort_order'], !empty($_POST['is_active']) ? 1 : 0, $id]);
        $bannerImage = upload_image('banner_image', 'categories') ?: $image;
        $upsert = $this->db->prepare('INSERT INTO category_banners (category_id, banner_image, tagline, cta_text, cta_link) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE banner_image = VALUES(banner_image), tagline = VALUES(tagline), cta_text = VALUES(cta_text), cta_link = VALUES(cta_link)');
        $upsert->execute([$id, $bannerImage, $_POST['tagline'] ?: $data['description'], $_POST['cta_text'] ?: 'Shop This Category', $_POST['cta_link'] ?: '#products']);
    }

    private function createBanner(): void
    {
        $image = upload_image('image_path', 'banners');
        if (!$image) {
            throw new RuntimeException('Banner image is required.');
        }
        $stmt = $this->db->prepare('INSERT INTO banners (position, image_path, kicker, heading, subtext, button_text, button_link, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)');
        $stmt->execute([$_POST['position'] ?: 'home_hero', $image, $_POST['kicker'], $_POST['heading'], $_POST['subtext'], $_POST['button_text'], $_POST['button_link'], (int)$_POST['sort_order']]);
    }

    private function updateBanner(): void
    {
        $id = (int)$_POST['id'];
        $current = $this->findRow('banners', $id);
        if (!$current) {
            throw new RuntimeException('Banner not found.');
        }
        $image = upload_image('image_path', 'banners') ?: $current['image_path'];
        $stmt = $this->db->prepare('UPDATE banners SET position = ?, image_path = ?, kicker = ?, heading = ?, subtext = ?, button_text = ?, button_link = ?, sort_order = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$_POST['position'] ?: 'home_hero', $image, $_POST['kicker'], $_POST['heading'], $_POST['subtext'], $_POST['button_text'], $_POST['button_link'], (int)$_POST['sort_order'], !empty($_POST['is_active']) ? 1 : 0, $id]);
    }

    private function deleteRow(string $table, int $id): void
    {
        $allowed = ['products', 'categories', 'banners', 'coupons', 'quote_requests', 'discount_leads', 'contact_messages'];
        if (!in_array($table, $allowed, true) || $id <= 0) {
            throw new RuntimeException('Delete action is not allowed.');
        }
        if ($table === 'categories') {
            $count = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
            $count->execute([$id]);
            if ((int)$count->fetchColumn() > 0) {
                $stmt = $this->db->prepare('UPDATE categories SET is_active = 0 WHERE id = ?');
                $stmt->execute([$id]);
                return;
            }
        }
        if ($table === 'products') {
            $this->db->prepare('DELETE FROM product_images WHERE product_id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM product_variants WHERE product_id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM quantity_price_tiers WHERE product_id = ?')->execute([$id]);
        }
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function findRow(string $table, int $id): ?array
    {
        $allowed = ['products', 'categories', 'banners', 'coupons'];
        if (!in_array($table, $allowed, true) || $id <= 0) {
            return null;
        }
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    private function findCategoryWithBanner(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT c.*, cb.banner_image, cb.tagline, cb.cta_text, cb.cta_link FROM categories c LEFT JOIN category_banners cb ON cb.category_id = c.id WHERE c.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    private function discountLeads(): array
    {
        $where = [];
        $params = [];
        $search = trim((string)($_GET['q'] ?? ''));
        if ($search !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }
        if (!empty($_GET['date_from'])) {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $_GET['date_from'];
        }
        if (!empty($_GET['date_to'])) {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $_GET['date_to'];
        }
        $sql = 'SELECT * FROM discount_leads' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function contactMessages(): array
    {
        $where = [];
        $params = [];
        $search = trim((string)($_GET['q'] ?? ''));
        if ($search !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term, $term);
        }
        $sql = 'SELECT * FROM contact_messages' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function exportDiscountLeads(): never
    {
        $rows = $this->discountLeads();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="aa-packages-discount-leads-' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'wb');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Name', 'Email', 'Phone', 'Source', 'Submitted', 'Last Updated']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['name'], $row['email'], $row['phone'], $row['source'], $row['created_at'], $row['updated_at']]);
        }
        fclose($output);
        exit;
    }
}
