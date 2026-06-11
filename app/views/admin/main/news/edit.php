<?php
/**
 * edit.php - View sửa bài viết trong admin panel
 * Form gửi dữ liệu đến route /admin/main/news/update/{id}
 */

$article = $data['article'] ?? [];
$categories = $data['categories'] ?? [];
$authors = $data['authors'] ?? [];
?>

<style>
    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .label-row label {
        font-weight: 500;
        color: #374151;
    }
    .required {
        color: #ef4444;
    }
    .ai-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        border: none;
    }
    .input-title, .input-slug, .input-author, .input-meta-title, 
    .input-meta-keywords, .input-source, .input-source-url, 
    .input-views, .input-scheduled {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .input-title:focus, .input-slug:focus, .input-author:focus,
    .input-meta-title:focus, .input-meta-keywords:focus,
    .input-source:focus, .input-source-url:focus,
    .input-views:focus, .input-scheduled:focus,
    select:focus, textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59,130,246,0.1);
    }
    select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        background: white;
    }
    textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        resize: vertical;
    }
    small {
        font-size: 12px;
        color: #6b7280;
        display: block;
        margin-top: 4px;
    }
    .editor-box {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
    }
    .editor-toolbar {
        background: #f9fafb;
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .editor-toolbar button {
        background: white;
        border: 1px solid #e5e7eb;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .editor-toolbar button:hover {
        background: #f3f4f6;
    }
    .divider {
        width: 1px;
        height: 24px;
        background: #e5e7eb;
    }
    .editor-area {
        min-height: 400px;
        padding: 16px;
        background: white;
        overflow-y: auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .editor-area:focus {
        outline: none;
    }
    .editor-area[placeholder]:empty:before {
        content: attr(placeholder);
        color: #9ca3af;
    }
    .bottom-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin: 20px 0;
    }
    .publish-settings {
        background: #f9fafb;
        padding: 20px;
        border-radius: 8px;
    }
    .publish-settings h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 16px;
        color: #374151;
    }
    .publish-settings label {
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        font-weight: 500;
        font-size: 13px;
        color: #6b7280;
    }
    .publish-settings .inline-label {
        display: inline-block;
        margin-left: 8px;
        font-weight: normal;
        margin-top: 0;
    }
    .publish-settings input[type="checkbox"] {
        width: auto;
        margin-top: 15px;
    }
    .thumbnail-box {
        background: #f9fafb;
        padding: 20px;
        border-radius: 8px;
    }
    .thumb-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-weight: 500;
    }
    .upload-box {
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .upload-box:hover {
        border-color: #3b82f6;
    }
    .upload-box span {
        font-size: 48px;
        display: block;
        margin-bottom: 10px;
    }
    .current-image {
        margin-top: 15px;
        padding: 10px;
        background: white;
        border-radius: 6px;
    }
    .current-image img {
        max-width: 100%;
        border-radius: 4px;
    }
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    .btn-update {
        background: #3b82f6;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-update:hover {
        opacity: 0.9;
    }
    .btn-cancel:hover {
        background: #e5e7eb;
    }
    .cancel-text {
        text-align: center;
        margin-top: 20px;
        color: #6b7280;
        cursor: pointer;
        font-size: 14px;
    }
    .cancel-text:hover {
        color: #ef4444;
    }
    #imagePreview {
        margin-top: 15px;
    }
    #previewImg {
        max-width: 100%;
        border-radius: 8px;
    }
    #removeImage {
        background: #ef4444;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 8px;
    }
    @media (max-width: 768px) {
        .bottom-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="main news-edit-page">
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form id="articleForm" action="/admin/main/news/update/<?php echo $article['id']; ?>" method="POST" enctype="multipart/form-data">
        <!-- HEADER -->
        <div class="add-header">
            <h1 style="margin: 0;">SỬA BÀI VIẾT</h1>
        </div>

        <div class="add-container">

            <!-- TITLE -->
            <div class="form-group">
                <div class="label-row">
                    <label>Tiêu đề bài viết: <span class="required">*</span></label>
                    <span class="ai-btn" type="button" onclick="generateAITitle()">✨ Gợi ý bằng AI</span>
                </div>
                <input class="input-title" type="text"
                    name="title"
                    id="newsTitle"
                    value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>"
                    placeholder="Nhập tiêu đề bài viết tại đây..."
                    required>
            </div>

            <!-- SLUG -->
            <div class="form-group">
                <label>Slug (URL):</label>
                <input class="input-slug" type="text"
                    name="slug"
                    id="newsSlug"
                    value="<?php echo htmlspecialchars($article['slug'] ?? ''); ?>"
                    placeholder="URL thân thiện">
                <small>Slug sẽ được tạo tự động từ tiêu đề nếu để trống</small>
            </div>

            <!-- CATEGORY -->
            <div class="form-group">
                <label>Chuyên mục:</label>
                <select class="input-category" name="category_id">
                    <option value="">-- Chọn chuyên mục --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"
                            <?php echo (($article['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- AUTHOR SELECT -->
            <div class="form-group">
                <label>Tác giả (từ danh sách):</label>
                <select class="input-author-id" name="author_id" id="authorId">
                    <option value="">-- Chọn tác giả --</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['id']; ?>"
                            <?php echo (($article['author_id'] ?? '') == $author['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- AUTHOR NAME -->
            <div class="form-group">
                <label>Tên tác giả hiển thị: <span class="required">*</span></label>
                <input class="input-author" type="text"
                    name="author"
                    id="authorName"
                    value="<?php echo htmlspecialchars($article['author'] ?? ''); ?>"
                    placeholder="Tên tác giả hiển thị trên bài viết"
                    required>
                <small>Tên sẽ được lưu trực tiếp vào cột author của bảng news</small>
            </div>

            <!-- PUBLISH DATE -->
            <div class="form-group">
                <label>Ngày xuất bản:</label>
                <input class="input-publish-date" type="date"
                    name="publish_date"
                    value="<?php echo htmlspecialchars($article['publish_date'] ?? date('Y-m-d')); ?>">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label>Mô tả ngắn:</label>
                <textarea class="input-description"
                    name="description"
                    rows="4"
                    placeholder="Mô tả ngắn về bài viết (hiển thị ở trang danh sách)"><?php echo htmlspecialchars($article['description'] ?? ''); ?></textarea>
            </div>

            <!-- CONTENT -->
            <div class="form-group">
                <label>Nội dung bài viết: <span class="required">*</span></label>
                <div class="editor-box">
                    <div class="editor-toolbar">
                        <select id="fontFamily" onchange="changeFontFamily(this.value)">
                            <option value="serif">Font: Serif</option>
                            <option value="arial">Arial</option>
                            <option value="times-new-roman">Times New Roman</option>
                            <option value="courier-new">Courier New</option>
                            <option value="roboto">Roboto</option>
                            <option value="monospace">Monospace</option>
                        </select>
                        <span class="divider"></span>
                        <button type="button" onclick="execCmd('bold')"><b>B</b></button>
                        <button type="button" onclick="execCmd('italic')"><i>I</i></button>
                        <button type="button" onclick="execCmd('underline')"><u>U</u></button>
                        <span class="divider"></span>
                        <input type="color" id="textColor" onchange="changeTextColor(this.value)">
                        <span class="divider"></span>
                        <button type="button" onclick="formatHeading(1)">H1</button>
                        <button type="button" onclick="formatHeading(2)">H2</button>
                        <span class="divider"></span>
                        <button type="button" onclick="execCmd('formatBlock', 'blockquote')">❝</button>
                        <button type="button" onclick="addLink()"><i class="fas fa-link"></i></button>
                        <span class="divider"></span>
                        <button type="button" onclick="insertImageFromUrl()"><i class="fas fa-image"></i> Chèn hình ảnh</button>
                        <button type="button" onclick="insertVideo()"><i class="fa-solid fa-video"></i> Chèn video</button>
                    </div>
                    <div id="editor" class="editor-area" contenteditable="true"
                        placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."><?php echo htmlspecialchars_decode($article['content'] ?? ''); ?></div>
                    <input type="hidden" name="content" id="contentInput">
                </div>
            </div>

            <!-- BOTTOM LAYOUT -->
            <div class="bottom-layout">

                <!-- LEFT -->
                <div class="publish-settings">
                    <h3>CẤU HÌNH XUẤT BẢN</h3>

                    <label>TRẠNG THÁI</label>
                    <select class="input-status" name="status" id="statusSelect">
                        <option value="draft" <?php echo ($article['status'] ?? 'draft') == 'draft' ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                        <option value="published" <?php echo ($article['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Đã đăng (Published)</option>
                        <option value="archived" <?php echo ($article['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Lưu trữ (Archived)</option>
                    </select>

                    <label>LƯỢT XEM (VIEWS)</label>
                    <input class="input-views" type="number" name="views"
                        value="<?php echo htmlspecialchars($article['views'] ?? 0); ?>"
                        placeholder="Số lượt xem">
                    <small>Sẽ tự động cập nhật khi có người xem</small>

                    <label>NGUỒN BÀI VIẾT</label>
                    <input class="input-source" type="text" name="source"
                        value="<?php echo htmlspecialchars($article['source'] ?? ''); ?>"
                        placeholder="Tên nguồn (VD: EMIR, Reuters, ...)">

                    <label>URL NGUỒN</label>
                    <input class="input-source-url" type="url" name="source_url"
                        value="<?php echo htmlspecialchars($article['source_url'] ?? ''); ?>"
                        placeholder="https://...">
                </div>

                <!-- RIGHT -->
                <div class="thumbnail-box">
                    <div class="thumb-header">
                        ẢNH ĐẠI DIỆN BÀI VIẾT
                        <span class="ai-btn" type="button" onclick="generateAIImage()">✨ Tạo bằng AI</span>
                    </div>
                    <div class="upload-box" id="uploadBox" onclick="document.getElementById('featuredImage').click()">
                        <span>📷</span>
                        <p>Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                        <small>Định dạng: JPG, PNG, WEBP (max 5MB)</small>
                        <input type="file" name="featured_image" id="featuredImage" accept="image/jpeg,image/png,image/webp" hidden onchange="previewImage(this)">
                    </div>
                    
                    <?php if (!empty($article['image'])): ?>
                    <div class="current-image" id="currentImage">
                        <strong>Ảnh hiện tại:</strong>
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="Current image">
                        <button type="button" onclick="removeCurrentImage()" style="background: #ef4444; color: white; border: none; padding: 4px 8px; border-radius: 4px; margin-top: 8px; cursor: pointer;">Xóa ảnh hiện tại</button>
                    </div>
                    <?php endif; ?>
                    
                    <div id="imagePreview" style="display: none; margin-top: 10px;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; border-radius: 8px;">
                        <button type="button" id="removeImage" onclick="removeNewImage()">Xóa ảnh mới</button>
                    </div>
                    
                    <input type="text" name="featured_image_caption"
                        value="<?php echo htmlspecialchars($article['featured_image_caption'] ?? ''); ?>"
                        placeholder="Chú thích ảnh" style="margin-top: 10px; width: 100%;">
                </div>

            </div>

            <!-- ACTION BUTTON -->
            <div class="action-buttons">
                <a href="/admin/main/news" class="btn-cancel">Hủy</a>
                <button type="submit" class="btn-update">Cập nhật bài viết</button>
            </div>

            <div class="cancel-text" onclick="window.location.href='/admin/main/news'">Quay lại danh sách</div>

        </div>
    </form>

</main>

<script>
    // Editor functions
    function execCmd(command, value = null) {
        document.execCommand(command, false, value);
        updateContentInput();
    }

    function changeFontFamily(font) {
        document.execCommand('fontName', false, font);
        updateContentInput();
    }

    function changeTextColor(color) {
        document.execCommand('foreColor', false, color);
        updateContentInput();
    }

    function formatHeading(level) {
        document.execCommand('formatBlock', false, 'H' + level);
        updateContentInput();
    }

    function addLink() {
        const url = prompt('Nhập URL liên kết:', 'https://');
        if (url) {
            document.execCommand('createLink', false, url);
            updateContentInput();
        }
    }

    function insertImageFromUrl() {
        const url = prompt('Nhập URL hình ảnh:', 'https://');
        if (url) {
            document.execCommand('insertImage', false, url);
            updateContentInput();
        }
    }

    function insertVideo() {
        const embedCode = prompt('Nhập mã nhúng video (YouTube/Vimeo):', '<iframe src="https://www.youtube.com/embed/..."></iframe>');
        if (embedCode) {
            document.execCommand('insertHTML', false, embedCode);
            updateContentInput();
        }
    }

    function updateContentInput() {
        const editor = document.getElementById('editor');
        document.getElementById('contentInput').value = editor.innerHTML;
    }

    // Auto update content input on editor change
    document.getElementById('editor').addEventListener('input', updateContentInput);
    document.getElementById('editor').addEventListener('blur', updateContentInput);

    // Slug generation from title
    function generateSlug(text) {
        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, function(match) {
                return match === 'đ' ? 'd' : 'd';
            })
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    document.getElementById('newsTitle').addEventListener('blur', function() {
        const slugInput = document.getElementById('newsSlug');
        if (!slugInput.value.trim() || slugInput.value === generateSlug(document.getElementById('newsTitle').dataset.oldTitle)) {
            slugInput.value = generateSlug(this.value);
        }
    });

    // Store original title
    document.getElementById('newsTitle').dataset.oldTitle = document.getElementById('newsTitle').value;

    // Author selection
    document.getElementById('authorId').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('authorName').value = selectedOption.text;
        }
    });

    // Image preview for new image
    let newImageSelected = false;
    
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (input.files && input.files[0]) {
            newImageSelected = true;
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeNewImage() {
        document.getElementById('featuredImage').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('previewImg').src = '';
        newImageSelected = false;
    }

    function removeCurrentImage() {
        if (confirm('Bạn có chắc muốn xóa ảnh hiện tại?')) {
            // Add hidden field to indicate delete current image
            const deleteFlag = document.createElement('input');
            deleteFlag.type = 'hidden';
            deleteFlag.name = 'delete_current_image';
            deleteFlag.value = '1';
            document.getElementById('articleForm').appendChild(deleteFlag);
            
            document.getElementById('currentImage').style.display = 'none';
        }
    }

    // AI functions (mock)
    function generateAITitle() {
        alert('Tính năng gợi ý tiêu đề bằng AI sẽ sớm ra mắt!');
    }

    function generateAIImage() {
        alert('Tính năng tạo ảnh bằng AI sẽ sớm ra mắt!');
    }

    // Initialize
    updateContentInput();
</script>