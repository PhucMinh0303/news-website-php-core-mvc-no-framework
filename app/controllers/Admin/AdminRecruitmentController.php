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
     * Danh sách tin tuyển dụng (Admin)
     */
    public function index()
    {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $jobs = $this->recruitmentModel->getAllAdmin($status, $limit, $offset, $search);
        $total = $this->recruitmentModel->countAdmin($status, $search);
        $totalPages = ceil($total / $limit);

        $data = [
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
     * Form tạo tin tuyển dụng
     */
    public function create()
    {
        $this->view('admin/main/recruitment/create');
    }

    /**
     * Xử lý lưu tin tuyển dụng mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/main/recruitment/store');
            return;
        }

        $errors = $this->validateRecruitmentData($_POST);

        // Xử lý upload ảnh
        $imageName = 'default-job.webp';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage['success']) {
                $imageName = $uploadedImage['filename'];
            } else {
                $errors[] = $uploadedImage['error'];
            }
        }

        if (empty($errors)) {
            $data = [
                'title' => $_POST['title'],
                'image' => $imageName,
                'work_location' => $_POST['work_location'],
                'degree' => $_POST['degree'],
                'quantity' => (int)$_POST['quantity'],
                'salary_range' => $_POST['salary_range'],
                'deadline' => $_POST['deadline'],
                'description' => $_POST['description'],
                'requirements' => $_POST['requirements'],
                'benefits' => $_POST['benefits'],
                'status' => (int)$_POST['status']
            ];

            $result = $this->recruitmentModel->create($data);

            if ($result) {
                $_SESSION['success'] = 'Thêm tin tuyển dụng thành công!';
                header('Location: /admin/main/recruitment/store');
                return;
            } else {
                $errors[] = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: /admin/main/recruitment/store');
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

        $errors = $this->validateRecruitmentData($_POST, true);

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
            } else {
                $errors[] = $uploadedImage['error'];
            }
        }

        if (empty($errors)) {
            $data = [
                'title' => $_POST['title'],
                'image' => $imageName,
                'work_location' => $_POST['work_location'],
                'degree' => $_POST['degree'],
                'quantity' => (int)$_POST['quantity'],
                'salary_range' => $_POST['salary_range'],
                'deadline' => $_POST['deadline'],
                'description' => $_POST['description'],
                'requirements' => $_POST['requirements'],
                'benefits' => $_POST['benefits'],
                'status' => (int)$_POST['status']
            ];

            $result = $this->recruitmentModel->update($id, $data);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật tin tuyển dụng thành công!';
                header('Location: /admin/recruitment');
                return;
            } else {
                $errors[] = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header("Location: /admin/recruitment/edit/{$id}");
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

            if ($result) {
                $_SESSION['success'] = 'Xóa tin tuyển dụng thành công!';
            } else {
                $_SESSION['error'] = 'Xóa tin tuyển dụng thất bại!';
            }
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
                $_SESSION['success'] = $newStatus == 1 ? 'Đã bật tin tuyển dụng!' : 'Đã tắt tin tuyển dụng!';
            } else {
                $_SESSION['error'] = 'Cập nhật trạng thái thất bại!';
            }
        }

        header('Location: /admin/recruitment');
    }

    /**
     * Validate dữ liệu tuyển dụng
     */
    private function validateRecruitmentData($data, $isUpdate = false)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Tiêu đề không được để trống!';
        } elseif (strlen($data['title']) < 5) {
            $errors[] = 'Tiêu đề phải có ít nhất 5 ký tự!';
        }

        if (empty($data['work_location'])) {
            $errors[] = 'Địa điểm làm việc không được để trống!';
        }

        if (empty($data['deadline'])) {
            $errors[] = 'Hạn nộp hồ sơ không được để trống!';
        } elseif (strtotime($data['deadline']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Hạn nộp hồ sơ phải lớn hơn hoặc bằng ngày hiện tại!';
        }

        if (isset($data['quantity']) && (int)$data['quantity'] <= 0) {
            $errors[] = 'Số lượng tuyển phải lớn hơn 0!';
        }

        return $errors;
    }

    /**
     * Upload ảnh
     */
    private function uploadImage($file)
    {
        // Tạo thư mục upload nếu chưa tồn tại
        $uploadDir = __DIR__ . '/../../public/uploads/recruitments/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Kiểm tra loại file và kích thước
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, WEBP)'];
        }
        $maxSize = 2 * 1024 * 1024; // 2MB (Giới hạn kích thước file, có thể điều chỉnh theo nhu cầu)
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Kích thước ảnh không được vượt quá 2MB'];
        }
        // Tạo tên file unique
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'error' => 'Upload ảnh thất bại!'];
    }
}
