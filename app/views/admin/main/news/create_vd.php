<?php
/**
 * add articles management view for admin panel
 */
// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
$categories = $categories ?? [];
$authors = $authors ?? [];

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
unset($_SESSION['success']);
?>

<main class="main news-create-page">

    <!-- Hiển thị thông báo -->
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

    <form id="articleForm" action="index.php?controller=news&action=store" method="POST" enctype="multipart/form-data">
        <!-- HEADER -->
        <div class="add-header">
            TRANG ADMIN - ĐĂNG BÀI VIẾT
        </div>

        <div class="add-container">

            <!-- TITLE -->
            <div class="form-group">
                <div class="label-row">
                    <label>Tiêu đề bài viết: <span class="required">*</span></label>
                    <span class="ai-btn" type="button">✨ Gợi ý bằng AI</span>
                </div>
                <input class="input-title" type="text" 
                       name="title" 
                       id="newsTitle"
                       value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>"
                       placeholder="[ Nhập tiêu đề bài viết tại đây... ]"
                       required>
            </div>

            <!-- SLUG (auto-generated from title) -->
            <div class="form-group">
                <label>Slug (URL):</label>
                <input class="input-slug" type="text" 
                       name="slug" 
                       id="newsSlug"
                       value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>"
                       placeholder="[ Tự động tạo từ tiêu đề ]">
                <small>(Slug sẽ được tạo tự động từ tiêu đề)</small>
            </div>

            <!-- CATEGORY -->
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
                <small>(Liên kết với bảng categories)</small>
            </div>

            <!-- AUTHOR -->
            <div class="form-group">
                <label>Tác giả:</label>
                <select class="input-author-id" name="author_id" id="authorId">
                    <option value="">-- Chọn tác giả --</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['id']; ?>"
                            <?php echo (($formData['author_id'] ?? '') == $author['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>(Liên kết với bảng authors)</small>
            </div>

            <!-- AUTHOR NAME -->
            <div class="form-group">
                <label>Tên tác giả hiển thị: <span class="required">*</span></label>
                <input class="input-author" type="text" 
                       name="author_name" 
                       id="authorName"
                       value="<?php echo htmlspecialchars($formData['author_name'] ?? ''); ?>"
                       placeholder="[ Tên tác giả hiển thị trên bài viết ]"
                       required>
                <small>(Tên sẽ được lưu trực tiếp vào cột author)</small>
            </div>

            <!-- PUBLISH DATE -->
            <div class="form-group">
                <label>Ngày xuất bản:</label>
                <input class="input-publish-date" type="datetime-local" 
                       name="published_at"
                       value="<?php echo htmlspecialchars($formData['published_at'] ?? date('Y-m-d\TH:i')); ?>">
            </div>

            <!-- META SEO -->
            <div class="form-group">
                <label>Meta Title:</label>
                <input class="input-meta-title" type="text" 
                       name="meta_title"
                       value="<?php echo htmlspecialchars($formData['meta_title'] ?? ''); ?>"
                       placeholder="SEO Title (để trống sẽ lấy tiêu đề)">
            </div>

            <div class="form-group">
                <label>Meta Description:</label>
                <textarea class="input-meta-description" 
                          name="meta_description" 
                          rows="3"
                          placeholder="Mô tả SEO (tối đa 160 ký tự)"><?php echo htmlspecialchars($formData['meta_description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Meta Keywords:</label>
                <input class="input-meta-keywords" type="text" 
                       name="meta_keywords"
                       value="<?php echo htmlspecialchars($formData['meta_keywords'] ?? ''); ?>"
                       placeholder="Từ khóa SEO, cách nhau bằng dấu phẩy">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label>Mô tả ngắn:</label>
                <textarea class="input-description" 
                          name="description" 
                          rows="4"
                          placeholder="Mô tả ngắn về bài viết (hiển thị ở trang danh sách)"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
            </div>

            <!-- CONTENT -->
            <div class="form-group">
                <label>Nội dung bài viết (hỗ trợ chèn hình ảnh vào giữa văn bản):</label>
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
                    <div id="editor" class="editor-area" contenteditable="true"
                        placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."><?php echo htmlspecialchars_decode($formData['content'] ?? ''); ?></div>
                    <input type="hidden" name="content" id="contentInput">
                </div>
                <input type="file" id="imageUpload" accept="image/*" hidden>
            </div>

            <!-- BOTTOM LAYOUT -->
            <div class="bottom-layout">

                <!-- LEFT -->
                <div class="publish-settings">
                    <h3>CẤU HÌNH XUẤT BẢN</h3>

                    <label>TRẠNG THÁI</label>
                    <select class="input-status" name="status">
                        <option value="draft" <?php echo (($formData['status'] ?? '') == 'draft') ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                        <option value="published" <?php echo (($formData['status'] ?? 'published') == 'published') ? 'selected' : ''; ?>>Đã đăng (Published)</option>
                        <option value="scheduled" <?php echo (($formData['status'] ?? '') == 'scheduled') ? 'selected' : ''; ?>>Hẹn giờ (Scheduled)</option>
                        <option value="archived" <?php echo (($formData['status'] ?? '') == 'archived') ? 'selected' : ''; ?>>Lưu trữ (Archived)</option>
                    </select>

                    <div id="scheduledDatetime" style="display: none;">
                        <label>THỜI GIAN HẸN GIỜ</label>
                        <input class="input-scheduled" type="datetime-local" name="scheduled_at"
                               value="<?php echo htmlspecialchars($formData['scheduled_at'] ?? ''); ?>">
                    </div>

                    <label>LƯỢT XEM (VIEWS)</label>
                    <input class="input-views" type="number" name="views" 
                           value="<?php echo htmlspecialchars($formData['views'] ?? 0); ?>"
                           placeholder="Số lượt xem">
                    <small>(Sẽ tự động cập nhật khi có người xem)</small>

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
                           value="<?php echo htmlspecialchars($formData['source'] ?? ''); ?>"
                           placeholder="Tên nguồn (VD: EMIR, Reuters, ...)">
                    
                    <label>URL NGUỒN</label>
                    <input class="input-source-url" type="url" name="source_url"
                           value="<?php echo htmlspecialchars($formData['source_url'] ?? ''); ?>"
                           placeholder="https://...">
                </div>

                <!-- RIGHT -->
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
                    <div id="imagePreview" style="display: none; margin-top: 10px;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; border-radius: 8px;">
                        <button type="button" id="removeImage" style="margin-top: 5px;">Xóa ảnh</button>
                    </div>
                    <input type="text" name="featured_image_caption" 
                           value="<?php echo htmlspecialchars($formData['featured_image_caption'] ?? ''); ?>"
                           placeholder="Chú thích ảnh" style="margin-top: 10px; width: 100%;">
                </div>

            </div>

            <!-- Ghi chú tác giả -->
            <div class="form-group">
                <label>Ghi chú tác giả:</label>
                <textarea class="input-author-note" 
                          name="author_note" 
                          rows="3"
                          placeholder="Ghi chú đặc biệt từ tác giả"><?php echo htmlspecialchars($formData['author_note'] ?? ''); ?></textarea>
            </div>

            <!-- ACTION BUTTON -->
            <div class="action-buttons">
                <button type="submit" name="action" value="draft" class="btn-draft">Lưu nháp</button>
                <button type="submit" name="action" value="publish" class="btn-publish">Đăng bài</button>
            </div>

            <div class="cancel-text">Hủy bỏ và quay lại</div>

        </div>
    </form>

</main>

<script>
// Auto generate slug from title
document.getElementById('newsTitle').addEventListener('blur', function() {
    let title = this.value;
    if (title && !document.getElementById('newsSlug').value) {
        fetch('index.php?controller=news&action=generateSlug', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'title=' + encodeURIComponent(title)
        })
        .then(response => response.json())
        .then(data => {
            if (data.slug) {
                document.getElementById('newsSlug').value = data.slug;
            }
        });
    }
});

// Auto fill author name when select author
document.getElementById('authorId').addEventListener('change', function() {
    let authorId = this.value;
    if (authorId) {
        let selectedOption = this.options[this.selectedIndex];
        document.getElementById('authorName').value = selectedOption.text;
    }
});

// Preview image
document.getElementById('uploadBox').addEventListener('click', function() {
    document.getElementById('featuredImage').click();
});

document.getElementById('featuredImage').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('uploadBox').style.display = 'none';
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

document.getElementById('removeImage').addEventListener('click', function() {
    document.getElementById('featuredImage').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('uploadBox').style.display = 'flex';
});

// Show/hide scheduled datetime
document.querySelector('select[name="status"]').addEventListener('change', function() {
    let scheduledDiv = document.getElementById('scheduledDatetime');
    if (this.value === 'scheduled') {
        scheduledDiv.style.display = 'block';
    } else {
        scheduledDiv.style.display = 'none';
    }
});

// Update content hidden input before submit
document.getElementById('articleForm').addEventListener('submit', function() {
    document.getElementById('contentInput').value = document.getElementById('editor').innerHTML;
});

// Simple editor functions
document.querySelectorAll('[data-cmd]').forEach(btn => {
    btn.addEventListener('click', function() {
        let cmd = this.dataset.cmd;
        let value = this.dataset.value || null;
        document.execCommand(cmd, false, value);
        document.getElementById('editor').focus();
    });
});

document.querySelectorAll('[data-heading]').forEach(btn => {
    btn.addEventListener('click', function() {
        let level = this.dataset.heading;
        document.execCommand('formatBlock', false, 'H' + level);
        document.getElementById('editor').focus();
    });
});

document.getElementById('textColor').addEventListener('input', function() {
    document.execCommand('foreColor', false, this.value);
});

document.getElementById('addLink').addEventListener('click', function() {
    let url = prompt('Nhập URL:', 'https://');
    if (url) {
        document.execCommand('createLink', false, url);
    }
});

document.getElementById('insertImage').addEventListener('click', function() {
    let fileInput = document.getElementById('imageUpload');
    fileInput.onchange = function(e) {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('image', file);
        
        fetch('index.php?controller=news&action=uploadEditorImage', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.execCommand('insertImage', false, data.url);
            } else {
                alert('Lỗi upload: ' + data.error);
            }
        });
    };
    fileInput.click();
});

document.getElementById('insertVideo').addEventListener('click', function() {
    let embedCode = prompt('Nhập mã nhúng video (YouTube/Vimeo):', '<iframe src="https://www.youtube.com/embed/..."></iframe>');
    if (embedCode) {
        document.execCommand('insertHTML', false, embedCode);
    }
});

document.getElementById('fontFamily').addEventListener('change', function() {
    document.execCommand('fontName', false, this.value);
});
</script>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.required {
    color: red;
}

.inline-label {
    display: inline;
    margin-left: 5px;
    font-weight: normal;
}

#scheduledDatetime {
    margin-top: 10px;
    margin-bottom: 15px;
}

.input-scheduled {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
}
</style>