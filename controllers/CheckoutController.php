<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Order.php';

class CheckoutController extends BaseController
{
    public function checkout(): void
    {
        $cartController = new CartController($this->db, $this->settings);
        if (!$cartController->cart()) {
            redirect(route('cart'));
        }
        $error = '';
        if (is_post()) {
            verify_csrf();
            enforce_rate_limit('user', $_SESSION['user_id'] ?? ($_POST['email'] ?? 'guest'));
            try {
                $data = require_fields($_POST, [
                    'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Name'],
                    'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
                    'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                    'business_name' => ['required' => false, 'max' => 190, 'label' => 'Business name'],
                    'address' => ['required' => true, 'min' => 8, 'max' => 800, 'label' => 'Address'],
                    'city' => ['required' => true, 'min' => 2, 'max' => 100, 'label' => 'City'],
                    'area' => ['required' => true, 'min' => 2, 'max' => 100, 'label' => 'Area'],
                    'notes' => ['required' => false, 'max' => 800, 'label' => 'Notes'],
                ]);
                $method = $_POST['payment_method'] ?? 'cash_on_delivery';
                $allowedMethods = ['cash_on_delivery','visa','mastercard','debit_card','jazzcash','easypaisa','bank_transfer'];
                if (!in_array($method, $allowedMethods, true)) {
                    throw new InvalidArgumentException('Invalid payment method.');
                }
                $orderId = (new Order($this->db))->create($data, $cartController->cart(), $method, $_SESSION['user_id'] ?? null, $cartController->totals());
                $_SESSION['cart'] = [];
                unset($_SESSION['coupon']);
                redirect(route('confirmation', ['id' => $orderId]));
            } catch (Throwable $e) {
                $error = $e instanceof InvalidArgumentException ? $e->getMessage() : 'We could not place the order. Please review your details and try again.';
            }
        }
        $this->render('checkout', ['cart' => $cartController->cart(), 'totals' => $cartController->totals(), 'error' => $error, 'paymentConfig' => require ROOT_PATH . '/includes/payment-config.php']);
    }

    public function confirmation(): void
    {
        $order = (new Order($this->db))->find((int)($_GET['id'] ?? 0));
        $this->render('confirmation', ['order' => $order]);
    }
}
