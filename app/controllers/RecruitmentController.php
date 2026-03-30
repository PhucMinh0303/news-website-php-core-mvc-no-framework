<?php
// controllers/RecruitmentController.php

require_once '../core/Controller.php';
require_once '../models/RecruitmentTitleModel.php';
require_once '../models/ApplicationModel.php';

class RecruitmentController extends Controller
{
    private $recruitmentTitleModel;
    private $applicationModel;

    public function __construct()
    {
        $this->recruitmentTitleModel = new RecruitmentTitleModel();
        $this->applicationModel = new ApplicationModel();
    }

    /**
     * Trang danh sách tuyển dụng
     */
    public function index()
    {
        // Lấy danh sách tuyển dụng đang mở
        $recruitments = $this->recruitmentTitleModel->getActiveRecruitments();

        $data = [
            'recruitments' => $recruitments,
            'title' => 'Tuyển dụng - EMIR'
        ];

        $this->view('recruitment/recruitment', $data);
    }

    /**
     * Chi tiết tuyển dụng
     */
    public function detail($slug)
    {
        // Lấy chi tiết từ bảng recruitment_title
        $recruitment = $this->recruitmentTitleModel->getDetail($slug);

        if (!$recruitment) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        // Tăng lượt xem
        $this->recruitmentTitleModel->incrementViews($recruitment['id']);

        // Lấy tuyển dụng liên quan
        $relatedRecruitments = $this->recruitmentTitleModel->getFeaturedRecruitments(4);

        // Current URL for sharing
        $currentUrl = $this->getCurrentUrl();

        $data = [
            'recruitment' => $recruitment,
            'relatedRecruitments' => $relatedRecruitments,
            'currentUrl' => $currentUrl,
            'title' => $recruitment['title'] . ' - EMIR'
        ];

        $this->view('recruitment/recruitment-title', $data);
    }

    /**
     * Ứng tuyển
     */
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruitment');
            return;
        }

        $recruitmentId = isset($_POST['recruitment_id']) ? (int)$_POST['recruitment_id'] : 0;
        $fullname = trim($_POST['ten'] ?? '');
        $phone = trim($_POST['dt'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $content = trim($_POST['noidung'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        // Validation
        $errors = [];

        if (empty($fullname)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($phone)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        // Xử lý upload CV
        $cvFile = null;
        if (isset($_FILES['filechon']) && $_FILES['filechon']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $fileType = $_FILES['filechon']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = 'uploads/cv/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExt = pathinfo($_FILES['filechon']['name'], PATHINFO_EXTENSION);
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $fullname) . '.' . $fileExt;
                $cvFile = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['filechon']['tmp_name'], $cvFile)) {
                    $errors[] = 'Không thể upload CV. Vui lòng thử lại.';
                    $cvFile = null;
                }
            } else {
                $errors[] = 'CV phải là file PDF hoặc Word';
            }
        } else {
            $errors[] = 'Vui lòng upload CV của bạn';
        }

        // Kiểm tra đã ứng tuyển chưa
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if (empty($errors) && $this->applicationModel->hasApplied($recruitmentId, $email, $ipAddress)) {
            $errors[] = 'Bạn đã ứng tuyển vị trí này rồi. Vui lòng chờ phản hồi từ chúng tôi.';
        }

        if (!empty($errors)) {
            $_SESSION['apply_errors'] = $errors;
            $_SESSION['apply_data'] = $_POST;
            header('Location: /recruitment/' . $slug);
            return;
        }

        // Lưu đơn ứng tuyển
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
            $_SESSION['apply_success'] = 'Cảm ơn bạn đã ứng tuyển. Chúng tôi sẽ liên hệ lại sớm nhất!';
        } else {
            $_SESSION['apply_error'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        header('Location: /recruitment/' . $slug);
    }

    /**
     * Lấy URL hiện tại
     */
    private function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}