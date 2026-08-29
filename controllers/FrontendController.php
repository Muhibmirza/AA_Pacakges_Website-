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
}

