<?php
/**
 * Admin Controller - Handles homepage
 */

class AdminController extends Controller {
    
    public function index() {
        $this->setPageTitle('Admin Panel');
        $this->render('admin/admin');
    }

    /**
     * Return the admin sidebar menu (for AJAX/partial loading)
     */
    public function menu() {
        // Render without layout
        $this->render('admin/menu/menu', false);
    }

    /**
     * Return the requested admin main section (for AJAX/partial loading)
     *
     * @param string $page
     */
    public function main($page = 'dashboard') {
        $allowedPages = [
            'dashboard' => 'admin/main/dashboard_admin',
            'articles' => 'admin/main/articles_admin',
            'add-articles' => 'admin/main/post/add_articles',
            'add-recruitment' => 'admin/main/post/add_recruitment',
            'recruitment' => 'admin/main/recruitment_admin',
            'contact' => 'admin/main/contact_admin',
            // Add other pages here as needed
        ];

        $view = $allowedPages[$page] ?? $allowedPages['dashboard'];

        // Render without layout
        $this->render($view, false);
    }
}
?>
