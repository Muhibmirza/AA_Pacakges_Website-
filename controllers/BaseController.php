<?php
declare(strict_types=1);

abstract class BaseController
{
    protected PDO $db;
    protected array $settings;

    public function __construct(PDO $db, array $settings = [])
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $settings = $this->settings;
        $pdo = $this->db;
        require ROOT_PATH . '/views/layouts/header.php';
        require ROOT_PATH . '/views/pages/' . $view . '.php';
        require ROOT_PATH . '/views/layouts/footer.php';
    }

    protected function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
