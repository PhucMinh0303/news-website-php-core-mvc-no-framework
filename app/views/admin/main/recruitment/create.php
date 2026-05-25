<?php

/**
 * add_recruitment.php - View thêm tin tuyển dụng mới trong admin panel
 * Form gửi dữ liệu đến route /admin/recruitment/store
 */

// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>

<main class="main">

    <!-- Hiển thị thông báo lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <?php foreach ($errors as $error): ?>
                <div>• <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="add-header">
        TRANG ADMIN - ĐĂNG TIN TUYỂN DỤNG
    </div>

    <!-- FORM - action gọi đến controller store -->
    <form method="POST" action="/admin/main/recruitment/store" enctype="multipart/form-data" id="recruitmentForm">
        <div class="add-container">

            <!-- TITLE -->
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

            <!-- SLUG (auto-generated from title) -->
            <div class="form-group">
                <label>Slug (URL):</label>
                <input type="hidden" name="slug_original" id="slug_original">
                <input class="input-slug" type="text" name="slug" id="slug" placeholder="[ Tự động tạo từ tiêu đề ]" readonly>
                <small>(Slug được tạo tự động từ tiêu đề, chỉ gồm chữ cái, số và dấu gạch ngang)</small>
            </div>

            <!-- WORK LOCATION -->
            <div class="form-group">
                <label>Địa điểm làm việc: <span class="required">*</span></label>
                <textarea class="input-location" name="work_location" rows="2"
                    placeholder="Địa chỉ cụ thể..."><?php echo htmlspecialchars($formData['work_location'] ?? ''); ?></textarea>
            </div>

            <!-- DEGREE & JOB TYPE Row -->
            <div class="grid-2cols">
                <div class="form-group">
                    <label>Trình độ yêu cầu:</label>
                    <select name="degree">
                        <option value="Không yêu cầu" <?php echo (($formData['degree'] ?? '') == 'Không yêu cầu') ? 'selected' : ''; ?>>
                            Không yêu cầu bằng cấp
                        </option>
                        <option value="Trung Cấp" <?php echo (($formData['degree'] ?? '') == 'Trung Cấp') ? 'selected' : ''; ?>>
                            Trung Cấp
                        </option>
                        <option value="Cao Đẳng - Đại Học" <?php echo (($formData['degree'] ?? 'Cao Đẳng - Đại Học') == 'Cao Đẳng - Đại Học') ? 'selected' : ''; ?>>
                            Cao Đẳng - Đại Học
                        </option>
                        <option value="Cao Học" <?php echo (($formData['degree'] ?? '') == 'Cao Học') ? 'selected' : ''; ?>>
                            Cao Học
                        </option>
                        <option value="Tiến Sĩ" <?php echo (($formData['degree'] ?? '') == 'Tiến Sĩ') ? 'selected' : ''; ?>>
                            Tiến Sĩ
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hình thức làm việc:</label>
                    <select name="work_type">
                        <option value="Toàn thời gian" <?php echo (($formData['work_type'] ?? '') == 'Toàn thời gian') ? 'selected' : ''; ?>>
                            Toàn thời gian
                        </option>
                        <option value="Bán thời gian" <?php echo (($formData['work_type'] ?? '') == 'Bán thời gian') ? 'selected' : ''; ?>>
                            Bán thời gian
                        </option>
                        <option value="Hợp đồng" <?php echo (($formData['work_type'] ?? '') == 'Hợp đồng') ? 'selected' : ''; ?>>
                            Hợp đồng
                        </option>
                    </select>
                </div>
            </div>

            <!-- QUANTITY & SALARY RANGE Row -->
            <div class="grid-2cols">
                <div class="form-group">
                    <label>Số lượng cần tuyển: <span class="required">*</span></label>
                    <input type="number" name="quantity" min="1"
                        value="<?php echo (int)($formData['quantity'] ?? 1); ?>" required>
                </div>
                <div class="form-group">
                    <label>Mức lương:</label>
                    <input type="text" name="salary_display" id="salary_display"
                        placeholder="VD: 15.000.000 - 20.000.000 VNĐ hoặc Thỏa thuận"
                        value="<?php echo htmlspecialchars($formData['salary_range'] ?? ''); ?>">
                    <input type="hidden" name="salary" id="salary_value">
                    <small id="salary_error" class="error-text"></small>
                </div>
            </div>

            <!-- DEADLINE -->
            <div class="form-group">
                <label>Hạn nộp hồ sơ: <span class="required">*</span></label>
                <input class="input-deadline" type="date" name="deadline"
                    value="<?php echo htmlspecialchars($formData['deadline'] ?? ''); ?>"
                    min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <div class="label-row">
                    <label>Mô tả công việc: <span class="required">*</span></label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIDescription()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="description" id="job_description" rows="8"
                    placeholder="Mô tả chi tiết công việc... (hỗ trợ HTML)"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- REQUIREMENTS -->
            <div class="form-group">
                <div class="label-row">
                    <label>Yêu cầu ứng viên: <span class="required">*</span></label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIRequirements()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="requirements" id="job_requirements" rows="8"
                    placeholder="Các yêu cầu về kỹ năng, kinh nghiệm, bằng cấp..."><?php echo htmlspecialchars($formData['requirements'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BENEFITS -->
            <div class="form-group">
                <div class="label-row">
                    <label>Quyền lợi được hưởng:</label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIBenefits()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="benefits" id="job_benefits" rows="6"
                    placeholder="Bảo hiểm, thưởng, cơ hội thăng tiến..."><?php echo htmlspecialchars($formData['benefits'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BOTTOM LAYOUT -->
            <div class="bottom-layout">
                <div class="publish-settings">
                    <h3>CẤU HÌNH ĐĂNG TIN</h3>

                    <label>TRẠNG THÁI</label>
                    <select name="status">
                        <option value="0" <?php echo (($formData['status'] ?? '0') == '0') ? 'selected' : ''; ?>>
                            Bản nháp (Draft)
                        </option>
                        <option value="1" <?php echo (($formData['status'] ?? '') == '1') ? 'selected' : ''; ?>>
                            Đang đăng (Open)
                        </option>
                        <option value="2" <?php echo (($formData['status'] ?? '') == '2') ? 'selected' : ''; ?>>
                            Đã đóng (Closed)
                        </option>
                    </select>

                    <small>• Draft (0): Lưu tạm, chưa hiển thị<br>
                        • Open (1): Đang tuyển dụng<br>
                        • Closed (2): Ngưng nhận hồ sơ</small>
                </div>

                <div class="thumbnail-box">
                    <div class="thumb-header">
                        ẢNH ĐẠI DIỆN TIN TUYỂN DỤNG
                        <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIImage()">✨ Tạo bằng AI</button>
                    </div>

                    <div class="upload-box" id="uploadBox">
                        <span id="uploadIcon">📷</span>
                        <p id="uploadText">Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                        <small id="uploadInfo">Mặc định: default-job.webp (Max 2MB)</small>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp"
                        style="display: none;">
                    <div id="imagePreview" style="margin-top: 10px; display: none;">
                        <img id="previewImg" src="#" alt="Preview"
                            style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-buttons">
                <button type="submit" name="save_draft" value="0" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="publish" value="1" class="btn-publish">Đăng tin</button>
                <button type="submit" name="save_and_continue" value="1" class="btn-secondary">Lưu và tiếp tục</button>
            </div>

            <div class="cancel-text" onclick="window.location.href='/admin/recruitment'">Hủy bỏ và quay lại</div>

        </div>
    </form>
</main>

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

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-title,
    .input-slug,
    .input-location {
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

    .publish-settings h3,
    .thumbnail-box .thumb-header {
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

    .btn-draft,
    .btn-publish,
    .btn-secondary {
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
        margin-bottom: 20px;
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

    .required {
        color: #ef4444;
    }
</style>

<script>
    // assets/js/recruitment-form.js - jQuery 3.7.1 version

    (function($) {
        'use strict';

        $(document).ready(function() {

            // Hàm chuyển đổi chuỗi có dấu thành không dấu
            function removeVietnameseTones(str) {
                if (!str) return '';

                const accentsMap = {
                    'a': /[\u00E0\u00E1\u1EA1\u1EA3\u00E3\u00E2\u1EA7\u1EA5\u1EAD\u1EA9\u1EAB\u0103\u1EB1\u1EAF\u1EB7\u1EB3\u1EB5]/g,
                    'e': /[\u00E8\u00E9\u1EB9\u1EBB\u1EBD\u00EA\u1EC1\u1EBF\u1EC7\u1EC3\u1EC5]/g,
                    'i': /[\u00EC\u00ED\u1ECB\u1EC9\u0129]/g,
                    'o': /[\u00F2\u00F3\u1ECD\u1ECF\u00F5\u00F4\u1ED3\u1ED1\u1ED9\u1ED5\u1ED7\u01A1\u1EDD\u1EDB\u1EE3\u1EE7\u1EE5]/g,
                    'u': /[\u00F9\u00FA\u1EE5\u1EE7\u0169\u01B0\u1EEB\u1EE9\u1EF1\u1EED\u1EEF]/g,
                    'y': /[\u00FD\u1EF3\u1EF5\u1EF1]/g,
                    'd': /[\u0111]/g,
                    'A': /[\u00C0\u00C1\u1EA0\u1EA2\u00C3\u00C2\u1EA6\u1EA4\u1EAC\u1EA8\u1EAA\u0102\u1EB0\u1EAE\u1EB6\u1EB2\u1EB4]/g,
                    'E': /[\u00C8\u00C9\u1EB8\u1EBA\u1EBC\u00CA\u1EC0\u1EBE\u1EC6\u1EC2\u1EC4]/g,
                    'I': /[\u00CC\u00CD\u1ECA\u1EC8\u0128]/g,
                    'O': /[\u00D2\u00D3\u1ECC\u1ECE\u00D5\u00D4\u1ED2\u1ED0\u1ED8\u1ED4\u1ED6\u01A0\u1EDC\u1EDA\u1EE2\u1EE6\u1EE4]/g,
                    'U': /[\u00D9\u00DA\u1EE4\u1EE6\u0168\u01AF\u1EEA\u1EE8\u1EF0\u1EEC\u1EEE]/g,
                    'Y': /[\u00DD\u1EF2\u1EF4\u1EF0]/g,
                    'D': /[\u0110]/g
                };

                let result = str;
                for (let char in accentsMap) {
                    result = result.replace(accentsMap[char], char);
                }

                return result;
            }

            // Hàm tạo slug từ title
            function generateSlugFromTitle(title) {
                if (!title || title.trim() === '') return '';

                // Xóa dấu tiếng Việt
                let slug = removeVietnameseTones(title);

                // Chuyển sang chữ thường
                slug = slug.toLowerCase();

                // Thay thế ký tự đặc biệt (chỉ giữ chữ cái, số, dấu cách)
                slug = slug.replace(/[^\w\s]/g, '');

                // Thay khoảng trắng bằng dấu gạch ngang
                slug = slug.replace(/\s+/g, '-');

                // Xóa dấu gạch ngang liên tiếp
                slug = slug.replace(/-+/g, '-');

                // Xóa dấu gạch ngang ở đầu và cuối
                slug = slug.replace(/^-+|-+$/g, '');

                // Giới hạn độ dài 100 ký tự
                slug = slug.substring(0, 100);

                return slug;
            }

            // Cập nhật slug (tự động)
            function updateSlug() {
                var $title = $('#recruitment_title');
                var $slug = $('#slug');
                var $slugOriginal = $('#slug_original');

                if (!$title.length || !$slug.length) return;

                var title = $title.val();
                var newSlug = generateSlugFromTitle(title);
                var oldSlug = $slug.val();

                // Chỉ cập nhật nếu slug thay đổi
                if (newSlug !== oldSlug) {
                    $slug.val(newSlug);
                    if ($slugOriginal.length) {
                        $slugOriginal.val(newSlug);
                    }

                    // Hiệu ứng highlight khi slug thay đổi
                    $slug.css({
                        'backgroundColor': '#fef3c7',
                        'transition': 'all 0.3s ease'
                    });

                    setTimeout(function() {
                        $slug.css('backgroundColor', '#f3f4f6');
                    }, 500);
                }
            }

            // Tạo lại slug (force update)
            window.regenerateSlug = function() {
                var $title = $('#recruitment_title');
                var $slug = $('#slug');
                var $slugOriginal = $('#slug_original');

                if (!$title.length) return;

                var title = $title.val();

                if (!title || title.trim() === '') {
                    showToast("Vui lòng nhập tiêu đề trước khi tạo slug!", "error");
                    $title.trigger('focus');
                    return;
                }

                var newSlug = generateSlugFromTitle(title);
                $slug.val(newSlug);
                if ($slugOriginal.length) {
                    $slugOriginal.val(newSlug);
                }

                // Hiệu ứng flash
                $slug.css({
                    'backgroundColor': '#d1fae5',
                    'borderColor': '#10b981'
                });

                setTimeout(function() {
                    $slug.css({
                        'backgroundColor': '#f3f4f6',
                        'borderColor': '#e5e7eb'
                    });
                }, 500);

                showToast("Đã tạo lại slug thành công!", "success");
            }

            // Kiểm tra slug có hợp lệ không
            function isValidSlug(slug) {
                if (!slug || slug.trim() === '') return false;
                // Slug chỉ được chứa chữ thường, số, dấu gạch ngang
                var slugRegex = /^[a-z0-9]+(-[a-z0-9]+)*$/;
                return slugRegex.test(slug);
            }

            // Preview ảnh trước khi upload
            window.previewImage = function(input) {
                var $preview = $('#imagePreview');
                var $previewImg = $('#previewImg');
                var $uploadBox = $('#uploadBox');
                var $uploadText = $('#uploadText');
                var $uploadInfo = $('#uploadInfo');

                if (input.files && input.files[0]) {
                    // Kiểm tra kích thước file (2MB)
                    if (input.files[0].size > 2 * 1024 * 1024) {
                        showToast("Ảnh không được vượt quá 2MB!", "error");
                        $(input).val('');
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $previewImg.attr('src', e.target.result);
                        $preview.show();
                        $uploadBox.css('opacity', '0.5');
                        $uploadText.html('Đã chọn ảnh: ' + input.files[0].name);
                        $uploadInfo.html('Click để đổi ảnh khác');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            };

            // AI suggest functions
            window.generateAITitle = function() {
                var titles = [
                    "Tuyển dụng Chuyên viên IT - Lương hấp dẫn - Môi trường chuyên nghiệp",
                    "Cần tuyển gấp Trưởng phòng Kinh doanh - Thưởng Tết hấp dẫn",
                    "Công ty TNHH ABC tuyển dụng Kế toán trưởng - Lương cạnh tranh",
                    "Urgent! Tuyển Nhân viên Marketing - Làm việc tại Quận 1 - Lương 15-20tr",
                    "Tuyển dụng Lập trình viên Fullstack - Làm việc remote",
                    "Cần tìm Nhân viên Chăm sóc khách hàng - Tiếng Anh tốt"
                ];
                var randomTitle = titles[Math.floor(Math.random() * titles.length)];
                $('#recruitment_title').val(randomTitle);
                updateSlug();
                showToast("Đã tạo gợi ý tiêu đề!", "success");
            };

            window.generateAIDescription = function() {
                var description = '<p><strong>Mô tả công việc:</strong></p>\n<ul>\n    <li>Thực hiện các công việc chuyên môn theo đúng quy trình của công ty</li>\n    <li>Phối hợp với các phòng ban để đảm bảo tiến độ công việc</li>\n    <li>Báo cáo kết quả công việc định kỳ cho cấp trên trực tiếp</li>\n    <li>Tham gia các dự án theo sự phân công của quản lý</li>\n    <li>Đề xuất các giải pháp cải thiện quy trình làm việc</li>\n</ul>';
                $('#job_description').val(description);
                showToast("Đã tạo gợi ý mô tả công việc!", "success");
            };

            window.generateAIRequirements = function() {
                var requirements = '<p><strong>Yêu cầu ứng viên:</strong></p>\n<ul>\n    <li>Tốt nghiệp Cao đẳng / Đại học chuyên ngành phù hợp</li>\n    <li>Có ít nhất 1-2 năm kinh nghiệm trong lĩnh vực tương tự</li>\n    <li>Thành thạo các công cụ văn phòng (Word, Excel, PowerPoint)</li>\n    <li>Kỹ năng giao tiếp, làm việc nhóm tốt</li>\n    <li>Chủ động, sáng tạo và có tinh thần trách nhiệm cao</li>\n</ul>';
                $('#job_requirements').val(requirements);
                showToast("Đã tạo gợi ý yêu cầu ứng viên!", "success");
            };

            window.generateAIBenefits = function() {
                var benefits = '<p><strong>Quyền lợi được hưởng:</strong></p>\n<ul>\n    <li>Lương cạnh tranh + thưởng hiệu quả công việc</li>\n    <li>Đầy đủ BHXH, BHYT, BHTN theo quy định</li>\n    <li>Môi trường làm việc năng động, thân thiện</li>\n    <li>Cơ hội thăng tiến và đào tạo chuyên sâu</li>\n    <li>Các hoạt động team building, du lịch hàng năm</li>\n</ul>';
                $('#job_benefits').val(benefits);
                showToast("Đã tạo gợi ý quyền lợi!", "success");
            };

            window.generateAIImage = function() {
                showToast("Tính năng tạo ảnh bằng AI đang phát triển!", "info");
            };

            // Hiển thị toast message
            function showToast(message, type) {
                // Xóa toast cũ nếu có
                $('.toast').remove();

                var toast = $('<div>')
                    .addClass('toast toast-' + type)
                    .html(message)
                    .hide();

                $('body').append(toast);
                toast.fadeIn(300);

                setTimeout(function() {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            // Xử lý lương
            function initSalaryHandler() {
                var $salaryDisplay = $('#salary_display');
                var $salaryValue = $('#salary_value');
                var $salaryError = $('#salary_error');

                if (!$salaryDisplay.length) return;

                $salaryDisplay.on('input', function() {
                    var originalValue = $(this).val();
                    var $this = $(this);

                    // Kiểm tra có chữ không
                    if (/[a-zA-Z]/.test(originalValue)) {
                        $salaryError.text('Chỉ nhập số, không nhập chữ');
                    } else {
                        $salaryError.text('');
                    }

                    // Chỉ giữ lại số
                    var numbers = originalValue.replace(/\D/g, '');

                    // Nếu rỗng
                    if (numbers === '') {
                        $this.val('');
                        $salaryValue.val('');
                        return;
                    }

                    // Thêm 6 số 0 (triệu)
                    var salary = parseInt(numbers, 10) * 1000000;

                    // Hiển thị format VN
                    $this.val(salary.toLocaleString('vi-VN'));

                    // Giá trị lưu DB
                    $salaryValue.val(salary);
                });
            }

            // Validate form trước khi submit
            function validateForm() {
                var $title = $('#recruitment_title');
                var $slug = $('#slug');
                var $workLocation = $('textarea[name="work_location"]');
                var $quantity = $('input[name="quantity"]');
                var $deadline = $('input[name="deadline"]');

                var title = $title.val().trim();
                var slug = $slug.val().trim();
                var workLocation = $workLocation.val().trim();
                var quantity = $quantity.val();
                var deadline = $deadline.val();

                var errors = [];

                if (!title) {
                    errors.push("Vui lòng nhập tiêu đề tin tuyển dụng");
                    $title.trigger('focus');
                }

                if (!slug) {
                    errors.push("Slug không được để trống");
                } else if (!isValidSlug(slug)) {
                    errors.push("Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)");
                }

                if (!workLocation) {
                    errors.push("Vui lòng nhập địa điểm làm việc");
                }

                if (!quantity || parseInt(quantity, 10) < 1) {
                    errors.push("Số lượng cần tuyển phải lớn hơn 0");
                }

                if (!deadline) {
                    errors.push("Vui lòng chọn hạn nộp hồ sơ");
                }

                if (errors.length > 0) {
                    var errorMessage = errors.join('\n• ');
                    showToast("• " + errorMessage, "error");
                    return false;
                }

                return true;
            }

            // Event listeners với jQuery 3.7.1
            // Khi nhập title -> tự động tạo slug
            $('#recruitment_title').on('input', function() {
                updateSlug();
            });

            // Ngăn chặn copy-paste vào slug
            $('#slug').on('copy cut paste', function(e) {
                e.preventDefault();
                showToast("Slug không thể chỉnh sửa trực tiếp!", "error");
                return false;
            });

            // Ngăn chặn kéo thả vào slug
            $('#slug').on('drag drop', function(e) {
                e.preventDefault();
                return false;
            });

            // Ngăn chặn nhập từ bàn phím vào slug
            $('#slug').on('keydown', function(e) {
                e.preventDefault();
                showToast("Slug được tạo tự động từ tiêu đề!", "info");
                return false;
            });

            // Ngăn chặn click chuột phải trên slug
            $('#slug').on('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Validate trước khi submit
            $('#recruitmentForm').on('submit', function(e) {
                // Đảm bảo slug được cập nhật mới nhất trước khi submit
                updateSlug();

                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            // Upload box click
            $('#uploadBox').on('click', function() {
                $('#imageInput').trigger('click');
            });

            // Image input change
            $('#imageInput').on('change', function() {
                if (this.files && this.files[0]) {
                    previewImage(this);
                }
            });

            // Khởi tạo slug ban đầu nếu có title nhưng chưa có slug
            var initialTitle = $('#recruitment_title').val();
            var initialSlug = $('#slug').val();

            if (initialTitle && initialTitle.trim() !== '' && (!initialSlug || initialSlug.trim() === '')) {
                updateSlug();
            }

            // Khởi tạo xử lý lương
            initSalaryHandler();

            // Thêm tooltip cho slug
            $('#slug').attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');

            // Log để kiểm tra jQuery version
            console.log('jQuery version: ' + $.fn.jquery);
            console.log('Recruitment form initialized successfully!');

        });

    })(jQuery);
</script>