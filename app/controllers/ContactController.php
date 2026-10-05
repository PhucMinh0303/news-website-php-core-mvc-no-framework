<?php

/**
 * Contact Controller - Handles contact pages
 */

class ContactController extends Controller
{

    public function index()
    {
        if ($this->isPost()) {
            $this->send();
            return;
        }

        $this->setPageTitle('Liên hệ');
        $this->render('Contact/contact');
    }

    public function send()
    {
        if (!$this->isPost()) {
            $this->redirect('contact');
        }

        $name = trim((string) $this->post('ten', $this->post('name', '')));
        $phone = trim((string) $this->post('dt', $this->post('phone', '')));
        $email = trim((string) $this->post('email', ''));
        $content = trim((string) $this->post('noidung', $this->post('message', '')));
        $phoneDigits = preg_replace('/\D+/', '', $phone);
        $errors = [];

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Vui lòng nhập họ tên (không quá 100 ký tự).';
        }
        if (strlen($phoneDigits) < 9 || strlen($phoneDigits) > 15) {
            $errors[] = 'Vui lòng nhập số điện thoại hợp lệ.';
        }
        if ($email !== '' && !$this->validateEmail($email)) {
            $errors[] = 'Email không hợp lệ.';
        }
        if ($content === '') {
            $errors[] = 'Vui lòng nhập nội dung liên hệ.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = [
                'ten' => $name,
                'dt' => $phone,
                'email' => $email,
                'noidung' => $content,
            ];
            $this->redirect('contact');
        }

        try {
            $contactId = (new ContactModel())->addContact([
                'customer_name' => $name,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'content' => $content,
                'contact_type' => 'general',
                'source' => 'website',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'page_url' => $_SERVER['HTTP_REFERER'] ?? null,
                'referrer_url' => $_SERVER['HTTP_REFERER'] ?? null,
            ]);

            if (!$contactId) {
                throw new RuntimeException('Contact insert did not return an ID.');
            }

            $_SESSION['success'] = 'Cảm ơn bạn đã liên hệ với chúng tôi!';
        } catch (Throwable $exception) {
            error_log('Unable to save contact: ' . $exception->getMessage());
            $_SESSION['errors'] = ['Gửi liên hệ chưa thành công. Vui lòng thử lại sau.'];
            $_SESSION['old_input'] = [
                'ten' => $name,
                'dt' => $phone,
                'email' => $email,
                'noidung' => $content,
            ];
        }

        $this->redirect('contact');
    }
}
