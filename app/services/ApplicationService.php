<?php
/**
 * ApplicationService
 * Business logic for application operations
 */

require_once __DIR__ . '/../repositories/ApplicationRepository.php';
require_once __DIR__ . '/ApplicationValidator.php';
require_once __DIR__ . '/FileUploadService.php';

class ApplicationService
{
    private $applicationRepository;
    private $validator;
    private $fileUploadService;

    public function __construct(
        ApplicationRepository $applicationRepository = null,
        ApplicationValidator $validator = null,
        FileUploadService $fileUploadService = null
    ) {
        $this->applicationRepository = $applicationRepository ?? new ApplicationRepository();
        $this->validator = $validator ?? new ApplicationValidator();
        $this->fileUploadService = $fileUploadService ?? new FileUploadService();
    }

    /**
     * Process job application
     *
     * @param int $recruitmentId
     * @param array $postData
     * @param array $files
     * @param string $ipAddress
     * @return array ['success' => bool, 'errors' => array, 'message' => string]
     */
    public function processApplication($recruitmentId, $postData, $files, $ipAddress)
    {
        // Validate input
        $errors = $this->validator->validate($postData, $files);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'message' => 'Dữ liệu không hợp lệ'
            ];
        }

        $email = trim($postData['email']);

        // Check duplicate application
        if ($this->applicationRepository->hasApplied($recruitmentId, $email, $ipAddress)) {
            return [
                'success' => false,
                'errors' => ['duplicate' => 'Bạn đã ứng tuyển vị trí này rồi. Vui lòng chờ phản hồi từ chúng tôi.'],
                'message' => 'Ứng tuyển trùng lặp'
            ];
        }

        // Upload CV
        $fullname = trim($postData['ten']);
        $uploadResult = $this->fileUploadService->uploadCV($files['filechon'] ?? null, $fullname);

        if (!$uploadResult['success']) {
            return [
                'success' => false,
                'errors' => ['cv' => $uploadResult['error']],
                'message' => 'Lỗi upload file'
            ];
        }

        // Save application
        $applicationData = [
            'recruitment_id' => $recruitmentId,
            'fullname' => $fullname,
            'phone' => trim($postData['dt']),
            'email' => $email,
            'content' => trim($postData['noidung'] ?? ''),
            'cv_file' => $uploadResult['path'],
            'ip_address' => $ipAddress
        ];

        if ($this->applicationRepository->save($applicationData)) {
            return [
                'success' => true,
                'errors' => [],
                'message' => 'Cảm ơn bạn đã ứng tuyển. Chúng tôi sẽ liên hệ lại sớm nhất!'
            ];
        }

        return [
            'success' => false,
            'errors' => [],
            'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
        ];
    }

    /**
     * Get applications by recruitment ID
     *
     * @param int $recruitmentId
     * @return array
     */
    public function getApplicationsByRecruitmentId($recruitmentId)
    {
        return $this->applicationRepository->getByRecruitmentId($recruitmentId);
    }

    /**
     * Check if user has applied
     *
     * @param int $recruitmentId
     * @param string $email
     * @param string $ipAddress
     * @return bool
     */
    public function hasApplied($recruitmentId, $email, $ipAddress)
    {
        return $this->applicationRepository->hasApplied($recruitmentId, $email, $ipAddress);
    }
}

