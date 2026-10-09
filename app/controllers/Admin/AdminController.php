<?php

/**
 * Admin Controller - Handles homepage
 */

class AdminController extends Controller
{
    public function __construct()
    {
        require_once __DIR__ . '/../../models/ApplicationModel.php';
    }

    public function index()
    {
        $this->setPageTitle('Admin Panel');
        $this->render('admin/admin');
    }

    /**
     * Return the admin sidebar menu (for AJAX/partial loading)
     */
    public function menu()
    {
        if (!$this->isAjax()) {
            $this->setPageTitle('Admin Panel');
            $this->render('admin/admin');
            return;
        }

        // Render without layout
        $this->render('admin/menu/menu', false);
    }

    /**
     * Return the requested admin main section (for AJAX/partial loading)
     *
     * @param string $page
     */
    public function main($page = 'dashboard')
    {
        $allowedPages = [
            'dashboard' => 'admin/main/dashboard_admin',
            'news' => 'admin/main/news/news_admin',
            'create-news' => 'admin/main/news/create-news',
            'recruitment' => 'admin/main/recruitment/recruitment_admin',
            'create-recruitment' => 'admin/main/recruitment/create-recruitment',
            'contact' => 'admin/main/contact_admin',
            'application' => 'admin/main/application_admin',
            'test-db' => 'admin/main/test_db',
            // Thêm các trang khác nếu cần



        ];

        $view = $allowedPages[$page] ?? $allowedPages['dashboard'];
        $data = [];

        if ($page === 'application') {
            $data['applications'] = (new ApplicationModel())->getAllApplications();
        }

        if (!$this->isAjax()) {
            $this->setPageTitle('Admin Panel');
            $data['admin_page'] = isset($allowedPages[$page]) ? $page : 'dashboard';
            $data['admin_view'] = $view;
            $this->render('admin/admin', $data);
            return;
        }

        // Render without layout
        $this->render($view, $data, false);
    }

    public function testDb()
    {
        $this->main('test-db');
    }
}
