<?php

/**
 * edit-recruitment.php - View chỉnh sửa tin tuyển dụng trong admin panel
 * Form gửi dữ liệu đến route admin/recruitment/update/{id}
 *
 * Biến nhận từ controller:
 * - $recruitment : array dữ liệu bản ghi cần sửa (BẮT BUỘC)
 * - $edit_id     : int ID bản ghi (BẮT BUỘC)
 * - $errors      : array lỗi validation (tùy chọn)
 */

// Dữ liệu từ database là nền; old_input chỉ ghi đè khi update validation fail.
$formData = array_merge(
    is_array($recruitment ?? null) ? $recruitment : [],
    is_array($_SESSION['old_input'] ?? null) ? $_SESSION['old_input'] : []
);
$errors = is_array($_SESSION['errors'] ?? null) ? $_SESSION['errors'] : ($errors ?? []);

// ID bản ghi đang sửa — BẮT BUỘC
$editId = isset($edit_id) ? (int) $edit_id : (int) ($formData['id'] ?? 0);

// Nếu không có ID hợp lệ → không thể edit, quay về danh sách
if ($editId <= 0) {
    header('Location: ' . Router::url('admin/recruitment'));
    exit;
}

// Xử lý ảnh hiện tại
$existingImageUrl = null;
$hasExistingImage = false;
if (!empty($formData['image']) && $formData['image'] !== 'default-job.webp') {
    $imageDirectories = ['public/upload/recruitments/', 'public/uploads/recruitments/'];
    foreach ($imageDirectories as $imageDirectory) {
        if (is_file(ROOT_PATH . $imageDirectory . $formData['image'])) {
            $existingImageUrl  = BASE_URL . $imageDirectory . rawurlencode($formData['image']);
            $hasExistingImage  = true;
            break;
        }
    }
}

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa tin tuyển dụng</title>
</head>

