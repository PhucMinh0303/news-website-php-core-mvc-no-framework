<?php
// controllers/ApiRecruitmentController.php
require_once __DIR__ . '/../models/RecruitmentModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

class ApiRecruitmentController
{
    private $recruitmentModel;
    private $applicationModel;
    
    public function __construct()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        $this->recruitmentModel = new RecruitmentModel();
        $this->applicationModel = new ApplicationModel();
    }
    
    /**
     * API Response helper
     */
    private function sendResponse($data, $statusCode = 200, $message = '')
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    private function sendError($message, $statusCode = 400, $errors = [])
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    private function checkAdminAuth()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $_GET['api_token'] ?? '';
        
        if (empty($token)) {
            $this->sendError('Unauthorized: API token required', 401);
        }
        
        // Kiểm tra token (có thể lưu trong database hoặc config)
        $validTokens = [
            'admin_token_123456' => 'admin',
            'api_key_789012' => 'editor'
        ];
        
        if (!isset($validTokens[$token])) {
            $this->sendError('Forbidden: Invalid API token', 403);
        }
        
        return $validTokens[$token];
    }
    
    private function getRequestData()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    
    /**
     * GET /api/recruitment - Lấy danh sách tin tuyển dụng (public)
     */
    public function getList()
    {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $status = isset($_GET['status']) ? (int)$_GET['status'] : 1; // Mặc định lấy tin đang mở
            $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
            $location = isset($_GET['location']) ? trim($_GET['location']) : '';
            $offset = ($page - 1) * $limit;
            
            $jobs = [];
            $total = 0;
            
            if (!empty($keyword) || !empty($location)) {
                $jobs = $this->recruitmentModel->search($keyword, $location, null, $limit, $offset);
                $total = $this->recruitmentModel->countSearchResults($keyword, $location);
            } else {
                $jobs = $this->recruitmentModel->getAll($status, $limit, $offset);
                $total = $this->recruitmentModel->count($status);
            }
            
            // Thêm thông tin bổ sung
            foreach ($jobs as &$job) {
                $job['is_expired'] = strtotime($job['deadline']) < strtotime(date('Y-m-d'));
                $job['days_left'] = $job['is_expired'] ? 0 : ceil((strtotime($job['deadline']) - time()) / 86400);
                $job['image_url'] = $job['image'] ? '/uploads/' . $job['image'] : '/assets/images/default-job.webp';
                unset($job['description'], $job['requirements'], $job['benefits']); // Ẩn chi tiết khi ở list
            }
            
            $this->sendResponse([
                'jobs' => $jobs,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_records' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ], 200, 'Lấy danh sách tin tuyển dụng thành công');
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/recruitment/{id} - Lấy chi tiết tin tuyển dụng (public)
     */
    public function getDetail($id)
    {
        try {
            $job = $this->recruitmentModel->findById($id);
            
            if (!$job) {
                $this->sendError('Không tìm thấy tin tuyển dụng', 404);
            }
            
            // Tăng lượt xem
            $this->recruitmentModel->incrementViews($id);
            
            $job['is_expired'] = strtotime($job['deadline']) < strtotime(date('Y-m-d'));
            $job['days_left'] = $job['is_expired'] ? 0 : ceil((strtotime($job['deadline']) - time()) / 86400);
            $job['can_apply'] = !$job['is_expired'] && $job['status'] == 1;
            $job['image_url'] = $job['image'] ? '/uploads/' . $job['image'] : '/assets/images/default-job.webp';
            
            // Lấy job liên quan
            $relatedJobs = $this->recruitmentModel->getRelatedJobs($id, $job['work_location'], 3);
            
            $this->sendResponse([
                'job' => $job,
                'related_jobs' => $relatedJobs
            ], 200, 'Lấy chi tiết tin tuyển dụng thành công');
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/recruitment/slug/{slug} - Lấy chi tiết theo slug (public)
     */
    public function getBySlug($slug)
    {
        try {
            $job = $this->recruitmentModel->getBySlug($slug);
            
            if (!$job) {
                $this->sendError('Không tìm thấy tin tuyển dụng', 404);
            }
            
            $this->recruitmentModel->incrementViews($job['id']);
            
            $job['is_expired'] = strtotime($job['deadline']) < strtotime(date('Y-m-d'));
            $job['days_left'] = $job['is_expired'] ? 0 : ceil((strtotime($job['deadline']) - time()) / 86400);
            $job['can_apply'] = !$job['is_expired'] && $job['status'] == 1;
            $job['image_url'] = $job['image'] ? '/uploads/' . $job['image'] : '/assets/images/default-job.webp';
            
            $this->sendResponse($job, 200, 'Lấy chi tiết tin tuyển dụng thành công');
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * POST /api/recruitment - Tạo tin tuyển dụng mới (admin)
     */
    public function create()
    {
        try {
            $this->checkAdminAuth();
            
            $data = $this->getRequestData();
            
            // Validate
            $errors = $this->validateRecruitmentData($data);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 422, $errors);
            }
            
            // Xử lý image nếu có base64
            $image = 'default-job.webp';
            if (!empty($data['image_base64'])) {
                $image = $this->saveBase64Image($data['image_base64']);
            } elseif (!empty($data['image'])) {
                $image = $data['image'];
            }
            
            $slug = !empty($data['slug']) ? $this->createSlug($data['slug']) : $this->createSlug($data['title']);
            
            $insertData = [
                ':title' => $data['title'],
                ':slug' => $slug,
                ':image' => $image,
                ':work_location' => $data['work_location'],
                ':degree' => $data['degree'] ?? 'Cao Đẳng - Đại Học',
                ':quantity' => (int)$data['quantity'],
                ':salary_range' => $data['salary_range'] ?? null,
                ':deadline' => $data['deadline'],
                ':description' => $data['description'] ?? null,
                ':requirements' => $data['requirements'] ?? null,
                ':benefits' => $data['benefits'] ?? null,
                ':status' => (int)$data['status']
            ];
            
            $id = $this->recruitmentModel->create($insertData);
            
            if ($id) {
                $newJob = $this->recruitmentModel->findById($id);
                $this->sendResponse($newJob, 201, 'Tạo tin tuyển dụng thành công');
            } else {
                $this->sendError('Không thể tạo tin tuyển dụng', 500);
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * PUT /api/recruitment/{id} - Cập nhật tin tuyển dụng (admin)
     */
    public function update($id)
    {
        try {
            $this->checkAdminAuth();
            
            $job = $this->recruitmentModel->findById($id);
            if (!$job) {
                $this->sendError('Không tìm thấy tin tuyển dụng', 404);
            }
            
            $data = $this->getRequestData();
            
            // Validate
            $errors = $this->validateRecruitmentData($data, true);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 422, $errors);
            }
            
            // Xử lý image nếu có base64
            $image = $job['image'];
            if (!empty($data['image_base64'])) {
                $image = $this->saveBase64Image($data['image_base64'], $job['image']);
            } elseif (isset($data['image']) && $data['image'] !== $job['image']) {
                $image = $data['image'];
            }
            
            $updateData = [
                ':title' => $data['title'],
                ':image' => $image,
                ':work_location' => $data['work_location'],
                ':degree' => $data['degree'] ?? $job['degree'],
                ':quantity' => (int)$data['quantity'],
                ':salary_range' => $data['salary_range'] ?? $job['salary_range'],
                ':deadline' => $data['deadline'],
                ':description' => $data['description'] ?? $job['description'],
                ':requirements' => $data['requirements'] ?? $job['requirements'],
                ':benefits' => $data['benefits'] ?? $job['benefits'],
                ':status' => (int)$data['status']
            ];
            
            // Cập nhật slug nếu title thay đổi
            if ($data['title'] !== $job['title']) {
                $updateData[':slug'] = $this->createSlug($data['title']);
            }
            
            $result = $this->recruitmentModel->update($id, $updateData);
            
            if ($result) {
                $updatedJob = $this->recruitmentModel->findById($id);
                $this->sendResponse($updatedJob, 200, 'Cập nhật tin tuyển dụng thành công');
            } else {
                $this->sendError('Không thể cập nhật tin tuyển dụng', 500);
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * DELETE /api/recruitment/{id} - Xóa tin tuyển dụng (admin)
     */
    public function delete($id)
    {
        try {
            $this->checkAdminAuth();
            
            $job = $this->recruitmentModel->findById($id);
            if (!$job) {
                $this->sendError('Không tìm thấy tin tuyển dụng', 404);
            }
            
            // Xóa ảnh nếu không phải mặc định
            if ($job['image'] && $job['image'] !== 'default-job.webp') {
                $imagePath = __DIR__ . '/../public/uploads/' . $job['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Xóa ứng tuyển
            $this->applicationModel->deleteByRecruitmentId($id);
            
            // Xóa tin tuyển dụng
            $result = $this->recruitmentModel->delete($id);
            
            if ($result) {
                $this->sendResponse(null, 200, 'Xóa tin tuyển dụng thành công');
            } else {
                $this->sendError('Không thể xóa tin tuyển dụng', 500);
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * POST /api/recruitment/apply - Ứng tuyển (public)
     */
    public function apply()
    {
        try {
            $data = $this->getRequestData();
            
            $recruitmentId = (int)($data['recruitment_id'] ?? 0);
            $job = $this->recruitmentModel->findById($recruitmentId);
            
            if (!$job || $job['status'] != 1 || strtotime($job['deadline']) < strtotime(date('Y-m-d'))) {
                $this->sendError('Tin tuyển dụng đã đóng hoặc không tồn tại', 400);
            }
            
            // Validate
            $errors = $this->validateApplicationData($data);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 422, $errors);
            }
            
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            
            // Kiểm tra ứng tuyển trùng
            if ($this->applicationModel->hasAppliedRecently($recruitmentId, $data['email'], $ipAddress, 24)) {
                $this->sendError('Bạn đã ứng tuyển vị trí này trong 24 giờ qua', 429);
            }
            
            // Xử lý CV base64
            $cvFile = '';
            if (!empty($data['cv_base64'])) {
                $cvFile = $this->saveBase64File($data['cv_base64'], 'cvs/', ['pdf', 'doc', 'docx']);
                if (!$cvFile) {
                    $this->sendError('File CV không hợp lệ', 422, ['cv_file' => 'CV phải là file PDF, DOC hoặc DOCX']);
                }
            } elseif (!empty($data['cv_url'])) {
                $cvFile = $this->downloadFileFromUrl($data['cv_url'], 'cvs/');
            } else {
                $this->sendError('CV là bắt buộc', 422, ['cv_file' => 'Vui lòng upload CV của bạn']);
            }
            
            $applicationData = [
                'recruitment_id' => $recruitmentId,
                'fullname' => $data['fullname'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'content' => $data['content'] ?? '',
                'cv_file' => $cvFile,
                'ip_address' => $ipAddress
            ];
            
            $result = $this->applicationModel->save($applicationData);
            
            if ($result) {
                $this->sendResponse([
                    'application_id' => $result,
                    'job_title' => $job['title']
                ], 201, 'Ứng tuyển thành công');
            } else {
                $this->sendError('Có lỗi xảy ra khi ứng tuyển', 500);
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/recruitment/applications/{id} - Lấy danh sách ứng viên (admin)
     */
    public function getApplications($recruitmentId)
    {
        try {
            $this->checkAdminAuth();
            
            $job = $this->recruitmentModel->findById($recruitmentId);
            if (!$job) {
                $this->sendError('Không tìm thấy tin tuyển dụng', 404);
            }
            
            $applications = $this->applicationModel->getByRecruitmentId($recruitmentId);
            
            // Thêm URL cho file CV
            foreach ($applications as &$app) {
                $app['cv_url'] = $app['cv_file'] ? '/uploads/cvs/' . $app['cv_file'] : null;
            }
            
            $this->sendResponse([
                'job' => $job,
                'applications' => $applications,
                'total_applications' => count($applications)
            ], 200, 'Lấy danh sách ứng viên thành công');
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/recruitment/stats - Thống kê (admin)
     */
    public function getStats()
    {
        try {
            $this->checkAdminAuth();
            
            $totalJobs = $this->recruitmentModel->count();
            $openJobs = $this->recruitmentModel->count(1);
            $expiredJobs = $this->recruitmentModel->countExpired();
            
            $totalApplications = $this->applicationModel->countAll();
            $recentApplications = $this->applicationModel->getRecent(10);
            
            $stats = [
                'jobs' => [
                    'total' => $totalJobs,
                    'open' => $openJobs,
                    'closed' => $expiredJobs,
                    'draft' => $this->recruitmentModel->count(0),
                    'archived' => $this->recruitmentModel->count(2)
                ],
                'applications' => [
                    'total' => $totalApplications,
                    'today' => $this->applicationModel->countToday(),
                    'this_week' => $this->applicationModel->countThisWeek(),
                    'this_month' => $this->applicationModel->countThisMonth(),
                    'recent' => $recentApplications
                ],
                'views' => [
                    'total_views' => $this->recruitmentModel->getTotalViews()
                ]
            ];
            
            $this->sendResponse($stats, 200, 'Lấy thống kê thành công');
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * POST /api/recruitment/bulk-delete - Xóa nhiều tin (admin)
     */
    public function bulkDelete()
    {
        try {
            $this->checkAdminAuth();
            
            $data = $this->getRequestData();
            $ids = $data['ids'] ?? [];
            
            if (empty($ids)) {
                $this->sendError('Vui lòng chọn tin tuyển dụng cần xóa', 400);
            }
            
            $deletedCount = 0;
            foreach ($ids as $id) {
                $job = $this->recruitmentModel->findById($id);
                if ($job) {
                    if ($job['image'] && $job['image'] !== 'default-job.webp') {
                        $imagePath = __DIR__ . '/../public/uploads/' . $job['image'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $this->applicationModel->deleteByRecruitmentId($id);
                    if ($this->recruitmentModel->delete($id)) {
                        $deletedCount++;
                    }
                }
            }
            
            $this->sendResponse([
                'deleted_count' => $deletedCount,
                'total_requested' => count($ids)
            ], 200, "Đã xóa {$deletedCount} tin tuyển dụng");
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * POST /api/recruitment/bulk-update-status - Cập nhật trạng thái nhiều tin (admin)
     */
    public function bulkUpdateStatus()
    {
        try {
            $this->checkAdminAuth();
            
            $data = $this->getRequestData();
            $ids = $data['ids'] ?? [];
            $status = (int)($data['status'] ?? 0);
            
            if (empty($ids)) {
                $this->sendError('Vui lòng chọn tin tuyển dụng cần cập nhật', 400);
            }
            
            $updatedCount = 0;
            foreach ($ids as $id) {
                if ($this->recruitmentModel->update($id, [':status' => $status])) {
                    $updatedCount++;
                }
            }
            
            $this->sendResponse([
                'updated_count' => $updatedCount,
                'new_status' => $status,
                'total_requested' => count($ids)
            ], 200, "Đã cập nhật trạng thái cho {$updatedCount} tin tuyển dụng");
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
    
    /**
     * Helper methods
     */
    private function validateRecruitmentData($data, $isUpdate = false)
    {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Tiêu đề không được để trống';
        }
        
        if (empty($data['work_location'])) {
            $errors['work_location'] = 'Địa điểm làm việc không được để trống';
        }
        
        if (empty($data['deadline'])) {
            $errors['deadline'] = 'Hạn nộp hồ sơ không được để trống';
        } elseif (strtotime($data['deadline']) < strtotime(date('Y-m-d'))) {
            $errors['deadline'] = 'Hạn nộp hồ sơ phải lớn hơn hoặc bằng ngày hiện tại';
        }
        
        if (isset($data['quantity']) && $data['quantity'] < 1) {
            $errors['quantity'] = 'Số lượng tuyển phải lớn hơn 0';
        }
        
        if (!isset($data['status']) && !$isUpdate) {
            $errors['status'] = 'Trạng thái không được để trống';
        }
        
        return $errors;
    }
    
    private function validateApplicationData($data)
    {
        $errors = [];
        
        if (empty($data['fullname'])) {
            $errors['fullname'] = 'Họ tên không được để trống';
        } elseif (strlen($data['fullname']) < 3) {
            $errors['fullname'] = 'Họ tên phải có ít nhất 3 ký tự';
        }
        
        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        if (empty($data['recruitment_id'])) {
            $errors['recruitment_id'] = 'ID tin tuyển dụng không hợp lệ';
        }
        
        return $errors;
    }
    
    private function createSlug($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        return strtolower(trim($string));
    }
    
    private function saveBase64Image($base64String, $oldImage = null)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $imageType = $matches[1];
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $imageData = base64_decode($base64String);
            
            $uploadDir = __DIR__ . '/../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Xóa ảnh cũ
            if ($oldImage && $oldImage !== 'default-job.webp' && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
            
            $fileName = uniqid() . '_' . time() . '.' . $imageType;
            file_put_contents($uploadDir . $fileName, $imageData);
            
            return $fileName;
        }
        
        return $oldImage ?: 'default-job.webp';
    }
    
    private function saveBase64File($base64String, $subDir = '', $allowedExt = ['pdf', 'doc', 'docx'])
    {
        if (preg_match('/^data:application\/(\w+);base64,/', $base64String, $matches)) {
            $fileType = $matches[1];
            if (!in_array($fileType, $allowedExt)) {
                return false;
            }
            
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $fileData = base64_decode($base64String);
            
            $uploadDir = __DIR__ . '/../public/uploads/' . $subDir;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . time() . '.' . $fileType;
            file_put_contents($uploadDir . $fileName, $fileData);
            
            return $fileName;
        }
        
        return false;
    }
    
    private function downloadFileFromUrl($url, $subDir = '')
    {
        $uploadDir = __DIR__ . '/../public/uploads/' . $subDir;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . time() . '.pdf';
        $fileContent = file_get_contents($url);
        
        if ($fileContent) {
            file_put_contents($uploadDir . $fileName, $fileContent);
            return $fileName;
        }
        
        return false;
    }
}