<?php
/**
 * ApplicationValidator Service
 * Handles validation logic for job applications
 */

class ApplicationValidator
{
    const MAX_CV_SIZE = 5242880; // 5MB
    const ALLOWED_CV_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    /**
     * Validate application form data
     *
     * @param array $data
     * @param array $files
     * @return array Validation errors
     */
    public function validate($data, $files)
    {
        $errors = [];

        // Validate fullname
        if (!$this->validateFullname($data['fullname'] ?? '')) {
            $errors['fullname'] = 'Vui lòng nhập họ tên';
        }

        // Validate phone
        $phoneError = $this->validatePhone($data['phone'] ?? '');
        if ($phoneError) {
            $errors['phone'] = $phoneError;
        }

        // Validate email
        $emailError = $this->validateEmail($data['email'] ?? '');
        if ($emailError) {
            $errors['email'] = $emailError;
        }

        // Validate CV file
        $cvError = $this->validateCV($files['filechon'] ?? null);
        if ($cvError) {
            $errors['cv'] = $cvError;
        }

        return $errors;
    }

    /**
     * Validate fullname
     *
     * @param string $fullname
     * @return bool
     */
    public function validateFullname($fullname)
    {
        return !empty(trim($fullname));
    }

    /**
     * Validate phone
     *
     * @param string $phone
     * @return string|null
     */
    public function validatePhone($phone)
    {
        $phone = trim($phone);

        if (empty($phone)) {
            return 'Vui lòng nhập số điện thoại';
        }

        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            return 'Số điện thoại không hợp lệ';
        }

        return null;
    }

    /**
     * Validate email
     *
     * @param string $email
     * @return string|null
     */
    public function validateEmail($email)
    {
        $email = trim($email);

        if (empty($email)) {
            return 'Vui lòng nhập email';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email không hợp lệ';
        }

        return null;
    }

    /**
     * Validate CV file
     *
     * @param array|null $file
     * @return string|null
     */
    public function validateCV($file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return 'Vui lòng upload CV của bạn';
        }

        $fileSize = $file['size'];
        $fileType = $file['type'];

        if ($fileSize > self::MAX_CV_SIZE) {
            return 'CV không được vượt quá 5MB';
        }

        if (!in_array($fileType, self::ALLOWED_CV_TYPES)) {
            return 'CV phải là file PDF hoặc Word';
        }

        return null;
    }
}

