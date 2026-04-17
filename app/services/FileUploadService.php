<?php
/**
 * FileUploadService
 * Handles file upload operations
 */

class FileUploadService
{
    private $uploadBaseDir;
    private $uploadSubDir = 'cv';

    public function __construct()
    {
        $this->uploadBaseDir = $_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/uploads/';
    }

    /**
     * Upload CV file
     *
     * @param array $file
     * @param string $fullname
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function uploadCV($file, $fullname)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Lỗi upload file'
            ];
        }

        try {
            $uploadDir = $this->uploadBaseDir . $this->uploadSubDir . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $fullname) . '.' . $fileExt;
            $relativePath = 'uploads/' . $this->uploadSubDir . '/' . $fileName;
            $fullPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                return [
                    'success' => true,
                    'path' => $relativePath,
                    'error' => null
                ];
            }

            return [
                'success' => false,
                'path' => null,
                'error' => 'Không thể upload CV. Vui lòng thử lại.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'path' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete file
     *
     * @param string $filePath Relative path to file
     * @return bool
     */
    public function deleteFile($filePath)
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/' . $filePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}

