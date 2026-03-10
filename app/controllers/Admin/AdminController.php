<?php
/**
 * Admin Controller - Handles homepage
 */

class AdminController extends Controller {
    
    public function index() {
        $this->setPageTitle('Admin Panel');
        
        $this->render('admin/admin');
        
    }
}
?>
