<?php
/**
 * add_recruitment.php - View thêm tin tuyển dụng mới trong admin panel
 * Chỉ hiển thị form, không xử lý logic (logic đã chuyển sang RecruitmentController)
 */

/* Kiểm tra đăng nhập admin - Đảm bảo chỉ admin mới có quyền truy cập trang này
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/login');
    exit;
}*/

// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';

// Xóa session data sau khi lấy
unset($_SESSION['form_data']);
unset($_SESSION['errors']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng tin tuyển dụng - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .add-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .add-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .label-row label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }

        .ai-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .ai-btn:hover {
            transform: translateY(-1px);
        }

        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-title, .input-slug, .input-location {
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            font-family: inherit;
        }

        small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #6b7280;
        }

        .bottom-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
            padding-top: 20px;
            border-top: 2px solid #f3f4f6;
        }

        .publish-settings h3, .thumbnail-box .thumb-header {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #374151;
        }

        .thumbnail-box .thumb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .upload-box {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f9fafb;
        }

        .upload-box:hover {
            border-color: #667eea;
            background: #f3f4f6;
        }

        #uploadIcon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }

        #uploadText {
            color: #4b5563;
            margin-bottom: 8px;
        }

        #uploadInfo {
            color: #9ca3af;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-draft, .btn-publish, .btn-secondary {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-draft {
            background: #6b7280;
            color: white;
        }

        .btn-draft:hover {
            background: #4b5563;
        }

        .btn-publish {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-publish:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #10b981;
            color: white;
        }

        .btn-secondary:hover {
            background: #059669;
        }

        .cancel-text {
            text-align: center;
            margin-top: 20px;
            color: #ef4444;
            cursor: pointer;
            font-size: 14px;
        }

        .cancel-text:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 20px;
            margin: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #dcfce7;
            color: #10b981;
            border-left: 4px solid #10b981;
        }

        .error-text {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }

        .toast-success {
            background: #10b981;
            color: white;
        }

        .toast-error {
            background: #ef4444;
            color: white;
        }

        .toast-info {
            background: #3b82f6;
            color: white;
        }
    </style>
</head>
<body>

