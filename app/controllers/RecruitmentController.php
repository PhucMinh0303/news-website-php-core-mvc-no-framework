<?php
// app/controllers/RecruitmentController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Recruitment.php';

class RecruitmentController extends Controller
{
    private $recruitmentModel;

    public function __construct()
    {
        $this->recruitmentModel = new Recruitment();
    }

    /**
     * Index page - List all recruitments
     */
    public function index()
    {
        $recruitments = $this->recruitmentModel->getAllOpen();

        $data = [
            'recruitments' => $recruitments,
            'pageTitle' => 'Tuyển dụng - Các vị trí đang tuyển'
        ];

        $this->view('Recruitment/recruitment', $data);
    }

    /**
     * Show recruitment detail
     */
    public function show($slug)
    {
        $recruitment = $this->recruitmentModel->getBySlug($slug);

        if (!$recruitment) {
            // Redirect to 404 page
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        // Increment view count
        $this->recruitmentModel->incrementViews($recruitment['id']);

        $data = [
            'recruitment' => $recruitment,
            'pageTitle' => $recruitment['recruitment_title']
        ];

        $this->view('Recruitment/recruitment_title2', $data);
    }

    /**
     * Search recruitments
     */
    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        if (empty($keyword)) {
            header('Location: ' . $this->url('recruitment'));
            return;
        }

        $recruitments = $this->recruitmentModel->search($keyword);

        $data = [
            'recruitments' => $recruitments,
            'keyword' => $keyword,
            'pageTitle' => "Tìm kiếm tuyển dụng: {$keyword}"
        ];

        $this->view('Recruitment/search', $data);
    }

    /**
     * Admin: List all recruitments
     */
    public function adminList()
    {
        $recruitments = $this->recruitmentModel->all();

        $data = [
            'recruitments' => $recruitments,
            'pageTitle' => 'Quản lý tuyển dụng'
        ];

        $this->view('admin/recruitment/list', $data);
    }

    /**
     * Admin: Create recruitment
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeRecruitmentData($_POST);

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['image']);
                if ($imagePath) {
                    $data['image'] = $imagePath;
                }
            }

            $id = $this->recruitmentModel->create($data);

            if ($id) {
                $_SESSION['success'] = 'Thêm tin tuyển dụng thành công';
                header('Location: ' . $this->url('admin/recruitment'));
                return;
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }

        $this->view('admin/recruitment/create');
    }

    /**
     * Admin: Edit recruitment
     */
    public function edit($id)
    {
        $recruitment = $this->recruitmentModel->find($id);

        if (!$recruitment) {
            $_SESSION['error'] = 'Không tìm thấy tin tuyển dụng';
            header('Location: ' . $this->url('admin/recruitment'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeRecruitmentData($_POST);

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['image']);
                if ($imagePath) {
                    $data['image'] = $imagePath;
                }
            }

            if ($this->recruitmentModel->update($id, $data)) {
                $_SESSION['success'] = 'Cập nhật tin tuyển dụng thành công';
                header('Location: ' . $this->url('admin/recruitment'));
                return;
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }

        $data = [
            'recruitment' => $recruitment,
            'pageTitle' => 'Chỉnh sửa tin tuyển dụng'
        ];

        $this->view('admin/recruitment/edit', $data);
    }

    /**
     * Sanitize recruitment data
     */
    private function sanitizeRecruitmentData($data)
    {
        return [
            'recruitment_title' => htmlspecialchars($data['recruitment_title']),
            'slug' => $this->createSlug($data['recruitment_title']),
            'job_description' => $data['job_description'],
            'job_requirements' => $data['job_requirements'],
            'job_benefits' => isset($data['job_benefits']) ? $data['job_benefits'] : null,
            'location' => htmlspecialchars($data['location']),
            'deadline' => $data['deadline'],
            'quantity' => (int)$data['quantity'],
            'position' => htmlspecialchars($data['position']),
            'education' => htmlspecialchars($data['education']),
            'salary_range' => isset($data['salary_range']) ? $data['salary_range'] : null,
            'experience' => isset($data['experience']) ? $data['experience'] : null,
            'job_type' => isset($data['job_type']) ? $data['job_type'] : 'fulltime',
            'status' => isset($data['status']) ? $data['status'] : 'draft'
        ];
    }

    /**
     * Create slug from title
     */
    private function createSlug($title)
    {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
        $slug = trim($slug, '-');
        return $slug;
    }

    /**
     * Upload image
     */
    private function uploadImage($file)
    {
        $targetDir = __DIR__ . '/../../public/uploads/recruitment/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = time() . '-' . uniqid() . '.' . $extension;
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return 'uploads/recruitment/' . $fileName;
        }

        return false;
    }

    /**
     * Generate URL
     */
    private function url($path)
    {
        return '/' . ltrim($path, '/');
    }
}

?>