<?php
// admin/recruitment/store.php
require_once '../../config/database.php';
require_once '../../models/RecruitmentModel.php';

session_start();

// Khởi tạo model
$recruitmentModel = new RecruitmentModel();

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/recruitment');
    exit;
}

// Validate dữ liệu
$errors = [];

if (empty($_POST['title'])) {
    $errors[] = 'Tiêu đề không được để trống!';
} elseif (strlen($_POST['title']) < 5) {
    $errors[] = 'Tiêu đề phải có ít nhất 5 ký tự!';
}

if (empty($_POST['work_location'])) {
    $errors[] = 'Địa điểm làm việc không được để trống!';
}

if (empty($_POST['deadline'])) {
    $errors[] = 'Hạn nộp hồ sơ không được để trống!';
} elseif (strtotime($_POST['deadline']) < strtotime(date('Y-m-d'))) {
    $errors[] = 'Hạn nộp hồ sơ phải lớn hơn hoặc bằng ngày hiện tại!';
}

if (isset($_POST['quantity']) && (int)$_POST['quantity'] <= 0) {
    $errors[] = 'Số lượng tuyển phải lớn hơn 0!';
}

// Xử lý upload ảnh
$imageName = 'default-job.webp';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../public/uploads/recruitments/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        $errors[] = 'Chỉ chấp nhận file ảnh (JPG, PNG, WEBP)';
    } elseif ($_FILES['image']['size'] > $maxSize) {
        $errors[] = 'Kích thước ảnh không được vượt quá 2MB';
    } else {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imageName = $filename;
        } else {
            $errors[] = 'Upload ảnh thất bại!';
        }
    }
}

// Nếu không có lỗi, tiến hành thêm mới
if (empty($errors)) {
    $data = [
        'title' => $_POST['title'],
        'image' => $imageName,
        'work_location' => $_POST['work_location'],
        'degree' => $_POST['degree'] ?? 'Cao Đẳng - Đại Học',
        'quantity' => (int)$_POST['quantity'],
        'salary_range' => $_POST['salary_range'] ?? null,
        'deadline' => $_POST['deadline'],
        'description' => $_POST['description'] ?? null,
        'requirements' => $_POST['requirements'] ?? null,
        'benefits' => $_POST['benefits'] ?? null,
        'status' => (int)($_POST['status'] ?? 1)
    ];

    $result = $recruitmentModel->create($data);
    
    if ($result) {
        $_SESSION['success'] = 'Thêm tin tuyển dụng thành công!';
        header('Location: /admin/recruitment');
        exit;
    } else {
        $errors[] = 'Có lỗi xảy ra, vui lòng thử lại!';
    }
}

// Nếu có lỗi, lưu lại và quay về form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: /admin/recruitment/create');
    exit;
}
?>