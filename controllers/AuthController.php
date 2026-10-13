<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/User.php';

class AuthController extends BaseController
{
    public function login(): void
    {
        $error = '';
        if (is_post()) {
            verify_csrf();
            $loginValue = trim($_POST['login'] ?? '');
            enforce_rate_limit('auth', $loginValue);
            if (too_many_attempts('customer_login')) {
                $error = 'Too many login attempts. Please wait and try again.';
            } else {
                $user = (new User($this->db))->findForLogin($loginValue);
                if ($user && $user['status'] === 'active' && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    clear_attempts('customer_login');
                    clear_rate_limit('auth', $loginValue);
                    redirect(route('account'));
                }
                record_attempt('customer_login');
                $error = 'Invalid login details.';
            }
        }
        $this->render('login', ['error' => $error]);
    }

    public function register(): void
    {
        $error = '';
        if (is_post()) {
            verify_csrf();
            enforce_rate_limit('auth', $_POST['email'] ?? '');
            try {
                $data = require_fields($_POST, [
                    'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Name'],
                    'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                    'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
                    'password' => ['required' => true, 'min' => 8, 'max' => 72, 'label' => 'Password'],
                    'address' => ['required' => false, 'max' => 600, 'label' => 'Address'],
                ]);
                $userId = (new User($this->db))->create($data);
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $data['name'];
                clear_rate_limit('auth', $data['email']);
                redirect(route('account'));
            } catch (Throwable $e) {
                $error = $e instanceof InvalidArgumentException ? $e->getMessage() : 'Could not create account. The email or phone may already be registered.';
            }
        }
        $this->render('register', ['error' => $error]);
    }

    public function logout(): never
    {
        unset($_SESSION['user_id'], $_SESSION['user_name']);
        session_regenerate_id(true);
        redirect('index.php');
    }

    public function account(): void
    {
        customer_guard();
        $userModel = new User($this->db);
        $userId = (int)$_SESSION['user_id'];
        $message = '';
        $error = '';
        if (is_post()) {
            verify_csrf();
            enforce_rate_limit('user', (string)$userId);
            try {
                if (($_POST['action'] ?? '') === 'update_profile') {
                    $data = require_fields($_POST, [
                        'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Name'],
                        'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                        'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
                        'address' => ['required' => false, 'max' => 600, 'label' => 'Address'],
                    ]);
                    $userModel->updateProfile($userId, $data);
                    $_SESSION['user_name'] = $data['name'];
                    $message = 'Profile updated successfully.';
                }
                if (($_POST['action'] ?? '') === 'change_password') {
                    $data = require_fields($_POST, [
                        'current_password' => ['required' => true, 'min' => 8, 'max' => 72, 'label' => 'Current password'],
                        'new_password' => ['required' => true, 'min' => 8, 'max' => 72, 'label' => 'New password'],
                    ]);
                    if (!$userModel->changePassword($userId, $data['current_password'], $data['new_password'])) {
                        throw new InvalidArgumentException('Current password is incorrect.');
                    }
                    $message = 'Password changed successfully.';
                }
            } catch (Throwable $e) {
                $error = $e instanceof InvalidArgumentException ? $e->getMessage() : 'Could not save changes.';
            }
        }
        $tab = $_GET['tab'] ?? 'orders';
        $orders = $userModel->orders($userId);
        $orderDetail = !empty($_GET['order']) ? $userModel->orderDetail($userId, (int)$_GET['order']) : null;
        $this->render('account', [
            'orders' => $orders,
            'user' => $userModel->find($userId),
            'tab' => $tab,
            'orderDetail' => $orderDetail,
            'message' => $message,
            'error' => $error,
        ]);
    }
}
