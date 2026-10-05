<?php

/**
 * RecruitmentService
 * Business logic for recruitment operations
 */

require_once __DIR__ . '/../repositories/RecruitmentRepository.php';
require_once __DIR__ . '/FileUploadService.php';

class RecruitmentService
{
    private $recruitmentRepository;
    private $fileUploadService;

    public function __construct(?RecruitmentRepository $recruitmentRepository = null, ?FileUploadService $fileUploadService = null)
    {
        $this->recruitmentRepository = $recruitmentRepository ?? new RecruitmentRepository();
        $this->fileUploadService = $fileUploadService ?? new FileUploadService();
    }

    /**
     * Xóa tin tuyển dụng và hình ảnh liên quan.
     */
    public function deleteRecruitment($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        if ($validatedId === false) {
            return [
                'success' => false,
                'message' => 'ID tin tuyển dụng không hợp lệ!'
            ];
        }

        $recruitment = $this->recruitmentRepository->getById($validatedId);
        if (!$recruitment) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy tin tuyển dụng!'
            ];
        }

        if (!$this->recruitmentRepository->delete($validatedId)) {
            return [
                'success' => false,
                'message' => 'Không thể xóa tin tuyển dụng!'
            ];
        }

        $image = $recruitment['image'] ?? '';
        if ($image !== '' && $image !== 'default-job.webp') {
            $this->fileUploadService->delete('recruitments', $image);

            $legacyImage = ROOT_PATH . 'public/uploads/recruitments/' . $image;
            if (is_file($legacyImage)) {
                unlink($legacyImage);
            }
        }

        return [
            'success' => true,
            'message' => 'Đã xóa tin tuyển dụng thành công!'
        ];
    }

    /**
     * Validate and update a recruitment, including an optional new image.
     */
    public function updateRecruitment($id, $data, $file = null)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        if ($validatedId === false) {
            return ['success' => false, 'errors' => ['ID tin tuyển dụng không hợp lệ!']];
        }

        $recruitment = $this->recruitmentRepository->getById($validatedId);
        if (!$recruitment) {
            return ['success' => false, 'errors' => ['Không tìm thấy tin tuyển dụng!']];
        }

        $errors = array_values($this->validateRecruitmentData($data, true));
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $image = $recruitment['image'] ?? 'default-job.webp';
        $newImage = null;
        if (is_array($file) && !empty($file['name'])) {
            $uploadResult = $this->fileUploadService->upload($file, 'recruitments');
            if (!$uploadResult['success']) {
                return ['success' => false, 'errors' => [$uploadResult['message']]];
            }
            $newImage = $uploadResult['filename'];
            $image = $newImage;
        }

        $updateData = [
            'title' => trim($data['title']),
            'slug' => $recruitment['slug'] ?? '',
            'image' => $image,
            'work_location' => trim($data['work_location']),
            'degree' => $data['degree'] ?? 'Cao Đẳng - Đại Học',
            'work_type' => $data['work_type'] ?? 'Toàn thời gian',
            'quantity' => (int)$data['quantity'],
            'salary_range' => trim($data['salary_range']),
            'deadline' => $data['deadline'],
            'description' => $data['description'] ?? '',
            'requirements' => $data['requirements'] ?? '',
            'benefits' => $data['benefits'] ?? '',
            'status' => (int)($data['status'] ?? 0)
        ];

        if ($updateData['title'] !== ($recruitment['title'] ?? '')) {
            $updateData['slug'] = $this->generateUniqueSlug($updateData['title'], $validatedId);
        }

        if (!$this->recruitmentRepository->update($validatedId, $updateData)) {
            if ($newImage !== null) {
                $this->fileUploadService->delete('recruitments', $newImage);
            }
            return ['success' => false, 'errors' => ['Không thể cập nhật tin tuyển dụng!']];
        }

        if ($newImage !== null && !empty($recruitment['image']) && $recruitment['image'] !== 'default-job.webp') {
            $this->fileUploadService->delete('recruitments', $recruitment['image']);
            $legacyImage = ROOT_PATH . 'public/uploads/recruitments/' . $recruitment['image'];
            if (is_file($legacyImage)) {
                unlink($legacyImage);
            }
        }

        return ['success' => true];
    }

    /**
     * Get a recruitment for the admin edit form.
     */
    public function getRecruitmentById($id): ?array
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        return $validatedId === false
            ? null
            : $this->recruitmentRepository->getById($validatedId);
    }

    public function getRecruitmentBySlug($slug): ?array
    {
        $slug = trim((string)$slug);
        return $slug === '' ? null : $this->recruitmentRepository->getBySlug($slug);
    }

    /**
     * Validate dữ liệu tuyển dụng
     */
    public function validateRecruitmentData($data, $isUpdate = false)
    {
        $errors = [];

        // Kiểm tra tiêu đề
        if (empty($data['title']) || strlen(trim($data['title'])) < 5) {
            $errors['title'] = 'Tiêu đề phải có ít nhất 5 ký tự.';
        }

        // Kiểm tra địa điểm
        if (empty($data['work_location']) || strlen(trim($data['work_location'])) < 3) {
            $errors['work_location'] = 'Địa điểm làm việc không được để trống.';
        }

        // Kiểm tra số lượng
        if (empty($data['quantity']) || (int)$data['quantity'] < 1) {
            $errors['quantity'] = 'Số lượng tuyển phải lớn hơn 0.';
        }

        // Kiểm tra mức lương
        if (empty($data['salary_range']) || strlen(trim($data['salary_range'])) < 3) {
            $errors['salary_range'] = 'Mức lương không được để trống.';
        }

        // Kiểm tra hạn nộp
        if (empty($data['deadline'])) {
            $errors['deadline'] = 'Hạn nộp hồ sơ không được để trống.';
        } else {
            $deadline = DateTime::createFromFormat('Y-m-d', $data['deadline']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);

            if (!$deadline || $deadline < $today) {
                $errors['deadline'] = 'Hạn nộp phải là ngày trong tương lai.';
            }
        }

        // Kiểm tra mô tả
        if (empty($data['description']) || strlen(trim($data['description'])) < 20) {
            $errors['description'] = 'Mô tả công việc phải có ít nhất 20 ký tự.';
        }

        // Kiểm tra yêu cầu
        if (empty($data['requirements']) || strlen(trim($data['requirements'])) < 20) {
            $errors['requirements'] = 'Yêu cầu ứng viên phải có ít nhất 20 ký tự.';
        }

        // Kiểm tra quyền lợi
        if (empty($data['benefits']) || strlen(trim($data['benefits'])) < 10) {
            $errors['benefits'] = 'Quyền lợi được hưởng phải có ít nhất 10 ký tự.';
        }

        return $errors;
    }

    private function generateSlug($title)
    {
        $title = strtr($title, [
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'Ì' => 'I',
            'Í' => 'I',
            'ì' => 'i',
            'í' => 'i',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'Ù' => 'U',
            'Ú' => 'U',
            'ù' => 'u',
            'ú' => 'u',
            'Đ' => 'D',
            'đ' => 'd'
        ]);
        return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]/', '-', strtolower(trim($title)))), '-');
    }

    private function generateUniqueSlug($title, $excludeId)
    {
        $slug = $this->generateSlug($title);
        $baseSlug = $slug;
        $counter = 1;

        while ($this->recruitmentRepository->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Lấy danh sách tin tuyển dụng với phân trang
     */
    public function getRecruitmentsList($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $recruitments = $this->recruitmentRepository->getActiveRecruitmentsPaginated($limit, $offset);
        $total = $this->recruitmentRepository->countActive();
        $totalPages = ceil($total / $limit);

        return [
            'recruitments' => $recruitments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'limit' => $limit
        ];
    }

    /**
     * Lấy chi tiết tin tuyển dụng
     */
    public function getRecruitmentDetail($slug)
    {
        $recruitment = $this->recruitmentRepository->getDetail($slug);

        return $recruitment;
    }

    /**
     * Lấy tin tuyển dụng liên quan
     */
    public function getRelatedRecruitments($position, $recruitmentId, $limit = 4)
    {
        $related = $this->recruitmentRepository->getByPosition($position, $limit + 1);

        // Loại bỏ tin hiện tại
        return array_filter($related, function ($item) use ($recruitmentId) {
            return $item['id'] != $recruitmentId;
        });
    }

    /**
     * Lấy tin tuyển dụng nổi bật
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        return $this->recruitmentRepository->getFeaturedRecruitments($limit);
    }

    /**
     * Lấy tất cả vị trí
     */
    public function getAllPositions()
    {
        return $this->recruitmentRepository->getAllPositions();
    }

    /**
     * Tìm kiếm tin tuyển dụng
     */
    public function searchRecruitments($keyword = '', $position = '', $limit = 20)
    {
        if (!empty($keyword)) {
            return $this->recruitmentRepository->search($keyword, $limit, 0);
        } elseif (!empty($position)) {
            return $this->recruitmentRepository->getByPosition($position, $limit);
        } else {
            return $this->recruitmentRepository->getActiveRecruitments($limit);
        }
    }
}
