<?php

/**
 * add_recruitment.php - View thêm tin tuyển dụng mới trong admin panel
 * Form gửi dữ liệu đến route /admin/main/recruitment/store
 */

// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>

<main class="main recruitment-create-page">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <?php foreach ($errors as $error): ?>
                <div>• <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="add-header">
        TRANG ADMIN - ĐĂNG TIN TUYỂN DỤNG
    </div>

    <form method="POST" action="/admin/main/recruitment/store" enctype="multipart/form-data" id="recruitmentForm">
        <div class="add-container">

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

            <div class="form-group">
                <label>Slug (URL):</label>
                <input type="hidden" name="slug_original" id="slug_original">
                <input class="input-slug" type="text" name="slug" id="slug" placeholder="[ Tự động tạo từ tiêu đề ]" readonly>
                <small>(Slug được tạo tự động từ tiêu đề, chỉ gồm chữ cái, số và dấu gạch ngang)</small>
            </div>

            <div class="form-group">
                <label>Địa điểm làm việc: <span class="required">*</span></label>
                <textarea class="input-location" name="work_location" rows="2"
                    placeholder="Địa chỉ cụ thể..."><?php echo htmlspecialchars($formData['work_location'] ?? ''); ?></textarea>
            </div>

            <div class="grid-2cols">
                <div class="form-group">
                    <label>Trình độ yêu cầu: <span class="required">*</span></label>
                    <select name="degree">
                        <option value="Không yêu cầu" <?php echo (($formData['degree'] ?? '') == 'Không yêu cầu') ? 'selected' : ''; ?>>
                            Không yêu cầu bằng cấp
                        </option>
                        <option value="Trung Cấp" <?php echo (($formData['degree'] ?? '') == 'Trung Cấp') ? 'selected' : ''; ?>>
                            Trung Cấp
                        </option>
                        <option value="Cao Đẳng - Đại Học" <?php echo (($formData['degree'] ?? '') == 'Cao Đẳng - Đại Học') ? 'selected' : ''; ?>>
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

            <div class="grid-2cols">
                <div class="form-group">
                    <label>Số lượng cần tuyển: <span class="required">*</span></label>
                    <input type="number" name="quantity" min="1"
                        value="<?php echo (int)($formData['quantity'] ?? 1); ?>" required>
                </div>
                <div class="form-group">
                    <label>Mức lương: <span class="required">*</span></label>
                    <input type="text" name="salary_display" id="salary_display"
                        placeholder="VD: 15.000.000 - 20.000.000 VNĐ hoặc Thỏa thuận"
                        value="<?php echo htmlspecialchars($formData['salary_range'] ?? ''); ?>">
                    <input type="hidden" name="salary" id="salary_value">
                    <small id="salary_error" class="error-text"></small>
                </div>
            </div>

            <div class="form-group">
                <label>Hạn nộp hồ sơ: <span class="required">*</span></label>
                <input class="input-deadline" name="deadline" id="deadline" type="date"
                    value="<?php echo htmlspecialchars($formData['deadline'] ?? ''); ?>"
                    min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <div class="label-row">
                    <label>Mô tả công việc: <span class="required">*</span></label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIDescription()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="description" id="job_description" rows="8"
                    placeholder="Mô tả chi tiết công việc... (hỗ trợ HTML)"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <div class="form-group">
                <div class="label-row">
                    <label>Yêu cầu ứng viên: <span class="required">*</span></label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIRequirements()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="requirements" id="job_requirements" rows="8"
                    placeholder="Các yêu cầu về kỹ năng, kinh nghiệm, bằng cấp..."><?php echo htmlspecialchars($formData['requirements'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <div class="form-group">
                <div class="label-row">
                    <label>Quyền lợi được hưởng: <span class="required">*</span></label>
                    <button type="button" class="ai-btn" onclick="recruitmentForm.generateAIBenefits()">✨ Gợi ý bằng AI</button>
                </div>
                <textarea name="benefits" id="job_benefits" rows="6"
                    placeholder="Bảo hiểm, thưởng, cơ hội thăng tiến..."><?php echo htmlspecialchars($formData['benefits'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

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

            <div class="action-buttons">
                <button type="submit" name="save_draft" value="0" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="publish" value="1" class="btn-publish">Đăng tin</button>
                <button type="submit" name="save_and_continue" value="1" class="btn-secondary">Lưu và tiếp tục</button>
            </div>

            <div class="cancel-text" onclick="window.location.href='/admin/recruitment'">Hủy bỏ và quay lại</div>

        </div>
    </form>
</main>