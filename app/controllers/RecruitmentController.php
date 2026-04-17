<?php
// controllers/UserRecruitmentController.php
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
    }

    /**
     * Hiển thị danh sách tin tuyển dụng (Frontend)
     */
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy các tham số lọc
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $jobs = [];
        $total = 0;

        if (!empty($keyword) || !empty($location)) {
            // Có tìm kiếm
            $jobs = $this->recruitmentModel->search($keyword, $location, null, $limit, $offset);
            $total = $this->recruitmentModel->countSearchResults($keyword, $location);
        } else {
            // Lấy tất cả jobs đang mở
            $jobs = $this->recruitmentModel->getAll(1, $limit, $offset);
            $total = $this->recruitmentModel->count(1);
        }

        $total_pages = ceil($total / $limit);

        // Lấy các job nổi bật (recent jobs)
        $recentJobs = $this->recruitmentModel->getRecentJobs(5);

        $data = [
            'jobs' => $jobs,
            'recentJobs' => $recentJobs,
            'total' => $total,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'keyword' => $keyword,
            'location' => $location
        ];

        extract($data);
        include __DIR__ . '/../views/recruitment/index.php';
    }

    /**
     * Hiển thị chi tiết tin tuyển dụng
     */
    public function show($slug)
    {
        $job = $this->recruitmentModel->getBySlug($slug);

        if (!$job) {
            header('HTTP/1.0 404 Not Found');
            include __DIR__ . '/../views/errors/404.php';
            return;
        }

        // Kiểm tra nếu job đã đóng hoặc hết hạn
        $isExpired = strtotime($job['deadline']) < strtotime(date('Y-m-d'));
        $isClosed = $job['status'] != 1;

        if ($isExpired || $isClosed) {
            // Job đã đóng, vẫn hiển thị nhưng không cho apply
            $job['can_apply'] = false;
            $job['closed_reason'] = $isExpired ? 'Hết hạn nộp hồ sơ' : 'Tin tuyển dụng đã đóng';
        } else {
            $job['can_apply'] = true;
        }

        // Tăng lượt xem
        $this->recruitmentModel->incrementViews($job['id']);

        // Lấy các job liên quan (cùng vị trí hoặc cùng địa điểm)
        $relatedJobs = $this->recruitmentModel->getRelatedJobs($job['id'], $job['work_location'], 3);

        include __DIR__ . '/../views/recruitment/detail.php';
    }

    /**
     * Xử lý ứng tuyển
     */
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruitment');
            return;
        }

        $recruitmentId = (int)($_POST['recruitment_id'] ?? 0);

        // Kiểm tra job có tồn tại và còn nhận hồ sơ không
        $job = $this->recruitmentModel->findById($recruitmentId);
        if (!$job || $job['status'] != 1 || strtotime($job['deadline']) < strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = 'Tin tuyển dụng này đã đóng hoặc không tồn tại!';
            header("Location: /recruitment");
            return;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // Validate
        $errors = [];

        if (empty($fullname)) {
            $errors[] = 'Họ tên không được để trống';
        } elseif (strlen($fullname) < 3) {
            $errors[] = 'Họ tên phải có ít nhất 3 ký tự';
        }

        if (empty($phone)) {
            $errors[] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (!empty($content) && strlen($content) > 1000) {
            $errors[] = 'Nội dung giới thiệu không được vượt quá 1000 ký tự';
        }

        // Kiểm tra đã ứng tuyển chưa (trong 24h gần nhất)
        if ($this->applicationModel->hasAppliedRecently($recruitmentId, $email, $ipAddress, 24)) {
            $errors[] = 'Bạn đã ứng tuyển vị trí này trong 24 giờ qua! Vui lòng chờ phản hồi.';
        }

        // Xử lý upload CV
        $cvFile = '';
        if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/cvs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExt = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['pdf', 'doc', 'docx'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if ($_FILES['cv_file']['size'] > $maxSize) {
                $errors[] = 'Kích thước file CV không được vượt quá 5MB';
            } elseif (in_array($fileExt, $allowedExt)) {
                $cvFile = uniqid() . '_' . time() . '.' . $fileExt;
                move_uploaded_file($_FILES['cv_file']['tmp_name'], $uploadDir . $cvFile);
            } else {
                $errors[] = 'CV phải là file PDF, DOC hoặc DOCX';
            }
        } else {
            $errors[] = 'Vui lòng upload CV của bạn';
        }

        if (empty($errors)) {
            $data = [
                'recruitment_id' => $recruitmentId,
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'content' => $content,
                'cv_file' => $cvFile,
                'ip_address' => $ipAddress
            ];

            if ($this->applicationModel->save($data)) {
                $_SESSION['success'] = 'Ứng tuyển thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.';

                // Gửi email thông báo (nếu có cấu hình)
                $this->sendApplicationConfirmationEmail($email, $fullname, $job['title']);
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
            }
        } else {
            $_SESSION['errors'] = $errors;
        }

        header("Location: /recruitment/" . $job['slug']);
    }

    /**
     * API: Lấy danh sách jobs theo AJAX (cho load more)
     */
    public function apiGetJobs()
    {
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $jobs = $this->recruitmentModel->getAll(1, $limit, $offset);
        $total = $this->recruitmentModel->count(1);

        echo json_encode([
            'success' => true,
            'data' => $jobs,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_records' => $total
            ]
        ]);
        exit;
    }

    /**
     * Gửi email xác nhận ứng tuyển
     */
    private function sendApplicationConfirmationEmail($to, $name, $jobTitle)
    {
        // Có thể implement gửi email ở đây
        // Ví dụ sử dụng PHPMailer hoặc mail()
        $subject = "Xác nhận ứng tuyển vị trí: {$jobTitle}";
        $message = "Chào {$name},\n\n";
        $message .= "Cảm ơn bạn đã ứng tuyển vào vị trí {$jobTitle}.\n";
        $message .= "Chúng tôi sẽ xem xét hồ sơ và liên hệ lại với bạn trong thời gian sớm nhất.\n\n";
        $message .= "Trân trọng,\nBan tuyển dụng";

        $headers = "From: recruitment@company.com\r\n";
        $headers .= "Reply-To: hr@company.com\r\n";

        // @mail($to, $subject, $message, $headers);

        return true; // Tạm thời return true
    }
}