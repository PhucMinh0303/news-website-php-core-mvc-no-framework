<?php
// app/services/FileUploadService.php

class FileUploadService
{
    private $uploadPath;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private $maxSize = 15 * 1024 * 1024; // 15MB

    public function __construct()
    {
        $this->uploadPath = __DIR__ . '/../../public/upload/';

        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Upload file
     */
    public function upload($file, $subDir = '')
    {
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => $this->getUploadErrorMessage($file['error'])];
        }

        // Kiểm tra định dạng file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận file ảnh JPG, PNG, WEBP, GIF'];
        }

        // Kiểm tra kích thước
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'message' => 'File quá lớn. Tối đa 15MB'];
        }

        // Tạo tên file mới (không trùng)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;

        // Xác định đường dẫn lưu
        $uploadDir = $this->uploadPath;
        if (!empty($subDir)) {
            $uploadDir .= $subDir . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
        }

        $filePath = $uploadDir . $filename;

        // Di chuyển file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filePath
            ];
        }

        return ['success' => false, 'message' => 'Không thể lưu file. Vui lòng kiểm tra quyền ghi thư mục.'];
    }

    /**
     * Xóa file
     */
    public function delete($subDir, $filename)
    {
        if (empty($filename) || $filename == 'default-job.webp') {
            return true;
        }

        $filePath = $this->uploadPath . $subDir . '/' . $filename;
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    /**
     * Lấy thông báo lỗi upload
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File vượt quá giới hạn cho phép của server (upload_max_filesize).';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File vượt quá giới hạn cho phép của form.';
            case UPLOAD_ERR_PARTIAL:
                return 'File chỉ được upload một phần.';
            case UPLOAD_ERR_NO_FILE:
                return 'Không có file nào được chọn.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Thiếu thư mục tạm để lưu file.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Không thể ghi file vào thư mục.';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bị chặn bởi PHP extension.';
            default:
                return 'Lỗi không xác định khi upload file.';
        }
    }
}
