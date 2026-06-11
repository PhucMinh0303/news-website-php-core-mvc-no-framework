<?php
// controllers/admin/AdminRecruitmentController.php
require_once __DIR__ . '/../../models/RecruitmentModel.php';

class AdminRecruitmentController extends Controller
{
    private $recruitmentModel;

    public function __construct()
    {
        $this->recruitmentModel = new RecruitmentModel();
    }

    /**
     * Dashboard - Hiển thị thống kê và danh sách tin tuyển dụng (Admin)
     */
    public function index()
    {
        // Lấy thống kê số lượng theo trạng thái
        $stats = [
            'active' => $this->recruitmentModel->countAdmin('1'),   // Đang đăng
            'drafts' => $this->recruitmentModel->countAdmin('0'),   // Bản nháp
            'closed' => $this->recruitmentModel->countAdmin('2'),   // Đã đóng
            'total' => $this->recruitmentModel->countAdmin(),       // Tổng số
            'applicants' => 0
        ];

        // Lấy danh sách tin tuyển dụng
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $jobs = $this->recruitmentModel->getAllAdmin($status, $limit, $offset, $search);
        $total = $this->recruitmentModel->countAdmin($status, $search);
        $totalPages = ceil($total / $limit);

        $data = [
            'stats' => $stats,
            'recruitments' => $jobs,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $total,
            'status_filter' => $status,
            'search_keyword' => $search,
            'page' => $page
        ];

        $this->view('admin/main/recruitment/recruitment_admin', $data);
    }

    /**
     * Form tạo tin tuyển dụng mới
     */
    public function create()
    {
        $this->view('admin/main/recruitment/create');
    }

    /**
     * Validate dữ liệu đầu vào
     */
    private function validateData($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Tiêu đề không được để trống';
        } elseif (strlen($data['title']) < 5) {
            $errors[] = 'Tiêu đề phải có ít nhất 5 ký tự';
        }

        if (empty($data['work_location'])) {
            $errors[] = 'Địa điểm làm việc không được để trống';
        }

        if (empty($data['degree'])) {
            $errors[] = 'Trình độ yêu cầu không được để trống';
        }

        if (empty($data['quantity']) || $data['quantity'] < 1) {
            $errors[] = 'Số lượng cần tuyển phải lớn hơn 0';
        }

        if (empty($data['salary_range'])) {
            $errors[] = 'Mức lương không được để trống';
        }

        if (empty($data['deadline'])) {
            $errors[] = 'Hạn nộp hồ sơ không được để trống';
        } elseif (strtotime($data['deadline']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Hạn nộp hồ sơ phải từ hôm nay trở đi';
        }

        if (empty($data['description'])) {
            $errors[] = 'Mô tả công việc không được để trống';
        }

        if (empty($data['requirements'])) {
            $errors[] = 'Yêu cầu ứng viên không được để trống';
        }

        if (empty($data['benefits'])) {
            $errors[] = 'Quyền lợi được hưởng không được để trống';
        }

        return $errors;
    }

    /**
     * Xử lý lưu tin tuyển dụng mới từ create.php
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/recruitment/recruitment_admin');
            return;
        }

        // Validate dữ liệu
        $errors = $this->validateData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /admin/recruitment/create');
            return;
        }

        // Xử lý upload ảnh
        $imageName = 'default-job.webp';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage['success']) {
                $imageName = $uploadedImage['filename'];
            } else {
                $_SESSION['errors'] = [$uploadedImage['error']];
                $_SESSION['old_input'] = $_POST;
                header('Location: /admin/recruitment/create');
                return;
            }
        }

        // Xác định trạng thái dựa trên action từ form
        $status = 0; // Mặc định draft
        if (isset($_POST['publish']) && $_POST['publish'] == 1) {
            $status = 1;
        }
        if (isset($_POST['save_draft']) && $_POST['save_draft'] == 0) {
            $status = 0;
        }

        $data = [
            'title' => trim($_POST['title']),
            'image' => $imageName,
            'work_location' => trim($_POST['work_location']),
            'degree' => $_POST['degree'],
            'work_type' => $_POST['work_type'] ?? 'Toàn thời gian',
            'quantity' => (int)$_POST['quantity'],
            'salary_range' => $_POST['salary_display'] ?? $_POST['salary_range'] ?? '',
            'deadline' => $_POST['deadline'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'benefits' => $_POST['benefits'],
            'status' => $status
        ];

        $result = $this->recruitmentModel->create($data);

        if ($result) {
            $_SESSION['success'] = 'Thêm tin tuyển dụng "' . htmlspecialchars($data['title']) . '" thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm tin tuyển dụng, vui lòng thử lại!';
        }

        // Xử lý "Lưu và tiếp tục" - quay lại form edit
        if (isset($_POST['save_and_continue']) && $_POST['save_and_continue'] == 1 && $result) {
            header('Location: /admin/recruitment/edit?id=' . $result);
            return;
        }

        header('Location: /admin/recruitment/recruitment_admin');
    }

    /**
     * Form chỉnh sửa tin tuyển dụng
     */
    public function edit($id)
    {
        $job = $this->recruitmentModel->getById($id);

        if (!$job) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        $this->view('admin/main/recruitment/edit', ['job' => $job]);
    }

