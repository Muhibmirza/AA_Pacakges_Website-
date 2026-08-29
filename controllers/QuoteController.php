<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Category.php';
require_once ROOT_PATH . '/models/QuoteRequest.php';

class QuoteController extends BaseController
{
    public function form(): void
    {
        $message = '';
        $error = '';
        if (is_post()) {
            verify_csrf();
            enforce_rate_limit('public', $_POST['email'] ?? '');
            try {
                $data = require_fields($_POST, [
                    'business_name' => ['required' => false, 'max' => 190, 'label' => 'Business name'],
                    'contact_name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Contact name'],
                    'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
                    'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                    'box_category' => ['required' => true, 'max' => 160, 'label' => 'Box category'],
                    'box_size' => ['required' => false, 'max' => 120, 'label' => 'Box size'],
                    'quantity' => ['required' => true, 'type' => 'int', 'min_value' => 1, 'max_value' => 1000000, 'label' => 'Quantity'],
                    'message' => ['required' => false, 'max' => 1200, 'label' => 'Message'],
                ]);
                $data['custom_printing'] = !empty($_POST['custom_printing']) ? 1 : 0;
                $logo = upload_image('logo_upload', 'quotes');
                (new QuoteRequest($this->db))->create($data, $logo);
                $message = 'Your quote request has been submitted. Our team will contact you shortly.';
            } catch (Throwable $e) {
                $error = $e instanceof InvalidArgumentException || $e instanceof RuntimeException ? $e->getMessage() : 'Could not submit your quote request.';
            }
        }
        $this->render('quote', ['categories' => (new Category($this->db))->all(), 'message' => $message, 'error' => $error]);
    }
}
