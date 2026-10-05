<?php
// app/controllers/Admin/AdminRecruitmentController.php

class AdminRecruitmentController extends Controller
{
    private $recruitmentModel;
    private $recruitmentService;
    private $uploadService;

    public function __construct()
    {
        $this->recruitmentModel = new RecruitmentModel();
        $this->recruitmentService = new RecruitmentService();
        $this->uploadService = new FileUploadService();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
            $this->delete(null);
        }

        // Lấy tham số từ request
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu từ model
        $recruitments = $this->recruitmentModel->getAllAdmin($status, $limit, $offset, $search);
        $totalRecords = $this->recruitmentModel->countAdmin($status, $search);
        $totalPages = ceil($totalRecords / $limit);

        // Render view
        $data = [
            'recruitments' => $recruitments,
            'status_filter' => $status,
            'search_keyword' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ];

        if ($this->isAjax()) {
            $this->view('admin/main/recruitment/recruitment_admin', $data);
            return;
        }

        $this->view('admin/admin', array_merge($data, [
            'admin_view' => 'admin/main/recruitment/recruitment_admin',
            'admin_page' => 'recruitment'
        ]));
    }

    public function create()
    {
        if ($this->isAjax()) {
            $this->view('admin/main/recruitment/create-recruitment');
            return;
        }

        $this->view('admin/admin', [
            'admin_view' => 'admin/main/recruitment/create-recruitment',
            'admin_page' => 'recruitment'
        ]);
    }

    public function store()
    {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        // Validate dữ liệu
        $errors = $this->validateRecruitmentData($_POST);

        // Xử lý upload ảnh
        $imagePath = 'default-job.webp'; // Giá trị mặc định
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = $this->uploadService->upload($_FILES['image'], 'recruitments');
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['message'];
            }
        }

        // Nếu có lỗi, quay lại form với dữ liệu cũ
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . Router::url('admin/recruitment/create'));
            exit();
        }

        // Tạo slug từ tiêu đề
        $slug = $this->generateSlug(trim($_POST['title']));

        // Chuẩn bị dữ liệu lưu - Mapping đầy đủ với SQL fields
        $data = [
            'title' => trim($_POST['title']),                       // maps to `title`
            'slug' => $slug,                                         // maps to `slug`
            'image' => $imagePath,                                   // maps to `image`
            'work_location' => trim($_POST['work_location']),       // maps to `work_location`
            'degree' => $_POST['degree'] ?? 'Cao Đẳng - Đại Học',   // maps to `degree`
            'work_type' => $_POST['work_type'] ?? 'Toàn thời gian', // maps to `work_type`
            'quantity' => (int)($_POST['quantity'] ?? 'Số lượng cần tuyển'),           // maps to `quantity`
            'salary_range' => trim($_POST['salary_range'] ?? 'Mức lương'),         // maps to `salary_range`
            'deadline' => $_POST['deadline'] ?? 'Hạn nộp hồ sơ',                        // maps to `deadline`
            'description' => $_POST['description'] ?? 'Mô tả công việc',           // maps to `description`
            'requirements' => $_POST['requirements'] ?? 'Yêu cầu ứng viên',         // maps to `requirements`
            'benefits' => $_POST['benefits'] ?? 'Quyền lợi được hưởng',                 // maps to `benefits`
            'status' => isset($_POST['publish'])
                ? 1
                : (int)($_POST['status'] ?? 0)                      // 0-Draft, 1-Open, 2-Closed
        ];

        // Lưu vào database
        $result = $this->recruitmentModel->create($data);

        if ($result) {
            $_SESSION['success'] = isset($_POST['publish'])
                ? 'Hiển thị thành công'
                : 'Đã lưu tin tuyển dụng thành công!';

            // Kiểm tra nút "Lưu và tiếp tục"
            if (isset($_POST['save_and_continue']) && $_POST['save_and_continue'] == 1) {
                header('Location: ' . Router::url('admin/recruitment/edit', [$result]));
            } else {
                header('Location: ' . Router::url('admin/recruitment'));
            }
        } else {
            $_SESSION['error'] = 'Không thể lưu tin tuyển dụng. Vui lòng thử lại!';
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . Router::url('admin/recruitment/create'));
        }
        exit();
    }

    public function edit($id)
    {
        $recruitment = ctype_digit((string)$id)
            ? $this->recruitmentService->getRecruitmentById($id)
            : $this->recruitmentService->getRecruitmentBySlug($id);

        if (!$recruitment) {
            $_SESSION['error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        $this->view('admin/admin', [
            'admin_view' => 'admin/main/recruitment/edit-recruitment',
            'admin_page' => 'recruitment',
            'recruitment' => $recruitment,
            'edit_id' => $recruitment['id']
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        $result = $this->recruitmentService->updateRecruitment($id, $_POST, $_FILES['image'] ?? null);

        if (!$result['success']) {
            $_SESSION['errors'] = $result['errors'];
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . Router::url('admin/recruitment/edit', [$id]));
            exit();
        }

        $_SESSION['success'] = 'Đã cập nhật tin tuyển dụng thành công!';

        header('Location: ' . Router::url('admin/recruitment'));
        exit();
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Yêu cầu xóa không hợp lệ!';
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $routeId = $id === null ? $postedId : filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        if ($routeId === false || $postedId === false || $postedId !== $routeId) {
            $_SESSION['error'] = 'ID tin tuyển dụng không hợp lệ!';
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        $id = $routeId;

        try {
            $result = $this->recruitmentService->deleteRecruitment($id);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Throwable $exception) {
            error_log('Recruitment deletion failed: ' . $exception->getMessage());
            $_SESSION['error'] = 'Không thể xóa tin tuyển dụng do lỗi cơ sở dữ liệu!';
        }

        $page = filter_input(INPUT_POST, 'p', FILTER_VALIDATE_INT, [
            'options' => ['default' => 1, 'min_range' => 1]
        ]);
        $status = trim((string)($_POST['status'] ?? ''));
        $search = trim((string)($_POST['search'] ?? ''));
        $query = http_build_query([
            'p' => $page ?: 1,
            'status' => $status,
            'search' => $search
        ]);

        header('Location: ' . Router::url('admin/recruitment') . '?' . $query);
        exit();
    }

    public function toggleStatus($id)
    {
        $recruitment = $this->recruitmentModel->getById($id);

        if (!$recruitment) {
            $_SESSION['error'] = 'Không tìm thấy tin tuyển dụng!';
            header('Location: ' . Router::url('admin/recruitment'));
            exit();
        }

        // Chuyển đổi trạng thái: 0->1, 1->2, 2->0
        $newStatus = ($recruitment['status'] + 1) % 3;
        $result = $this->recruitmentModel->updateStatus($id, $newStatus);

        if ($result) {
            $_SESSION['success'] = 'Đã thay đổi trạng thái tin tuyển dụng!';
        } else {
            $_SESSION['error'] = 'Không thể thay đổi trạng thái!';
        }

        header('Location: ' . Router::url('admin/recruitment'));
        exit();
    }

    /**
     * Validate dữ liệu tuyển dụng
     * Mapping tất cả fields từ SQL
     */
    private function validateRecruitmentData($data, $isUpdate = false)
    {
        $errors = [];

        // 1. Validate `recruitments_title` - Tiêu đề
        if (empty($data['title']) || strlen(trim($data['title'])) < 5) {
            $errors[] = 'Tiêu đề tin tuyển dụng phải có ít nhất 5 ký tự.';
        }

        // 2. Validate `work_location` - Địa điểm làm việc
        if (empty($data['work_location']) || strlen(trim($data['work_location'])) < 3) {
            $errors[] = 'Địa điểm làm việc không được để trống và phải có ít nhất 3 ký tự.';
        }

        // 3. Validate `degree` - Trình độ yêu cầu (không bắt buộc vì có default)
        // Không cần validate vì có giá trị mặc định

        // 4. Validate `work_type` - Hình thức làm việc (không bắt buộc vì có default)
        // Không cần validate vì có giá trị mặc định

        // 5. Validate `quantity` - Số lượng cần tuyển
        if (empty($data['quantity']) || (int)$data['quantity'] < 1) {
            $errors[] = 'Số lượng tuyển phải lớn hơn 0.';
        }

        // 6. Validate `salary_range` - Mức lương
        if (empty($data['salary_range']) || strlen(trim($data['salary_range'])) < 3) {
            $errors[] = 'Mức lương không được để trống và phải có ít nhất 3 ký tự.';
        }

        // 7. Validate `deadline` - Hạn nộp hồ sơ
        if (empty($data['deadline'])) {
            $errors[] = 'Hạn nộp hồ sơ không được để trống.';
        } else {
            $deadline = DateTime::createFromFormat('Y-m-d', $data['deadline']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);

            if (!$deadline) {
                $errors[] = 'Hạn nộp hồ sơ không đúng định dạng ngày tháng.';
            } elseif ($deadline < $today) {
                $errors[] = 'Hạn nộp hồ sơ phải là ngày trong tương lai.';
            }
        }

        // 8. Validate `description` - Mô tả công việc
        if (empty($data['description']) || strlen(trim($data['description'])) < 20) {
            $errors[] = 'Mô tả công việc phải có ít nhất 20 ký tự.';
        }

        // 9. Validate `requirements` - Yêu cầu ứng viên
        if (empty($data['requirements']) || strlen(trim($data['requirements'])) < 20) {
            $errors[] = 'Yêu cầu ứng viên phải có ít nhất 20 ký tự.';
        }

        // 10. Validate `benefits` - Quyền lợi được hưởng
        if (empty($data['benefits']) || strlen(trim($data['benefits'])) < 10) {
            $errors[] = 'Quyền lợi được hưởng phải có ít nhất 10 ký tự.';
        }

        // 11. Validate `status`: 0-Draft, 1-Open, 2-Closed
        if (!in_array((int)($data['status'] ?? 0), [0, 1, 2], true)) {
            $errors[] = 'Trạng thái tin tuyển dụng không hợp lệ.';
        }

        return $errors;
    }

    /**
     * Tạo slug từ tiêu đề
     */
    private function generateSlug($title)
    {
        // Chuyển đổi tiếng Việt có dấu sang không dấu
        $title = $this->removeVietnameseAccents($title);

        // Chuyển thành chữ thường
        $slug = strtolower(trim($title));

        // Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);

        // Xóa dấu gạch ngang lặp lại
        $slug = preg_replace('/-+/', '-', $slug);

        // Xóa dấu gạch ngang ở đầu và cuối
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Xóa dấu tiếng Việt
     */
    private function removeVietnameseAccents($str)
    {
        $unwanted_array = array(
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'Þ' => 'B',
            'þ' => 'b',
            'ß' => 'B',
            'Ç' => 'C',
            'ç' => 'c',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'Ñ' => 'N',
            'ñ' => 'n',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'Š' => 'S',
            'š' => 's',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'Ý' => 'Y',
            'ý' => 'y',
            'ÿ' => 'y',
            'Ž' => 'Z',
            'ž' => 'z',
            'Đ' => 'D',
            'đ' => 'd'
        );
        return strtr($str, $unwanted_array);
    }
}