<body>
    <main class="main create-page">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Có lỗi xảy ra:</strong>
                <?php foreach ($errors as $error): ?>
                    <div>• <?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="add-header">
            TRANG ADMIN - CHỈNH SỬA TIN TUYỂN DỤNG
            <span style="font-size: 13px; font-weight: normal; opacity: 0.7; margin-left: 10px;">
                (ID: #<?php echo $editId; ?>)
            </span>
        </div>

        <form method="POST"
            action="<?php echo htmlspecialchars(Router::url('admin/recruitment/update', [$editId])); ?>"
            enctype="multipart/form-data"
            id="recruitmentForm"
            data-edit-mode="1"
            data-recruitment-id="<?php echo $editId; ?>">

            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id" value="<?php echo $editId; ?>">
            <!-- Giữ lại ảnh cũ nếu user không chọn ảnh mới -->
            <input type="hidden" name="existing_image"
                value="<?php echo htmlspecialchars($formData['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <div class="add-container">

                <!-- Tiêu đề -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Tiêu đề tin tuyển dụng: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="recruitmentForm.generateAITitle()">✨ Gợi ý bằng AI</button>
                    </div>
                    <input class="input-title" type="text" name="title" id="recruitment_title"
                        placeholder="Nhập tiêu đề tin tuyển dụng tại đây..."
                        value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>"
                        autocomplete="off">
                    <small>Nhập tiêu đề, slug sẽ tự động tạo từ bảng chữ cái</small>
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label>Slug (URL):</label>
                    <input type="hidden" name="slug_original" id="slug_original"
                        value="<?php echo htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input class="input-slug" type="text" name="slug" id="slug"
                        placeholder="[ Tự động tạo từ tiêu đề ]"
                        value="<?php echo htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        readonly>
                    <small>(Slug được tạo tự động từ tiêu đề, chỉ gồm chữ cái, số và dấu gạch ngang)</small>
                </div>

                <!-- Địa điểm -->
                <div class="form-group">
                    <label>Địa điểm làm việc: <span class="required">*</span></label>
                    <textarea class="input-location" name="work_location" rows="2"
                        placeholder="Địa chỉ cụ thể..."><?php echo htmlspecialchars($formData['work_location'] ?? ''); ?></textarea>
                </div>

                <!-- Trình độ + Hình thức -->
                <div class= "grid-2cols">
                    <div class="form-group">
                        <label>Trình độ yêu cầu: <span class="required">*</span></label>
                        <select name="degree">
                            <?php
                            $degreeOptions = ['Không yêu cầu', 'Trung Cấp', 'Cao Đẳng - Đại Học', 'Cao Học', 'Tiến Sĩ'];
                            $currentDegree = $formData['degree'] ?? 'Không yêu cầu';
                            foreach ($degreeOptions as $opt):
                            ?>
                                <option value="<?php echo $opt; ?>" <?php echo ($currentDegree === $opt) ? 'selected' : ''; ?>>
                                    <?php echo $opt === 'Không yêu cầu' ? 'Không yêu cầu bằng cấp' : $opt; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hình thức làm việc:</label>
                        <select name="work_type">
                            <?php
                            $workTypeOptions = ['Toàn thời gian', 'Bán thời gian', 'Hợp đồng'];
                            $currentWorkType = $formData['work_type'] ?? 'Toàn thời gian';
                            foreach ($workTypeOptions as $opt):
                            ?>
                                <option value="<?php echo $opt; ?>" <?php echo ($currentWorkType === $opt) ? 'selected' : ''; ?>>
                                    <?php echo $opt; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Số lượng + Lương + Deadline -->
                <div class="grid-2cols">
                    <div class="form-group">
                        <label>Số lượng cần tuyển: <span class="required">*</span></label>
                        <input type="number" name="quantity" min="1"
                            value="<?php echo (int)($formData['quantity'] ?? 1); ?>">
                    </div>
                    <div class="form-group">
                        <label>Mức lương: <span class="required">*</span></label>
                        <input type="text" name="salary_range" id="salary_range"
                            placeholder="VD: 15.000.000 - 20.000.000 VNĐ hoặc Thỏa thuận"
                            value="<?php echo htmlspecialchars($formData['salary_range'] ?? ''); ?>">
                        <input type="hidden" name="salary" id="salary_value"
                            value="<?php echo htmlspecialchars($formData['salary'] ?? ''); ?>">
                        <small id="salary_error" class="error-text"></small>
                    </div>
                    <div class="form-group">
                        <label>Hạn nộp hồ sơ: <span class="required">*</span></label>
                        <input class="input-deadline" name="deadline" id="deadline" type="date"
                            value="<?php echo htmlspecialchars($formData['deadline'] ?? ''); ?>">
                        <!-- KHÔNG set min cho edit: giữ nguyên ngày cũ dù đã qua -->
                        <small class="error-message deadline-error"
                            style="display: none; color: #dc2626; font-size: 12px; margin-top: 5px;"></small>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Mô tả công việc: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIDescription()">✨ Gợi ý bằng AI</button>
                    </div>
                    <textarea name="description" id="job_description" rows="8"
                        placeholder="Mô tả chi tiết công việc... (hỗ trợ HTML)"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                    <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
                </div>

                <!-- Yêu cầu -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Yêu cầu ứng viên: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIRequirements()">✨ Gợi ý bằng AI</button>
                    </div>
                    <textarea name="requirements" id="job_requirements" rows="8"
                        placeholder="Các yêu cầu về kỹ năng, kinh nghiệm, bằng cấp..."><?php echo htmlspecialchars($formData['requirements'] ?? ''); ?></textarea>
                    <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
                </div>

                <!-- Quyền lợi -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Quyền lợi được hưởng: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIBenefits()">✨ Gợi ý bằng AI</button>
                    </div>
                    <textarea name="benefits" id="job_benefits" rows="6"
                        placeholder="Bảo hiểm, thưởng, cơ hội thăng tiến..."><?php echo htmlspecialchars($formData['benefits'] ?? ''); ?></textarea>
                    <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
                </div>

                <!-- Bottom: Trạng thái + Ảnh -->
                <div class="bottom-layout">
                    <div class="publish-settings">
                        <h3>CẤU HÌNH ĐĂNG TIN</h3>

                        <label>TRẠNG THÁI</label>
                        <select name="status">
                            <?php $currentStatus = (string)($formData['status'] ?? '0'); ?>
                            <option value="0" <?php echo $currentStatus === '0' ? 'selected' : ''; ?>>
                                Bản nháp (Draft)
                            </option>
                            <option value="1" <?php echo $currentStatus === '1' ? 'selected' : ''; ?>>
                                Đang đăng (Open)
                            </option>
                            <option value="2" <?php echo $currentStatus === '2' ? 'selected' : ''; ?>>
                                Đã đóng (Closed)
                            </option>
                        </select>

                        <small>
                            • <strong>Bản nháp</strong>: Chưa hiển thị ra ngoài<br>
                            • <strong>Đã đăng</strong>: Hiển thị công khai trên website<br>
                            • <strong>Đã đóng</strong>: Ẩn khỏi giao diện người dùng
                        </small>

                        <!-- Metadata -->
                        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px dashed #d1d5db; font-size: 12px; color: #6b7280;">
                            <?php if (!empty($formData['created_at'])): ?>
                                <div>📅 Ngày tạo: <?php echo date('d/m/Y H:i', strtotime($formData['created_at'])); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($formData['updated_at'])): ?>
                                <div>✏️ Cập nhật: <?php echo date('d/m/Y H:i', strtotime($formData['updated_at'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="thumbnail-box">
                        <div class="thumb-header">
                            ẢNH ĐẠI DIỆN TIN TUYỂN DỤNG
                            <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIImage()">✨ Tạo bằng AI</button>
                        </div>

                        <div class="upload-box" id="uploadBox"
                            data-existing-image="<?php echo htmlspecialchars($existingImageUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <span id="uploadIcon">📷</span>
                            <p id="uploadText">
                                <?php echo $hasExistingImage
                                    ? 'Ảnh hiện tại (chọn ảnh mới để thay thế)'
                                    : 'Tải lên ảnh đại diện (JPG, PNG, WEBP)'; ?>
                            </p>
                            <small id="uploadInfo">
                                <?php echo $hasExistingImage
                                    ? 'Bỏ trống nếu không muốn thay đổi ảnh'
                                    : 'Mặc định: default-job.webp (Max 15MB)'; ?>
                            </small>
                        </div>
                        <input type="file" id="imageInput" name="image"
                            accept="image/jpeg,image/png,image/webp" style="display: none;">

                        <div id="imagePreview" style="margin-top: 10px; display: <?php echo $hasExistingImage ? 'block' : 'none'; ?>;">
                            <img id="previewImg"
                                src="<?php echo htmlspecialchars($existingImageUrl ?? '#', ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Preview"
                                style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                            <?php if ($hasExistingImage): ?>
                                <button type="button" id="removeImageBtn"
                                    style="display:block; margin-top:6px; font-size:12px; color:#dc2626; background:none; border:none; cursor:pointer;">
                                    ✕ Xóa ảnh này (sẽ dùng ảnh mặc định)
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="action-buttons">
                    <button type="submit" name="save_draft" value="0" class="btn-draft">
                        Lưu nháp
                    </button>
                    <button type="submit" name="publish" value="1" class="btn-publish">
                        Cập nhật &amp; Đăng
                    </button>
                    <button type="submit" name="save_and_continue" value="1" class="btn-secondary">
                        Lưu và tiếp tục
                    </button>

                    <!-- Nút xóa (tùy chọn, có thể ẩn nếu chỉ muốn xóa ở list) -->
                    <a href="<?php echo htmlspecialchars(Router::url('admin/recruitment/delete', [$editId])); ?>"
                        class="btn-danger"
                        onclick="return confirm('Bạn có chắc muốn xóa tin này? Hành động không thể hoàn tác.');"
                        style="margin-left:auto; background:#dc2626; color:#fff; padding:10px 18px; border-radius:6px; text-decoration:none;">
                        🗑 Xóa tin
                    </a>
                </div>

                <div class="cancel-text" data-page="recruitment">
                    Hủy bỏ và quay lại
                </div>

            </div>
        </form>
    </main>

    <!-- Truyền lỗi từ PHP sang JS -->
    <script>
        window.serverErrors = <?php echo json_encode($errors); ?>;
        window.EDIT_MODE = true;
        window.RECRUITMENT_ID = <?php echo $editId; ?>;
    </script>
</body>

</html>