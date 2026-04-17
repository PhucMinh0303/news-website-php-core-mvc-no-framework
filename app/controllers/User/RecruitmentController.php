<?php

// controllers/RecruitmentController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../services/RecruitmentService.php';
require_once __DIR__ . '/../services/ApplicationService.php';
require_once __DIR__ . '/../repositories/RecruitmentRepository.php';
require_once __DIR__ . '/../repositories/ApplicationRepository.php';

class RecruitmentController extends Controller
{
    private $recruitmentService;
    private $applicationService;

    public function __construct()
    {
        // Inject services
        $this->recruitmentService = new RecruitmentService();
        $this->applicationService = new ApplicationService();

        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Trang danh sách tuyển dụng
     */
    public function index()
    {
        $this->setPageTitle('Tuyển dụng - Capital AM');

        // Get pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;

        // Get recruitment list from service
        $listData = $this->recruitmentService->getRecruitmentsList($page, $limit);
        $recruitments = $listData['recruitments'];
        $totalPages = $listData['totalPages'];
        $total = $listData['total'];

        // Get featured and positions
        $featuredRecruitments = $this->recruitmentService->getFeaturedRecruitments(5);
        $positions = $this->recruitmentService->getAllPositions();

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
     * Chi tiết tuyển dụng
     */
    public function detail($slug)
    {
        // Get recruitment detail from service
        $recruitment = $this->recruitmentService->getRecruitmentDetail($slug);

        if (!$recruitment) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        // Get related recruitments
        $relatedRecruitments = $this->recruitmentService->getRelatedRecruitments(
            $recruitment['position'],
            $recruitment['id'],
            4
        );

        // Current URL for sharing
        $currentUrl = $this->getCurrentUrl();

        // Get messages from session
        $successMessage = $_SESSION['apply_success'] ?? null;
        $errorMessage = $_SESSION['apply_error'] ?? null;
        $errors = $_SESSION['apply_errors'] ?? null;
        $oldData = $_SESSION['apply_data'] ?? null;

        // Clear session messages
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

        $recruitmentId = isset($_POST['recruitment_id']) ? (int)$_POST['recruitment_id'] : 0;
        $slug = trim($_POST['slug'] ?? '');
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // Process application using service
        $result = $this->applicationService->processApplication(
            $recruitmentId,
            $_POST,
            $_FILES,
            $ipAddress
        );

        if ($result['success']) {
            $_SESSION['apply_success'] = $result['message'];
            // Optionally send notification email
            $this->sendNotificationEmail($_POST['email'], $_POST['ten'], $slug);
        } else {
            if (!empty($result['errors'])) {
                $_SESSION['apply_errors'] = $result['errors'];
                $_SESSION['apply_data'] = $_POST;
            }
            $_SESSION['apply_error'] = $result['message'];
        }

        $this->redirect('/recruitment/' . $slug);
    }

    /**
     * Tìm kiếm tuyển dụng (AJAX)
     */
    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $position = isset($_GET['position']) ? trim($_GET['position']) : '';

        // Use service to search
        $recruitments = $this->recruitmentService->searchRecruitments($keyword, $position, 20);

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'data' => $recruitments
            ]);
        } else {
            $data = [
                'recruitments' => $recruitments,
                'keyword' => $keyword,
                'position' => $position,
                'title' => 'Kết quả tìm kiếm - Capital AM'
            ];
            $this->view('recruitment/search', $data);
        }
    }

    /**
     * API lấy danh sách tuyển dụng
     */
    public function apiList()
    {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $recruitments = $this->recruitmentService->getAllPositions();

        $this->json([
            'success' => true,
            'data' => $recruitments,
            'total' => count($recruitments)
        ]);
    }

    /**
     * Lấy URL hiện tại
     */
    private function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Gửi email thông báo
     */
    private function sendNotificationEmail($to, $name, $slug)
    {
        // Có thể implement gửi email ở đây
        // Sử dụng PHPMailer hoặc mail() function
    }
}