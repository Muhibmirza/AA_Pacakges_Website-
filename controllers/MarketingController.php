<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/DiscountLead.php';
require_once ROOT_PATH . '/models/ContactMessage.php';

class MarketingController extends BaseController
{
    public function discountLead(): never
    {
        apply_cors();
        if (!is_post()) {
            $this->json(['ok' => false, 'message' => 'Method not allowed.'], 405);
        }
        verify_csrf();
        enforce_rate_limit('public', strtolower(trim((string)($_POST['email'] ?? ''))));

        try {
            $data = require_fields($_POST, [
                'name' => ['required' => false, 'max' => 160, 'label' => 'Name'],
                'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
            ]);
            $result = (new DiscountLead($this->db))->subscribe($data);
            $message = $result['duplicate']
                ? 'You are already registered. Your WELCOME5 discount is still active.'
                : 'Thank you! Use code WELCOME5 for 5% off your first order.';
            $this->json(['ok' => true, 'duplicate' => $result['duplicate'], 'code' => 'WELCOME5', 'message' => $message]);
        } catch (InvalidArgumentException $exception) {
            $this->json(['ok' => false, 'message' => $exception->getMessage()], 422);
        } catch (Throwable $exception) {
            error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
            $this->json(['ok' => false, 'message' => 'We could not save your details right now. Please try again.'], 500);
        }
    }

    public function contactMessage(): never
    {
        apply_cors();
        if (!is_post()) {
            $this->json(['ok' => false, 'message' => 'Method not allowed.'], 405);
        }
        verify_csrf();
        enforce_rate_limit('public', strtolower(trim((string)($_POST['email'] ?? ''))));

        try {
            $data = require_fields($_POST, [
                'name' => ['required' => true, 'min' => 2, 'max' => 160, 'label' => 'Name'],
                'email' => ['required' => true, 'type' => 'email', 'max' => 190, 'label' => 'Email'],
                'phone' => ['required' => true, 'type' => 'phone', 'max' => 40, 'label' => 'Phone'],
                'message' => ['required' => true, 'min' => 10, 'max' => 3000, 'label' => 'Message'],
            ]);
            (new ContactMessage($this->db))->create($data);
            $this->json(['ok' => true, 'message' => 'Thank you. Your message has been received and our team will contact you soon.']);
        } catch (InvalidArgumentException $exception) {
            $this->json(['ok' => false, 'message' => $exception->getMessage()], 422);
        } catch (Throwable $exception) {
            error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
            $this->json(['ok' => false, 'message' => 'We could not send your message right now. Please try again.'], 500);
        }
    }
}
