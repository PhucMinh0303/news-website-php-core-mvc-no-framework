<?php
// controllers/AdminRecruitmentController.php
require_once __DIR__ . '/../models/RecruitmentModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

class AdminRecruitmentController extends Controller
{
    private $recruitmentModel;
    private $applicationModel;

    public function __construct()
    {
        $this->recruitmentModel = new RecruitmentModel();
        $this->applicationModel = new ApplicationModel();
    }

    /**
     * Kiểm tra đăng nhập admin
     */
    private function checkAdminAuth()
    {
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Tạo slug từ string
     */
    private function createSlug($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        return strtolower(trim($string));
    }

    /**
     * Xử lý upload ảnh
     */
    private function uploadImage($file, $existingImage = null)
    {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExt, $allowedExt)) {
                // Xóa ảnh cũ nếu có và không phải ảnh mặc định
                if ($existingImage && $existingImage !== 'default-job.webp' && file_exists($uploadDir . $existingImage)) {
                    unlink($uploadDir . $existingImage);
                }

                $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                    return $newFileName;
                }
            }
        }
        return $existingImage ?: 'default-job.webp';
    }

    /**
     * Admin: Danh sách tin tuyển dụng
     */
    public function index()
    {
        $this->checkAdminAuth();

        // Lấy các tham số từ request
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $status_filter = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
        $search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu từ model
        $recruitments = $this->recruitmentModel->getAll($status_filter, $limit, $offset, $search_keyword);
        $total_records = $this->recruitmentModel->count($status_filter, $search_keyword);
        $total_pages = ceil($total_records / $limit);

        // Truyền dữ liệu sang view
        $data = [
            'recruitments' => $recruitments,
            'status_filter' => $status_filter,
            'search_keyword' => $search_keyword,
            'page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records
        ];

        // Load view
        extract($data);
        include __DIR__ . '/../views/admin/recruitment/recruitment_admin.php';
    }

    /**
     * Admin: Form thêm tin tuyển dụng
     */
    public function create()
    {
        $this->checkAdminAuth();
        include __DIR__ . '/../views/admin/recruitment/add_recruitment.php';
    }

    /**
     * Admin: Xử lý thêm tin tuyển dụng
     */
    public function store()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/recruitment/create');
            return;
        }

        // Validate dữ liệu
        $errors = $this->validateRecruitmentData($_POST);
        if (!empty($errors)) {
            $_SESSION['admin_errors'] = $errors;
            header('Location: /admin/recruitment/create');
            return;
        }

        // Xử lý upload ảnh
        $image = $this->uploadImage($_FILES['image'] ?? null);

        // Tạo slug nếu không có
        $slug = !empty($_POST['slug']) ? $this->createSlug($_POST['slug']) : $this->createSlug($_POST['title']);

        $data = [
            ':title' => $_POST['title'],
            ':slug' => $slug,
            ':image' => $image,
            ':work_location' => $_POST['work_location'],
            ':degree' => $_POST['degree'] ?? 'Cao Đẳng - Đại Học',
            ':quantity' => (int)$_POST['quantity'],
            ':salary_range' => $_POST['salary_range'] ?? null,
            ':deadline' => $_POST['deadline'],
            ':description' => $_POST['description'] ?? null,
            ':requirements' => $_POST['requirements'] ?? null,
            ':benefits' => $_POST['benefits'] ?? null,
            ':status' => (int)$_POST['status']
        ];

        $id = $this->recruitmentModel->create($data);

        if ($id) {
            $_SESSION['admin_success'] = 'Đã thêm tin tuyển dụng thành công!';
            header('Location: /admin/recruitment');
        } else {
            $_SESSION['admin_error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
            header('Location: /admin/recruitment/create');
        }
    }

    /**
     * Admin: Form sửa tin tuyển dụng
     */
    public function edit($id)
    {
        $this->checkAdminAuth();

        $job = $this->recruitmentModel->findById($id);
        if (!$job) {
            $_SESSION['admin_error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: /admin/recruitment');
            return;
        }

        include __DIR__ . '/../views/admin/recruitment/edit_recruitment.php';
    }

    /**
     * Admin: Xử lý cập nhật tin tuyển dụng
     */
    public function update($id)
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/recruitment');
            return;
        }

        $job = $this->recruitmentModel->findById($id);
        if (!$job) {
            $_SESSION['admin_error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: /admin/recruitment');
            return;
        }

        // Validate dữ liệu
        $errors = $this->validateRecruitmentData($_POST);
        if (!empty($errors)) {
            $_SESSION['admin_errors'] = $errors;
            header("Location: /admin/recruitment/edit?id={$id}");
            return;
        }

        // Xử lý upload ảnh mới
        $image = $this->uploadImage($_FILES['image'] ?? null, $job['image']);

        $data = [
            ':title' => $_POST['title'],
            ':image' => $image,
            ':work_location' => $_POST['work_location'],
            ':degree' => $_POST['degree'] ?? 'Cao Đẳng - Đại Học',
            ':quantity' => (int)$_POST['quantity'],
            ':salary_range' => $_POST['salary_range'] ?? null,
            ':deadline' => $_POST['deadline'],
            ':description' => $_POST['description'] ?? null,
            ':requirements' => $_POST['requirements'] ?? null,
            ':benefits' => $_POST['benefits'] ?? null,
            ':status' => (int)$_POST['status']
        ];

        // Cập nhật slug nếu có thay đổi title
        if ($_POST['title'] !== $job['title']) {
            $data[':slug'] = $this->createSlug($_POST['title']);
        }

        $result = $this->recruitmentModel->update($id, $data);

        if ($result) {
            $_SESSION['admin_success'] = 'Đã cập nhật tin tuyển dụng thành công!';
        } else {
            $_SESSION['admin_error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Admin: Xóa tin tuyển dụng
     */
    public function delete($id)
    {
        $this->checkAdminAuth();

        $job = $this->recruitmentModel->findById($id);
        if (!$job) {
            $_SESSION['admin_error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: /admin/recruitment');
            return;
        }

        // Xóa ảnh nếu không phải ảnh mặc định
        if ($job['image'] && $job['image'] !== 'default-job.webp') {
            $imagePath = __DIR__ . '/../public/uploads/' . $job['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Xóa tất cả ứng tuyển liên quan
        $this->applicationModel->deleteByRecruitmentId($id);

        // Xóa tin tuyển dụng
        $result = $this->recruitmentModel->delete($id);

        if ($result) {
            $_SESSION['admin_success'] = 'Đã xóa tin tuyển dụng thành công!';
        } else {
            $_SESSION['admin_error'] = 'Có lỗi xảy ra khi xóa tin tuyển dụng!';
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Admin: Xem danh sách ứng viên của một tin tuyển dụng
     */
    public function applications($recruitmentId)
    {
        $this->checkAdminAuth();

        $job = $this->recruitmentModel->findById($recruitmentId);
        if (!$job) {
            $_SESSION['admin_error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: /admin/recruitment');
            return;
        }

        $applications = $this->applicationModel->getByRecruitmentId($recruitmentId);

        include __DIR__ . '/../views/admin/recruitment/applications.php';
    }

    /**
     * Admin: Xuất danh sách ứng viên ra Excel/CSV
     */
    public function exportApplications($recruitmentId)
    {
        $this->checkAdminAuth();

        $applications = $this->applicationModel->getByRecruitmentId($recruitmentId);
        $job = $this->recruitmentModel->findById($recruitmentId);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="ung_vien_' . $job['slug'] . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for UTF-8

        fputcsv($output, ['Họ tên', 'Email', 'Số điện thoại', 'Nội dung', 'CV', 'IP', 'Ngày ứng tuyển']);

        foreach ($applications as $app) {
            fputcsv($output, [
                $app['fullname'],
                $app['email'],
                $app['phone'],
                $app['content'],
                $app['cv_file'],
                $app['ip_address'],
                $app['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Validate dữ liệu tuyển dụng
     */
    private function validateRecruitmentData($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Tiêu đề không được để trống';
        }

        if (empty($data['work_location'])) {
            $errors[] = 'Địa điểm làm việc không được để trống';
        }

        if (empty($data['deadline'])) {
            $errors[] = 'Hạn nộp hồ sơ không được để trống';
        } elseif (strtotime($data['deadline']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Hạn nộp hồ sơ phải lớn hơn hoặc bằng ngày hiện tại';
        }

        if (isset($data['quantity']) && $data['quantity'] < 1) {
            $errors[] = 'Số lượng tuyển phải lớn hơn 0';
        }

        return $errors;
    }
}