<?php
/**
 * Contact Controller - Handles contact pages
 */

class ContactController extends Controller {
    
    public function index() {
        $this->setPageTitle('Liên hệ');
        $this->render('Contact/contact');
    }
    
    public function send() {
        if (!$this->isPost()) {
            $this->redirect('contact');
        }
        
        // Get and sanitize form data
        $name = $this->sanitize($this->post('name'));
        $email = $this->sanitize($this->post('email'), 'email');
        $subject = $this->sanitize($this->post('subject'));
        $message = $this->sanitize($this->post('message'));
        
        // Validate email
        if (!$this->validateEmail($email)) {
            $_SESSION['error'] = 'Email không hợp lệ';
            $this->redirect('contact');
        }
        
        // Here you would save to database or send email
        // For now, just set success message
        $_SESSION['success'] = 'Cảm ơn bạn đã liên hệ với chúng tôi!';
        
        $this->redirect('contact');
    }
}

?>