<main class="main">

    <!-- Hiển thị thông báo lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div>• <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Hiển thị thông báo thành công -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="add-header">
        TRANG ADMIN - ĐĂNG TIN TUYỂN DỤNG
    </div>

    <!-- FORM - action gọi đến controller -->
    <form method="POST" action="/admin/recruitment/store" enctype="multipart/form-data" id="recruitmentForm">
        <div class="add-container">

            <!-- TITLE -->
            <div class="form-group">
                <div class="label-row">
                    <label>Tiêu đề tin tuyển dụng: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAITitle()">✨ Gợi ý bằng AI</span>
                </div>
                <input class="input-title" type="text" name="recruitment_title" id="recruitment_title"
                       placeholder="Nhập tiêu đề tin tuyển dụng tại đây..."
                       value="<?php echo htmlspecialchars($formData['recruitment_title'] ?? ''); ?>"
                       onkeyup="generateSlug()" required>
                <?php if (isset($errors['recruitment_title'])): ?>
                    <div class="error-text"><?php echo $errors['recruitment_title']; ?></div>
                <?php endif; ?>
            </div>

            <!-- SLUG (auto-generated from title) -->
            <div class="form-group">
                <label>Slug (URL):</label>
                <input class="input-slug" type="text" name="slug" id="slug"
                       placeholder="Tự động tạo từ tiêu đề"
                       value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>">
                <small>Slug sẽ được tạo tự động từ tiêu đề</small>
            </div>

            <!-- WORK LOCATION -->
            <div class="form-group">
                <label>Địa điểm làm việc:</label>
                <textarea class="input-location" name="location" rows="2"
                          placeholder="Địa chỉ cụ thể..."><?php echo htmlspecialchars($formData['location'] ?? ''); ?></textarea>
            </div>

            <!-- POSITION & EXPERIENCE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Vị trí tuyển dụng:</label>
                    <input type="text" name="position"
                           placeholder="VD: Trưởng phòng, Chuyên viên, Nhân viên..."
                           value="<?php echo htmlspecialchars($formData['position'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Kinh nghiệm yêu cầu:</label>
                    <input type="text" name="experience"
                           placeholder="VD: 2 năm, Không yêu cầu kinh nghiệm..."
                           value="<?php echo htmlspecialchars($formData['experience'] ?? ''); ?>">
                </div>
            </div>

            <!-- DEGREE & JOB TYPE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Trình độ yêu cầu:</label>
                    <select name="education">
                        <option value="Không yêu cầu" <?php echo (($formData['education'] ?? '') == 'Không yêu cầu') ? 'selected' : ''; ?>>
                            Không yêu cầu bằng cấp
                        </option>
                        <option value="Trung Cấp" <?php echo (($formData['education'] ?? '') == 'Trung Cấp') ? 'selected' : ''; ?>>
                            Trung Cấp
                        </option>
                        <option value="Cao Đẳng - Đại Học" <?php echo (($formData['education'] ?? 'Cao Đẳng - Đại Học') == 'Cao Đẳng - Đại Học') ? 'selected' : ''; ?>>
                            Cao Đẳng - Đại Học
                        </option>
                        <option value="Cao Học" <?php echo (($formData['education'] ?? '') == 'Cao Học') ? 'selected' : ''; ?>>
                            Cao Học
                        </option>
                        <option value="Tiến Sĩ" <?php echo (($formData['education'] ?? '') == 'Tiến Sĩ') ? 'selected' : ''; ?>>
                            Tiến Sĩ
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hình thức làm việc:</label>
                    <select name="job_type">
                        <option value="fulltime" <?php echo (($formData['job_type'] ?? 'fulltime') == 'fulltime') ? 'selected' : ''; ?>>
                            Toàn thời gian
                        </option>
                        <option value="parttime" <?php echo (($formData['job_type'] ?? '') == 'parttime') ? 'selected' : ''; ?>>
                            Bán thời gian
                        </option>
                        <option value="contract" <?php echo (($formData['job_type'] ?? '') == 'contract') ? 'selected' : ''; ?>>
                            Hợp đồng
                        </option>
                        <option value="internship" <?php echo (($formData['job_type'] ?? '') == 'internship') ? 'selected' : ''; ?>>
                            Thực tập
                        </option>
                    </select>
                </div>
            </div>

            <!-- QUANTITY & SALARY RANGE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Số lượng cần tuyển:</label>
                    <input type="number" name="quantity" min="1"
                           value="<?php echo (int)($formData['quantity'] ?? 1); ?>">
                </div>
                <div class="form-group">
                    <label>Mức lương:</label>
                    <input type="text" name="salary_range"
                           placeholder="VD: 15.000.000 - 20.000.000 VNĐ hoặc Thỏa thuận"
                           value="<?php echo htmlspecialchars($formData['salary_range'] ?? ''); ?>">
                </div>
            </div>

            <!-- DEADLINE -->
            <div class="form-group">
                <label>Hạn nộp hồ sơ: <span style="color: red;">*</span></label>
                <input class="input-deadline" type="date" name="deadline"
                       value="<?php echo htmlspecialchars($formData['deadline'] ?? ''); ?>" required>
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <div class="label-row">
                    <label>Mô tả công việc: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAIDescription()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea name="job_description" id="job_description" rows="8"
                          placeholder="Mô tả chi tiết công việc... (hỗ trợ HTML)"><?php echo htmlspecialchars($formData['job_description'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- REQUIREMENTS -->
            <div class="form-group">
                <div class="label-row">
                    <label>Yêu cầu ứng viên: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAIRequirements()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea name="job_requirements" id="job_requirements" rows="8"
                          placeholder="Các yêu cầu về kỹ năng, kinh nghiệm, bằng cấp..."><?php echo htmlspecialchars($formData['job_requirements'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BENEFITS -->
            <div class="form-group">
                <div class="label-row">
                    <label>Quyền lợi được hưởng:</label>
                    <span class="ai-btn" type="button" onclick="generateAIBenefits()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea name="job_benefits" id="job_benefits" rows="6"
                          placeholder="Bảo hiểm, thưởng, cơ hội thăng tiến..."><?php echo htmlspecialchars($formData['job_benefits'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BOTTOM LAYOUT -->
            <div class="bottom-layout">
                <div class="publish-settings">
                    <h3>CẤU HÌNH ĐĂNG TIN</h3>

                    <label>TRẠNG THÁI</label>
                    <select name="status">
                        <option value="draft" <?php echo (($formData['status'] ?? 'draft') == 'draft') ? 'selected' : ''; ?>>
                            Bản nháp (Draft)
                        </option>
                        <option value="open" <?php echo (($formData['status'] ?? '') == 'open') ? 'selected' : ''; ?>>
                            Đang đăng (Open)
                        </option>
                        <option value="closed" <?php echo (($formData['status'] ?? '') == 'closed') ? 'selected' : ''; ?>>
                            Đã đóng (Closed)
                        </option>
                        <option value="filled" <?php echo (($formData['status'] ?? '') == 'filled') ? 'selected' : ''; ?>>
                            Đã tuyển đủ (Filled)
                        </option>
                    </select>

                    <small>• Draft: Lưu tạm, chưa hiển thị<br>
                        • Open: Đang tuyển dụng<br>
                        • Closed: Ngưng nhận hồ sơ<br>
                        • Filled: Đã tuyển đủ chỉ tiêu</small>
                </div>

                <div class="thumbnail-box">
                    <div class="thumb-header">
                        ẢNH ĐẠI DIỆN TIN TUYỂN DỤNG
                        <span class="ai-btn" type="button" onclick="generateAIImage()">✨ Tạo bằng AI</span>
                    </div>

                    <div class="upload-box" id="uploadBox" onclick="document.getElementById('imageInput').click()">
                        <span id="uploadIcon">📷</span>
                        <p id="uploadText">Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                        <small id="uploadInfo">Mặc định: default-job.webp</small>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                           style="display: none;" onchange="previewImage(this)">
                    <div id="imagePreview" style="margin-top: 10px; display: none;">
                        <img id="previewImg" src="#" alt="Preview"
                             style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-buttons">
                <button type="submit" name="save_draft" value="draft" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="publish" value="open" class="btn-publish">Đăng tin</button>
                <button type="submit" name="save_and_continue" value="1" class="btn-secondary">Lưu và tiếp tục</button>
            </div>

            <div class="cancel-text" onclick="window.location.href='/admin/recruitment'">Hủy bỏ và quay lại</div>

        </div>
    </form>
</main>

<script>
    // Tạo slug từ title
    function generateSlug() {
        var title = document.getElementById('recruitment_title').value;
        var slug = title.toLowerCase()
            .replace(/[^\w\s]/gi, '')
            .replace(/\s+/g, '-')
            .substring(0, 100);
        document.getElementById('slug').value = slug;
    }

    // Preview ảnh trước khi upload
    function previewImage(input) {
        var preview = document.getElementById('imagePreview');
        var previewImg = document.getElementById('previewImg');
        var uploadBox = document.getElementById('uploadBox');
        var uploadText = document.getElementById('uploadText');
        var uploadInfo = document.getElementById('uploadInfo');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                uploadBox.style.opacity = '0.5';
                uploadText.innerHTML = 'Đã chọn ảnh: ' + input.files[0].name;
                uploadInfo.innerHTML = 'Click để đổi ảnh khác';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AI suggest functions
    function generateAITitle() {
        var titles = [
            "Tuyển dụng [Vị trí] - Lương hấp dẫn - Môi trường chuyên nghiệp",
            "Cần tuyển gấp [Vị trí] - Thưởng Tết hấp dẫn - Lương thỏa thuận",
            "[Tên công ty] tuyển dụng [Vị trí] - Lương cạnh tranh - Cơ hội thăng tiến",
            "Urgent! Tuyển [Vị trí] - Làm việc tại Quận 1 - Lương 15-20tr"
        ];
        var randomTitle = titles[Math.floor(Math.random() * titles.length)];
        document.getElementById('recruitment_title').value = randomTitle;
        generateSlug();
        showToast("Đã tạo gợi ý tiêu đề!", "success");
    }

    function generateAIDescription() {
        var description = `<p><strong>Mô tả công việc:</strong></p>
<ul>
    <li>Thực hiện các công việc chuyên môn theo đúng quy trình của công ty</li>
    <li>Phối hợp với các phòng ban để đảm bảo tiến độ công việc</li>
    <li>Báo cáo kết quả công việc định kỳ cho cấp trên trực tiếp</li>
    <li>Tham gia các dự án theo sự phân công của quản lý</li>
    <li>Đề xuất các giải pháp cải thiện quy trình làm việc</li>
</ul>`;
        document.getElementById('job_description').value = description;
        showToast("Đã tạo gợi ý mô tả công việc!", "success");
    }

    function generateAIRequirements() {
        var requirements = `<p><strong>Yêu cầu ứng viên:</strong></p>
<ul>
    <li>Tốt nghiệp Cao đẳng / Đại học chuyên ngành phù hợp</li>
    <li>Có ít nhất 1-2 năm kinh nghiệm trong lĩnh vực tương tự</li>
    <li>Thành thạo các công cụ văn phòng (Word, Excel, PowerPoint)</li>
    <li>Kỹ năng giao tiếp, làm việc nhóm tốt</li>
    <li>Chủ động, sáng tạo và có tinh thần trách nhiệm cao</li>
</ul>`;
        document.getElementById('job_requirements').value = requirements;
        showToast("Đã tạo gợi ý yêu cầu ứng viên!", "success");
    }

    function generateAIBenefits() {
        var benefits = `<p><strong>Quyền lợi được hưởng:</strong></p>
<ul>
    <li>Lương cạnh tranh + thưởng hiệu quả công việc</li>
    <li>Đầy đủ BHXH, BHYT, BHTN theo quy định</li>
    <li>Môi trường làm việc năng động, thân thiện</li>
    <li>Cơ hội thăng tiến và đào tạo chuyên sâu</li>
    <li>Các hoạt động team building, du lịch hàng năm</li>
</ul>`;
        document.getElementById('job_benefits').value = benefits;
        showToast("Đã tạo gợi ý quyền lợi!", "success");
    }

    function generateAIImage() {
        showToast("Tính năng tạo ảnh bằng AI đang phát triển!", "info");
    }

    function showToast(message, type) {
        var toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(function () {
            toast.remove();
        }, 3000);
    }
</script>

</body>
</html>