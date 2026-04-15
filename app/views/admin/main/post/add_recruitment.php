<?php
/**
 * add recruitment management view for admin panel
 * Xử lý thêm tin tuyển dụng mới
 */

require_once __DIR__ . '/../model/Re.php';

$database = new Database();
$conn = $database->getConnection();

$error = '';
$success = '';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $recruitment_title = trim($_POST['recruitment_title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $education = trim($_POST['education'] ?? 'Cao Đẳng - Đại Học');
    $quantity = (int)($_POST['quantity'] ?? 1);
    $salary_range = trim($_POST['salary_range'] ?? '');
    $deadline = $_POST['deadline'] ?? '';
    $job_description = $_POST['job_description'] ?? '';
    $job_requirements = $_POST['job_requirements'] ?? '';
    $job_benefits = $_POST['job_benefits'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    $position = trim($_POST['position'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $job_type = $_POST['job_type'] ?? 'fulltime';

    // Xử lý upload ảnh
    $image = 'default-job.webp';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '_' . date('YmdHis') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            }
        }
    }

    // Validate dữ liệu
    $errors = [];
    if (empty($recruitment_title)) {
        $errors[] = "Vui lòng nhập tiêu đề tin tuyển dụng";
    }
    if (empty($slug)) {
        $slug = createSlug($recruitment_title);
    }
    if (empty($deadline)) {
        $errors[] = "Vui lòng chọn hạn nộp hồ sơ";
    }
    if (empty($job_description)) {
        $errors[] = "Vui lòng nhập mô tả công việc";
    }
    if (empty($job_requirements)) {
        $errors[] = "Vui lòng nhập yêu cầu ứng viên";
    }

    // Kiểm tra slug đã tồn tại chưa
    $check_slug = $database->fetchOne("SELECT id FROM recruitments WHERE slug = :slug", [':slug' => $slug]);
    if ($check_slug) {
        $slug = $slug . '-' . uniqid();
    }

    if (empty($errors)) {
        try {
            $data = [
                    ':recruitment_title' => $recruitment_title,
                    ':slug' => $slug,
                    ':job_description' => $job_description,
                    ':job_requirements' => $job_requirements,
                    ':job_benefits' => $job_benefits,
                    ':image' => $image,
                    ':salary_range' => $salary_range,
                    ':location' => $location,
                    ':deadline' => $deadline,
                    ':quantity' => $quantity,
                    ':position' => $position,
                    ':experience' => $experience,
                    ':education' => $education,
                    ':job_type' => $job_type,
                    ':status' => $status,
                    ':views' => 0
            ];

            $sql = "INSERT INTO recruitments (recruitment_title, slug, job_description, job_requirements, 
                    job_benefits, image, salary_range, location, deadline, quantity, position, 
                    experience, education, job_type, status, views, created_at, updated_at) 
                    VALUES (:recruitment_title, :slug, :job_description, :job_requirements, 
                    :job_benefits, :image, :salary_range, :location, :deadline, :quantity, :position, 
                    :experience, :education, :job_type, :status, :views, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->execute($data);

            $new_id = $conn->lastInsertId();
            $success = "Đăng tin tuyển dụng thành công! ID: " . $new_id;

            // Reset form sau khi thành công
            if (isset($_POST['save_and_continue'])) {
                // Không reset, tiếp tục chỉnh sửa
            } else {
                // Chuyển hướng về danh sách sau 2 giây
                echo "<script>
                    setTimeout(function() {
                        window.location.href = '?page=recruitment';
                    }, 2000);
                </script>";
            }

        } catch (PDOException $e) {
            $error = "Lỗi khi lưu dữ liệu: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Hàm tạo slug từ string
function createSlug($string)
{
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    $string = preg_replace('/\s+/', '-', $string);
    $string = strtolower(trim($string));
    return $string;
}

// Hàm lấy text status
function getStatusText($status)
{
    $statuses = [
            'draft' => 'Bản nháp',
            'open' => 'Đang đăng',
            'closed' => 'Đã đóng',
            'filled' => 'Đã tuyển đủ'
    ];
    return $statuses[$status] ?? $status;
}

?>

<main class="main">

    <!-- Hiển thị thông báo -->
    <?php if ($error): ?>
        <div class="alert alert-error"
             style="background: #fee2e2; color: #ef4444; padding: 12px 20px; margin: 20px; border-radius: 8px; border-left: 4px solid #ef4444;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"
             style="background: #dcfce7; color: #10b981; padding: 12px 20px; margin: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="add-header">
        TRANG ADMIN - ĐĂNG TIN TUYỂN DỤNG
    </div>

    <form method="POST" action="" enctype="multipart/form-data" id="recruitmentForm">
        <div class="add-container">

            <!-- TITLE -->
            <div class="form-group">
                <div class="label-row">
                    <label>Tiêu đề tin tuyển dụng: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAITitle()">✨ Gợi ý bằng AI</span>
                </div>
                <input class="input-title" type="text" name="recruitment_title" id="recruitment_title"
                       placeholder="[ Nhập tiêu đề tin tuyển dụng tại đây... ]"
                       value="<?php echo htmlspecialchars($_POST['recruitment_title'] ?? ''); ?>"
                       onkeyup="generateSlug()" required>
            </div>

            <!-- SLUG (auto-generated from title) -->
            <div class="form-group">
                <label>Slug (URL):</label>
                <input class="input-slug" type="text" name="slug" id="slug"
                       placeholder="[ Tự động tạo từ tiêu đề ]"
                       value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                <small>(Slug sẽ được tạo tự động từ tiêu đề)</small>
            </div>

            <!-- WORK LOCATION -->
            <div class="form-group">
                <label>Địa điểm làm việc:</label>
                <textarea class="input-location" name="location"
                          placeholder="[ Địa chỉ cụ thể... ]"><?php echo htmlspecialchars($_POST['location'] ?? ''); ?></textarea>
            </div>

            <!-- POSITION & EXPERIENCE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- POSITION -->
                <div class="form-group">
                    <label>Vị trí tuyển dụng:</label>
                    <input class="input-position" type="text" name="position"
                           placeholder="[ VD: Trưởng phòng, Chuyên viên, Nhân viên... ]"
                           value="<?php echo htmlspecialchars($_POST['position'] ?? ''); ?>">
                </div>

                <!-- EXPERIENCE -->
                <div class="form-group">
                    <label>Kinh nghiệm yêu cầu:</label>
                    <input class="input-experience" type="text" name="experience"
                           placeholder="[ VD: 2 năm, Không yêu cầu kinh nghiệm... ]"
                           value="<?php echo htmlspecialchars($_POST['experience'] ?? ''); ?>">
                </div>
            </div>

            <!-- DEGREE & JOB TYPE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- DEGREE -->
                <div class="form-group">
                    <label>Trình độ yêu cầu:</label>
                    <select class="input-degree" name="education">
                        <option value="Không yêu cầu" <?php echo (($_POST['education'] ?? '') == 'Không yêu cầu') ? 'selected' : ''; ?>>
                            Không yêu cầu bằng cấp
                        </option>
                        <option value="Trung Cấp" <?php echo (($_POST['education'] ?? '') == 'Trung Cấp') ? 'selected' : ''; ?>>
                            Trung Cấp
                        </option>
                        <option value="Cao Đẳng - Đại Học" <?php echo (($_POST['education'] ?? 'Cao Đẳng - Đại Học') == 'Cao Đẳng - Đại Học') ? 'selected' : ''; ?>>
                            Cao Đẳng - Đại Học
                        </option>
                        <option value="Cao Học" <?php echo (($_POST['education'] ?? '') == 'Cao Học') ? 'selected' : ''; ?>>
                            Cao Học
                        </option>
                        <option value="Tiến Sĩ" <?php echo (($_POST['education'] ?? '') == 'Tiến Sĩ') ? 'selected' : ''; ?>>
                            Tiến Sĩ
                        </option>
                    </select>
                </div>

                <!-- JOB TYPE -->
                <div class="form-group">
                    <label>Hình thức làm việc:</label>
                    <select class="input-jobtype" name="job_type">
                        <option value="fulltime" <?php echo (($_POST['job_type'] ?? 'fulltime') == 'fulltime') ? 'selected' : ''; ?>>
                            Toàn thời gian
                        </option>
                        <option value="parttime" <?php echo (($_POST['job_type'] ?? '') == 'parttime') ? 'selected' : ''; ?>>
                            Bán thời gian
                        </option>
                        <option value="contract" <?php echo (($_POST['job_type'] ?? '') == 'contract') ? 'selected' : ''; ?>>
                            Hợp đồng
                        </option>
                        <option value="internship" <?php echo (($_POST['job_type'] ?? '') == 'internship') ? 'selected' : ''; ?>>
                            Thực tập
                        </option>
                    </select>
                </div>
            </div>

            <!-- QUANTITY & SALARY RANGE Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- QUANTITY -->
                <div class="form-group">
                    <label>Số lượng cần tuyển:</label>
                    <input class="input-quantity" type="number" name="quantity" min="1"
                           value="<?php echo (int)($_POST['quantity'] ?? 1); ?>">
                </div>

                <!-- SALARY RANGE -->
                <div class="form-group">
                    <label>Mức lương:</label>
                    <input class="input-salary" type="text" name="salary_range"
                           placeholder="[ VD: 15.000.000 - 20.000.000 VNĐ hoặc Thỏa thuận ]"
                           value="<?php echo htmlspecialchars($_POST['salary_range'] ?? ''); ?>">
                </div>
            </div>

            <!-- DEADLINE -->
            <div class="form-group">
                <label>Hạn nộp hồ sơ: <span style="color: red;">*</span></label>
                <input class="input-deadline" type="date" name="deadline"
                       value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>" required>
            </div>

            <!-- DESCRIPTION (Mô tả công việc) -->
            <div class="form-group">
                <div class="label-row">
                    <label>Mô tả công việc: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAIDescription()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea class="input-description" name="job_description" id="job_description"
                          placeholder="[ Mô tả chi tiết công việc... ]" rows="6"
                          required><?php echo htmlspecialchars($_POST['job_description'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- REQUIREMENTS (Yêu cầu ứng viên) -->
            <div class="form-group">
                <div class="label-row">
                    <label>Yêu cầu ứng viên: <span style="color: red;">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAIRequirements()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea class="input-requirements" name="job_requirements" id="job_requirements"
                          placeholder="[ Các yêu cầu về kỹ năng, kinh nghiệm, bằng cấp... ]" rows="6"
                          required><?php echo htmlspecialchars($_POST['job_requirements'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BENEFITS (Quyền lợi) -->
            <div class="form-group">
                <div class="label-row">
                    <label>Quyền lợi được hưởng:</label>
                    <span class="ai-btn" type="button" onclick="generateAIBenefits()">✨ Gợi ý bằng AI</span>
                </div>
                <textarea class="input-benefits" name="job_benefits" id="job_benefits"
                          placeholder="[ Bảo hiểm, thưởng, cơ hội thăng tiến... ]"
                          rows="5"><?php echo htmlspecialchars($_POST['job_benefits'] ?? ''); ?></textarea>
                <small>Hỗ trợ HTML tags: &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</small>
            </div>

            <!-- BOTTOM LAYOUT -->
            <div class="bottom-layout">

                <!-- LEFT -->
                <div class="publish-settings">
                    <h3>CẤU HÌNH ĐĂNG TIN</h3>

                    <label>TRẠNG THÁI</label>
                    <select class="input-status" name="status">
                        <option value="draft" <?php echo (($_POST['status'] ?? 'draft') == 'draft') ? 'selected' : ''; ?>>
                            Bản nháp (Draft)
                        </option>
                        <option value="open" <?php echo (($_POST['status'] ?? '') == 'open') ? 'selected' : ''; ?>>Đang
                            đăng (Open)
                        </option>
                        <option value="closed" <?php echo (($_POST['status'] ?? '') == 'closed') ? 'selected' : ''; ?>>
                            Đã đóng (Closed)
                        </option>
                        <option value="filled" <?php echo (($_POST['status'] ?? '') == 'filled') ? 'selected' : ''; ?>>
                            Đã tuyển đủ (Filled)
                        </option>
                    </select>

                    <small>• Draft: Lưu tạm, chưa hiển thị<br>
                        • Open: Đang tuyển dụng<br>
                        • Closed: Ngưng nhận hồ sơ<br>
                        • Filled: Đã tuyển đủ chỉ tiêu</small>
                </div>

                <!-- RIGHT -->
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

            <!-- ACTION BUTTON -->
            <div class="action-buttons">
                <button type="submit" name="save_draft" value="1" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="publish" value="1" class="btn-publish">Đăng tin</button>
                <button type="submit" name="save_and_continue" value="1" class="btn-secondary">Lưu và tiếp tục</button>
            </div>

            <div class="cancel-text" onclick="window.location.href='?page=recruitment'">Hủy bỏ và quay lại</div>

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
        var uploadIcon = document.getElementById('uploadIcon');
        var uploadText = document.getElementById('uploadText');
        var uploadInfo = document.getElementById('uploadInfo');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                uploadBox.style.opacity = '0.5';
                uploadIcon.style.opacity = '0.5';
                uploadText.innerHTML = 'Đã chọn ảnh: ' + input.files[0].name;
                uploadInfo.innerHTML = 'Click để đổi ảnh khác';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AI suggest functions (simulate AI response)
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
        toast.className = 'toast toast-' + type;
        toast.innerHTML = message;
        toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#333; color:white; padding:12px 20px; border-radius:8px; z-index:9999; animation: slideIn 0.3s ease;';
        document.body.appendChild(toast);
        setTimeout(function () {
            toast.remove();
        }, 3000);
    }

    // Click vào upload box
    document.getElementById('uploadBox').addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Style cho upload box
    var style = document.createElement('style');
    style.textContent = `
    .upload-box {
        cursor: pointer;
        transition: opacity 0.3s ease;
    }
    .upload-box:hover {
        opacity: 0.8;
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
    .btn-secondary {
        background: #6b7280;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #4b5563;
    }
`;
    document.head.appendChild(style);
</script>