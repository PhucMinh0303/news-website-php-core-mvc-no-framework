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
            'applicants' => 0 // Tạm thời để 0, có thể thêm sau
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
     * Xử lý lưu tin tuyển dụng mới từ create.php
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/main/recruitment/recruitment_admin');
            return;
        }

        // Xử lý upload ảnh
        $imageName = 'default-job.webp';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage['success']) {
                $imageName = $uploadedImage['filename'];
            }
        }

        // Xác định trạng thái dựa trên action từ form
        $status = 0; // Mặc định draft
        if (isset($_POST['publish']) && $_POST['publish'] == 1) {
            $status = 1;
        }

        $data = [
            'title' => $_POST['title'],
            'image' => $imageName,
            'work_location' => $_POST['work_location'],
            'degree' => $_POST['degree'],
            'quantity' => (int)$_POST['quantity'],
            'salary_range' => $_POST['salary_range'] ?? $_POST['salary_display'] ?? '',
            'deadline' => $_POST['deadline'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'benefits' => $_POST['benefits'],
            'status' => $status
        ];

        $result = $this->recruitmentModel->create($data);

        if ($result) {
            $_SESSION['success'] = 'Thêm tin tuyển dụng thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header('Location: /admin/main/recruitment/recruitment_admin');
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
            header('Location: /admin/main/recruitment/recruitment_admin');
            return;
        }

        $job = $this->recruitmentModel->getById($id);
        if (!$job) {
            $_SESSION['error'] = 'Tin tuyển dụng không tồn tại!';
            header('Location: /admin/main/recruitment/recruitment_admin');
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
            'title' => $_POST['title'],
            'image' => $imageName,
            'work_location' => $_POST['work_location'],
            'degree' => $_POST['degree'],
            'quantity' => (int)$_POST['quantity'],
            'salary_range' => $_POST['salary_range'] ?? $_POST['salary_display'] ?? $job['salary_range'],
            'deadline' => $_POST['deadline'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'benefits' => $_POST['benefits'],
            'status' => (int)$_POST['status']
        ];

        $result = $this->recruitmentModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật tin tuyển dụng thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header('Location: /admin/main/recruitment');
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
            $_SESSION['success'] = $result ? 'Xóa tin tuyển dụng thành công!' : 'Xóa tin tuyển dụng thất bại!';
        } else {
            $_SESSION['error'] = 'Tin tuyển dụng không tồn tại!';
        }

        header('Location: /admin/main/recruitment');
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

        header('Location: /admin/main/recruitment');
    }

    /**
     * Upload ảnh
     */
    private function uploadImage($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/recruitments/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'filename' => 'default-job.webp'];
    }
}