    /**
     * Xử lý cập nhật tin tuyển dụng
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/recruitment');
            return;
        }

        $job = $this->recruitmentModel->getById($id);
        if (!$job) {
            $_SESSION['error'] = 'Tin tuyển dụng không tồn tại!';
            header('Location: /admin/recruitment');
            return;
        }

        // Validate dữ liệu
        $errors = $this->validateData($_POST);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/recruitment/edit?id=' . $id);
            return;
        }

        // Xử lý upload ảnh mới
        $imageName = $job['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage['success']) {
                $imageName = $uploadedImage['filename'];
                // Xóa ảnh cũ nếu không phải ảnh mặc định
                if ($job['image'] !== 'default-job.webp') {
                    $oldImagePath = __DIR__ . '/../../public/uploads/recruitments/' . $job['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
        }

        $data = [
            'title' => trim($_POST['title']),
            'image' => $imageName,
            'work_location' => trim($_POST['work_location']),
            'degree' => $_POST['degree'],
            'work_type' => $_POST['work_type'] ?? 'Toàn thời gian',
            'quantity' => (int)$_POST['quantity'],
            'salary_range' => $_POST['salary_display'] ?? $_POST['salary_range'] ?? $job['salary_range'],
            'deadline' => $_POST['deadline'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'benefits' => $_POST['benefits'],
            'status' => (int)$_POST['status']
        ];

        $result = $this->recruitmentModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật tin tuyển dụng "' . htmlspecialchars($data['title']) . '" thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Xóa tin tuyển dụng
     */
    public function destroy($id)
    {
        $job = $this->recruitmentModel->getById($id);

        if ($job) {
            // Xóa ảnh nếu không phải ảnh mặc định
            if ($job['image'] !== 'default-job.webp') {
                $imagePath = __DIR__ . '/../../public/uploads/recruitments/' . $job['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $result = $this->recruitmentModel->delete($id);
            $_SESSION['success'] = $result ? 'Xóa tin tuyển dụng "' . htmlspecialchars($job['title']) . '" thành công!' : 'Xóa tin tuyển dụng thất bại!';
        } else {
            $_SESSION['error'] = 'Tin tuyển dụng không tồn tại!';
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Cập nhật trạng thái (bật/tắt)
     */
    public function toggleStatus($id)
    {
        $job = $this->recruitmentModel->getById($id);

        if ($job) {
            $newStatus = $job['status'] == 1 ? 0 : 1;
            $result = $this->recruitmentModel->updateStatus($id, $newStatus);

            if ($result) {
                $message = $newStatus == 1 ? 'Đã bật tin tuyển dụng "' . htmlspecialchars($job['title']) . '"!' : 'Đã tắt tin tuyển dụng "' . htmlspecialchars($job['title']) . '"!';
                $_SESSION['success'] = $message;
            } else {
                $_SESSION['error'] = 'Cập nhật trạng thái thất bại!';
            }
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Upload ảnh
     */
    private function uploadImage($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/recruitments/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Kiểm tra kích thước file (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'filename' => 'default-job.webp', 'error' => 'File ảnh không được vượt quá 2MB'];
        }

        // Kiểm tra loại file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'filename' => 'default-job.webp', 'error' => 'Chỉ chấp nhận file JPG, PNG, WEBP'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'filename' => 'default-job.webp', 'error' => 'Upload ảnh thất bại'];
    }
}