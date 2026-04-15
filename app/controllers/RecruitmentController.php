<?php
// controllers/RecruitmentController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/RecruitmentModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

class RecruitmentController extends Controller
{
    private $recruitmentModel;
    private $applicationModel;

    public function __construct()
    {
        $this->recruitmentModel = new RecruitmentModel();
        $this->applicationModel = new ApplicationModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Admin: Danh sách tuyển dụng
     */
    public function adminIndex()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        // Lấy các tham số filter
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $status_filter = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
        $search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu từ model
        $recruitments = $this->recruitmentModel->getAll($status_filter, $search_keyword, $limit, $offset);
        $total_records = $this->recruitmentModel->getTotal($status_filter, $search_keyword);
        $total_pages = ceil($total_records / $limit);

        $data = [
            'recruitments' => $recruitments,
            'status_filter' => $status_filter,
            'search_keyword' => $search_keyword,
            'page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records,
            'title' => 'Quản lý tuyển dụng - Admin'
        ];

        $this->view('admin/recruitment_admin', $data);
    }

    /**
     * Admin: Xóa recruitment
     */
    public function adminDelete()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            // Lấy thông tin để xóa ảnh nếu cần
            $recruitment = $this->recruitmentModel->getById($id);

            if ($this->recruitmentModel->delete($id)) {
                // Xóa file ảnh nếu không phải default
                if ($recruitment && $recruitment['image'] && $recruitment['image'] != 'default-job.webp') {
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/uploads/' . $recruitment['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $_SESSION['admin_success'] = 'Xóa tin tuyển dụng thành công!';
            } else {
                $_SESSION['admin_error'] = 'Có lỗi xảy ra khi xóa tin tuyển dụng!';
            }
        }

        $this->redirect('/admin/recruitment');
    }

    /**
     * Admin: Sửa recruitment (hiển thị form)
     */
    public function adminEdit()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $recruitment = $this->recruitmentModel->getById($id);

        if (!$recruitment) {
            $_SESSION['admin_error'] = 'Không tìm thấy tin tuyển dụng!';
            $this->redirect('/admin/recruitment');
            return;
        }

        $data = [
            'recruitment' => $recruitment,
            'title' => 'Sửa tin tuyển dụng - Admin'
        ];

        $this->view('admin/recruitment_edit', $data);
    }

    /**
     * Admin: Cập nhật recruitment
     */
    public function adminUpdate()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/recruitment');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'work_location' => trim($_POST['work_location'] ?? ''),
            'degree' => trim($_POST['degree'] ?? 'Cao Đẳng - Đại Học'),
            'quantity' => (int)($_POST['quantity'] ?? 1),
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'deadline' => $_POST['deadline'] ?? '',
            'description' => $_POST['description'] ?? '',
            'requirements' => $_POST['requirements'] ?? '',
            'benefits' => $_POST['benefits'] ?? '',
            'status' => (int)($_POST['status'] ?? 1)
        ];

        // Xử lý upload ảnh mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $this->recruitmentModel->uploadImage($_FILES['image']);
            if ($image) {
                $data['image'] = $image;

                // Xóa ảnh cũ
                $old = $this->recruitmentModel->getById($id);
                if ($old && $old['image'] && $old['image'] != 'default-job.webp') {
                    $oldPath = $_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/uploads/' . $old['image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        }

        if ($this->recruitmentModel->update($id, $data)) {
            $_SESSION['admin_success'] = 'Cập nhật tin tuyển dụng thành công!';
        } else {
            $_SESSION['admin_error'] = 'Có lỗi xảy ra khi cập nhật!';
        }

        $this->redirect('/admin/recruitment');
    }

    /**
     * Admin: Thêm mới recruitment (hiển thị form)
     */
    public function adminAdd()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        $data = [
            'title' => 'Thêm tin tuyển dụng - Admin'
        ];

        $this->view('admin/recruitment_add', $data);
    }

    /**
     * Admin: Lưu recruitment mới
     */
    public function adminStore()
    {
        // Kiểm tra đăng nhập admin
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/recruitment');
            return;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'work_location' => trim($_POST['work_location'] ?? ''),
            'degree' => trim($_POST['degree'] ?? 'Cao Đẳng - Đại Học'),
            'quantity' => (int)($_POST['quantity'] ?? 1),
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'deadline' => $_POST['deadline'] ?? '',
            'description' => $_POST['description'] ?? '',
            'requirements' => $_POST['requirements'] ?? '',
            'benefits' => $_POST['benefits'] ?? '',
            'status' => (int)($_POST['status'] ?? 1)
        ];

        // Xử lý upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $this->recruitmentModel->uploadImage($_FILES['image']);
            if ($image) {
                $data['image'] = $image;
            }
        }

        if ($this->recruitmentModel->create($data)) {
            $_SESSION['admin_success'] = 'Thêm tin tuyển dụng thành công!';
        } else {
            $_SESSION['admin_error'] = 'Có lỗi xảy ra khi thêm mới!';
        }

        $this->redirect('/admin/recruitment');
    }

    /**
     * Kiểm tra admin đã đăng nhập
     */
    private function isAdminLoggedIn()
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    // ========== CÁC METHOD CHO FRONTEND ==========

    /**
     * Trang danh sách tuyển dụng (frontend)
     */
    public function index()
    {
        $this->setPageTitle('Tuyển dụng - Capital AM');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $recruitments = $this->recruitmentModel->getActiveRecruitments($limit, $offset);
        $total = $this->recruitmentModel->countActive();
        $totalPages = ceil($total / $limit);
        $featuredRecruitments = $this->recruitmentModel->getFeaturedRecruitments(5);
        $positions = $this->recruitmentModel->getAllPositions();

        $data = [
            'recruitments' => $recruitments,
            'featuredRecruitments' => $featuredRecruitments,
            'positions' => $positions,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'title' => 'Tuyển dụng - Capital AM'
        ];

        $this->view('recruitment/recruitment', $data);
    }

    /**
     * Chi tiết tuyển dụng (frontend)
     */
    public function detail($slug)
    {
        $recruitment = $this->recruitmentModel->getBySlug($slug);

        if (!$recruitment) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        $this->recruitmentModel->incrementViews($recruitment['id']);

        $relatedRecruitments = $this->recruitmentModel->getByPosition($recruitment['degree'], 4);
        $relatedRecruitments = array_filter($relatedRecruitments, function ($item) use ($recruitment) {
            return $item['id'] != $recruitment['id'];
        });

        $currentUrl = $this->getCurrentUrl();

        $successMessage = $_SESSION['apply_success'] ?? null;
        $errorMessage = $_SESSION['apply_error'] ?? null;
        $errors = $_SESSION['apply_errors'] ?? null;
        $oldData = $_SESSION['apply_data'] ?? null;

        unset($_SESSION['apply_success'], $_SESSION['apply_error'], $_SESSION['apply_errors'], $_SESSION['apply_data']);

        $data = [
            'recruitment' => $recruitment,
            'relatedRecruitments' => $relatedRecruitments,
            'currentUrl' => $currentUrl,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'errors' => $errors,
            'oldData' => $oldData,
            'title' => $recruitment['title'] . ' - Capital AM'
        ];

        $this->view('recruitment/recruitment-title', $data);
    }

    /**
     * Ứng tuyển
     */
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/recruitment');
            return;
        }

        // Xử lý apply (giữ nguyên code cũ)
        // ... code xử lý apply ...
    }

    private function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}

?>