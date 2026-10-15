<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Category.php';
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Content.php';
require_once ROOT_PATH . '/models/Order.php';

class FrontendController extends BaseController
{
    public function home(): void
    {
        $content = new Content($this->db);
        $categories = (new Category($this->db))->all();
        $products = (new Product($this->db))->featured(8);
        $this->render('home', [
            'banners' => $content->banners(),
            'categories' => $categories,
            'products' => $products,
            'uspBlocks' => $content->uspBlocks(),
            'testimonials' => $content->testimonials(),
        ]);
    }

    public function shop(): void
    {
        $categoryModel = new Category($this->db);
        $productModel = new Product($this->db);
        $category = null;
        $filters = ['sort' => $_GET['sort'] ?? '', 'q' => $_GET['q'] ?? ''];
        if (!empty($_GET['category'])) {
            $category = $categoryModel->findBySlug((string)$_GET['category']);
            if ($category) {
                $filters['category_id'] = $category['id'];
            }
        }
        $this->render('shop', [
            'categories' => $categoryModel->all(),
            'category' => $category,
            'products' => $productModel->all($filters, 24, 0),
            'sort' => $filters['sort'],
        ]);
    }

    public function product(): void
    {
        $slug = (string)($_GET['slug'] ?? '');
        $productModel = new Product($this->db);
        $product = $productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->render('static', ['page' => ['title' => 'Product not found', 'content' => '<p>The requested product is unavailable.</p>']]);
            return;
        }
        $related = $productModel->all(['category_id' => $product['category_id']], 4, 0);
        $this->render('product', ['product' => $product, 'related' => $related]);
    }

    public function staticPage(string $slug): void
    {
        $page = (new Content($this->db))->page($slug);
        if (!$page) {
            http_response_code(404);
            $page = ['title' => 'Page not found', 'content' => '<p>This page has not been created yet.</p>'];
        }
        $this->render('static', ['page' => $page]);
    }

    public function about(): void
    {
        $page = (new Content($this->db))->page('about') ?: [];
        $stats = [
            'products' => (int)$this->db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn(),
            'categories' => (int)$this->db->query('SELECT COUNT(*) FROM categories WHERE is_active = 1')->fetchColumn(),
        ];
        $this->render('about', ['stats' => $stats, 'page' => $page]);
    }

    public function contact(): void
    {
        $page = (new Content($this->db))->page('contact') ?: [];
        $this->render('contact', ['page' => $page]);
    }

    public function track(): void
    {
        $order = null;
        $message = '';
        if (is_post()) {
            verify_csrf();
            $order = (new Order($this->db))->findForTracking(trim($_POST['order_no'] ?? ''), trim($_POST['phone'] ?? ''));
            $message = $order ? '' : 'No matching order found for that Order ID and phone number.';
        }
        $this->render('track', ['order' => $order, 'message' => $message]);
    }

    public function sitemap(): never
    {
        $urls = [
            ['loc' => absolute_url(route()), 'priority' => '1.0'],
            ['loc' => absolute_url(route('shop')), 'priority' => '0.9'],
            ['loc' => absolute_url(route('about')), 'priority' => '0.7'],
            ['loc' => absolute_url(route('contact')), 'priority' => '0.7'],
            ['loc' => absolute_url(route('quote')), 'priority' => '0.8'],
            ['loc' => absolute_url(route('faqs')), 'priority' => '0.6'],
            ['loc' => absolute_url(route('terms')), 'priority' => '0.3'],
            ['loc' => absolute_url(route('privacy')), 'priority' => '0.3'],
            ['loc' => absolute_url(route('returns')), 'priority' => '0.3'],
        ];
        foreach ((new Category($this->db))->all() as $category) {
            $urls[] = ['loc' => absolute_url(route('category', ['slug' => $category['slug']])), 'priority' => '0.8'];
        }
        $products = $this->db->query('SELECT slug, created_at FROM products WHERE is_active = 1 ORDER BY id')->fetchAll();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => absolute_url(route('product', ['slug' => $product['slug']])),
                'priority' => '0.8',
                'lastmod' => date('Y-m-d', strtotime($product['created_at'])),
            ];
        }

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            echo '    <priority>' . $url['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
        exit;
    }

    public function robots(): never
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo 'Disallow: /' . ADMIN_PATH . "/\n";
        echo "Disallow: /cart\nDisallow: /checkout\nDisallow: /account\n";
        echo 'Sitemap: ' . absolute_url(url('sitemap.xml')) . "\n";
        exit;
    }
}

