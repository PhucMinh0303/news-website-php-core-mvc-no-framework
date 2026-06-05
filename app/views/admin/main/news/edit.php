<?php
/**
 * edit.php - View chỉnh sửa bài viết trong admin panel
 */

$formData = $article ?? [];
$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>

<main class="main news-edit-page">

    <?php if ($success): ?>
        <div class="alert alert-success">
            <strong>Thành công!</strong> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <?php foreach ($errors as $error): ?>
                <div>• <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form id="articleForm" action="/admin/main/news/store" method="POST" enctype="multipart/form-data">
        <div class="add-header">
            TRANG ADMIN - CHỈNH SỬA BÀI VIẾT
        </div>

        <div class="add-container">

            <div class="form-group">
                <div class="label-row">
                    <label>Tiêu đề bài viết: <span class="required">*</span></label>
                    <span class="ai-btn" type="button">✨ Gợi ý bằng AI</span>
                </div>
                <input class="input-title" type="text"
                    name="title"
                    id="newsTitle"
                    value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Slug (URL):</label>
                <input class="input-slug" type="text"
                    name="slug"
                    id="newsSlug"
                    value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>">
                <small>(Slug sẽ được tạo tự động từ tiêu đề)</small>
            </div>

            <div class="form-group">
                <label>Chuyên mục: <span class="required">*</span></label>
                <select class="input-category" name="category_id" required>
                    <option value="">-- Chọn chuyên mục --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"
                            <?php echo (($formData['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tác giả:</label>
                <select class="input-author-id" name="author_id">
                    <option value="">-- Chọn tác giả --</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['id']; ?>"
                            <?php echo (($formData['author_id'] ?? '') == $author['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tên tác giả hiển thị: <span class="required">*</span></label>
                <input class="input-author" type="text"
                    name="author_name"
                    id="authorName"
                    value="<?php echo htmlspecialchars($formData['author_name'] ?? ''); ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Ngày xuất bản:</label>
                <input class="input-publish-date" type="datetime-local"
                    name="published_at"
                    value="<?php echo !empty($formData['published_at']) ? date('Y-m-d\TH:i', strtotime($formData['published_at'])) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Meta Title:</label>
                <input class="input-meta-title" type="text"
                    name="meta_title"
                    value="<?php echo htmlspecialchars($formData['meta_title'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Meta Description:</label>
                <textarea class="input-meta-description"
                    name="meta_description"
                    rows="3"><?php echo htmlspecialchars($formData['meta_description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Meta Keywords:</label>
                <input class="input-meta-keywords" type="text"
                    name="meta_keywords"
                    value="<?php echo htmlspecialchars($formData['meta_keywords'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Mô tả ngắn:</label>
                <textarea class="input-description"
                    name="description"
                    rows="4"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Nội dung bài viết:</label>
                <div class="editor-box">
                    <div class="editor-toolbar">
                        <select id="fontFamily">
                            <option value="serif">Font: Serif</option>
                            <option value="arial">Arial</option>
                            <option value="times-new-roman">Times New Roman</option>
                            <option value="courier-new">Courier New</option>
                            <option value="roboto">Roboto</option>
                            <option value="monospace">Monospace</option>
                        </select>
                        <span class="divider"></span>
                        <button type="button" data-cmd="bold"><b>B</b></button>
                        <button type="button" data-cmd="italic"><i>I</i></button>
                        <button type="button" data-cmd="underline"><u>U</u></button>
                        <span class="divider"></span>
                        <input type="color" id="textColor">
                        <span class="divider"></span>
                        <button type="button" data-heading="1">H1</button>
                        <button type="button" data-heading="2">H2</button>
                        <span class="divider"></span>
                        <button type="button" data-cmd="formatBlock" data-value="blockquote">❝</button>
                        <button type="button" id="addLink"><i class="fas fa-link"></i></button>
                        <span class="divider"></span>
                        <button type="button" id="insertImage"><i class="fas fa-image"></i> Chèn hình ảnh</button>
                        <button type="button" id="insertVideo"><i class="fa-solid fa-video"></i> Chèn video</button>
                    </div>
                    <div id="editor" class="editor-area" contenteditable="true"><?php echo htmlspecialchars_decode($formData['content'] ?? ''); ?></div>
                    <input type="hidden" name="content" id="contentInput">
                </div>
                <input type="file" id="imageUpload" accept="image/*" hidden>
            </div>

            <div class="bottom-layout">
                <div class="publish-settings">
                    <h3>CẤU HÌNH XUẤT BẢN</h3>

                    <label>TRẠNG THÁI</label>
                    <select class="input-status" name="status">
                        <option value="draft" <?php echo (($formData['status'] ?? '') == 'draft') ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                        <option value="published" <?php echo (($formData['status'] ?? '') == 'published') ? 'selected' : ''; ?>>Đã đăng (Published)</option>
                        <option value="scheduled" <?php echo (($formData['status'] ?? '') == 'scheduled') ? 'selected' : ''; ?>>Hẹn giờ (Scheduled)</option>
                        <option value="archived" <?php echo (($formData['status'] ?? '') == 'archived') ? 'selected' : ''; ?>>Lưu trữ (Archived)</option>
                    </select>

                    <div id="scheduledDatetime" style="display: <?php echo ($formData['status'] ?? '') == 'scheduled' ? 'block' : 'none'; ?>;">
                        <label>THỜI GIAN HẸN GIỜ</label>
                        <input class="input-scheduled" type="datetime-local" name="scheduled_at"
                            value="<?php echo !empty($formData['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($formData['scheduled_at'])) : ''; ?>">
                    </div>

                    <label>LƯỢT XEM (VIEWS)</label>
                    <input class="input-views" type="number" name="views"
                        value="<?php echo htmlspecialchars($formData['views'] ?? 0); ?>">

                    <label>NỔI BẬT</label>
                    <input type="checkbox" name="is_featured" value="1"
                        <?php echo isset($formData['is_featured']) && $formData['is_featured'] ? 'checked' : ''; ?>>
                    <label class="inline-label">Hiển thị ở mục nổi bật</label>

                    <label>TIN NÓNG</label>
                    <input type="checkbox" name="is_breaking" value="1"
                        <?php echo isset($formData['is_breaking']) && $formData['is_breaking'] ? 'checked' : ''; ?>>
                    <label class="inline-label">Đánh dấu là tin nóng</label>

                    <label>TIN HOT</label>
                    <input type="checkbox" name="is_hot" value="1"
                        <?php echo isset($formData['is_hot']) && $formData['is_hot'] ? 'checked' : ''; ?>>
                    <label class="inline-label">Đánh dấu là tin hot</label>

                    <label>NGUỒN BÀI VIẾT</label>
                    <input class="input-source" type="text" name="source"
                        value="<?php echo htmlspecialchars($formData['source'] ?? ''); ?>">

                    <label>URL NGUỒN</label>
                    <input class="input-source-url" type="url" name="source_url"
                        value="<?php echo htmlspecialchars($formData['source_url'] ?? ''); ?>">
                </div>

                <div class="thumbnail-box">
                    <div class="thumb-header">
                        ẢNH ĐẠI DIỆN BÀI VIẾT
                        <span class="ai-btn" type="button">✨ Tạo bằng AI</span>
                    </div>
                    <div class="upload-box" id="uploadBox">
                        <span>📷</span>
                        <p>Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                        <small>Định dạng: JPG, PNG, WEBP (max 5MB)</small>
                        <input type="file" name="featured_image" id="featuredImage" accept="image/jpeg,image/png,image/webp" hidden>
                    </div>
                    <div id="imagePreview" style="display: <?php echo !empty($formData['featured_image']) ? 'block' : 'none'; ?>; margin-top: 10px;">
                        <img id="previewImg" src="<?php echo htmlspecialchars($formData['featured_image'] ?? ''); ?>" alt="Preview" style="max-width: 100%; border-radius: 8px;">
                        <button type="button" id="removeImage" style="margin-top: 5px;">Xóa ảnh</button>
                    </div>
                    <input type="text" name="featured_image_caption"
                        value="<?php echo htmlspecialchars($formData['featured_image_caption'] ?? ''); ?>"
                        placeholder="Chú thích ảnh" style="margin-top: 10px; width: 100%;">
                </div>
            </div>

            <div class="form-group">
                <label>Ghi chú tác giả:</label>
                <textarea class="input-author-note"
                    name="author_note"
                    rows="3"><?php echo htmlspecialchars($formData['author_note'] ?? ''); ?></textarea>
            </div>

            <div class="action-buttons">
                <button type="submit" name="action" value="draft" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="action" value="publish" class="btn-publish">Cập nhật</button>
            </div>

            <div class="cancel-text"><a href="/admin/main/news">Hủy bỏ và quay lại</a></div>

        </div>
    </form>

</main>

<script>
// Auto generate slug from title
document.getElementById('newsTitle').addEventListener('blur', function() {
    let slugInput = document.getElementById('newsSlug');
    if (!slugInput.value || slugInput.value === '') {
        let title = this.value;
        let slug = title.toLowerCase()
            .replace(/[^\w\s]/g, '')
            .replace(/\s+/g, '-');
        slugInput.value = slug;
    }
});

// Show/hide scheduled datetime
document.querySelector('select[name="status"]').addEventListener('change', function() {
    let scheduledDiv = document.getElementById('scheduledDatetime');
    scheduledDiv.style.display = this.value === 'scheduled' ? 'block' : 'none';
});

// Image preview
document.getElementById('featuredImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('removeImage').addEventListener('click', function() {
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('featuredImage').value = '';
    document.getElementById('previewImg').src = '';
});

// Editor handling
document.getElementById('uploadBox').addEventListener('click', function() {
    document.getElementById('featuredImage').click();
});

// Simple editor functionality
document.querySelectorAll('[data-cmd]').forEach(button => {
    button.addEventListener('click', function() {
        document.execCommand(this.dataset.cmd, false, this.dataset.value);
    });
});

document.querySelectorAll('[data-heading]').forEach(button => {
    button.addEventListener('click', function() {
        document.execCommand('formatBlock', false, 'H' + this.dataset.heading);
    });
});

document.getElementById('fontFamily').addEventListener('change', function() {
    document.execCommand('fontName', false, this.value);
});

document.getElementById('textColor').addEventListener('input', function() {
    document.execCommand('foreColor', false, this.value);
});

document.getElementById('addLink').addEventListener('click', function() {
    const url = prompt('Nhập URL:', 'https://');
    if (url) document.execCommand('createLink', false, url);
});

document.getElementById('insertImage').addEventListener('click', function() {
    const imgInput = document.createElement('input');
    imgInput.type = 'file';
    imgInput.accept = 'image/*';
    imgInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.execCommand('insertImage', false, e.target.result);
            };
            reader.readAsDataURL(file);
        }
    };
    imgInput.click();
});

// Before submit, copy editor content to hidden input
document.getElementById('articleForm').addEventListener('submit', function() {
    document.getElementById('contentInput').value = document.getElementById('editor').innerHTML;
});
</script>