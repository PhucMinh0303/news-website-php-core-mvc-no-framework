<?php

/**
 * create-news.php - View thêm bài viết mới trong admin panel
 * Form gửi dữ liệu đến route admin/news/create
 */

// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['old_input'] ?? ($article ?? []);
$errors = $_SESSION['errors'] ?? ($errors ?? []);
$categories = $categories ?? [];
$authors = $authors ?? [];
$editId = null;
$isAdminLayout = !empty($admin_layout);
if (isset($edit_id)) {
    $editId = $edit_id;
}
$isEditing = $editId !== null;
$currentImage = $formData['image'] ?? $formData['featured_image'] ?? '';
$existingImageUrl = '';
if ($isEditing && $currentImage !== '') {
    $imageFilename = basename($currentImage);
    foreach (['public/upload/news/', 'public/uploads/news/'] as $imageDirectory) {
        if (is_file(ROOT_PATH . $imageDirectory . $imageFilename)) {
            $existingImageUrl = BASE_URL . 'public/upload/news/' . rawurlencode($imageFilename);
            break;
        }
    }
}

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>

<?php if (!$isAdminLayout): ?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

    </head>
<?php endif; ?>

<!-- ==================== VIDEO MODAL ==================== -->
<div id="videoModal" style="display: none;">
    <div class="video-modal-content">
        <h2 class="video-modal-title">CHÈN VIDEO</h2>

        <!-- Nhập URL YouTube -->
        <div class="video-input-section">
            <label class="video-input-label">Nhập URL YouTube</label>
            <div class="video-input-wrapper">
                <input type="text" id="youtubeUrl" class="video-input-field" placeholder="https://www.youtube.com/watch?v=..." autocomplete="off">
                <button class="video-insert-btn" onclick="insertYoutubeVideo()">Chèn</button>
            </div>
        </div>

        <div class="video-divider">
            <span>HOẶC</span>
        </div>

        <!-- Chọn từ Video File Explorer -->
        <div class="video-file-section">
            <p class="video-file-label">Chọn từ Video File Explorer</p>
            <div class="video-upload-box" onclick="document.getElementById('videoFileInput').click();">
                <div class="video-upload-icon">📹</div>
                <p class="video-upload-text">Mở File Explorer</p>
            </div>
            <div class="video-file-name" id="videoFileName"></div>
            <input type="file" id="videoFileInput" accept="video/*" style="display: none;" onchange="handleVideoFileUpload(event)">
        </div>

        <!-- Nút hủy bỏ -->
        <button class="video-cancel-btn" onclick="closeVideoModal()">Hủy bỏ</button>
    </div>
</div>

<?php if (!$isAdminLayout): ?>

    <body><?php endif; ?>
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
            TRANG ADMIN - <?php echo $isEditing ? 'CHỈNH SỬA TIN TỨC' : 'ĐĂNG TIN TỨC MỚI'; ?>
            <?php if ($isEditing): ?>
                <span style="font-size: 13px; font-weight: normal; opacity: 0.7; margin-left: 10px;">(ID: #<?php echo (int) $editId; ?>)</span>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($editId ? Router::url('admin/news/update', [$editId]) : Router::url('admin/news/create')); ?>" data-store-url="<?php echo htmlspecialchars(Router::url('admin/news'), ENT_QUOTES, 'UTF-8'); ?>" data-upload-image-url="<?php echo htmlspecialchars(Router::url('admin/news-image/upload'), ENT_QUOTES, 'UTF-8'); ?>" enctype="multipart/form-data" id="newsForm" <?php echo $isEditing ? ' data-edit-mode="1"' : ''; ?>>
            <?php if ($isEditing): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editId; ?>">
            <?php endif; ?>
            <div class="add-container">

                <!-- Tiêu đề bài viết -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Tiêu đề tin tức: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="newsForm.generateAITitle()">✨ Gợi ý bằng AI</button>
                    </div>
                    <input class="input-title" type="text" name="title" id="news_title"
                        placeholder="Nhập tiêu đề tin tuyển dụng tại đây..."
                        value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>"
                        autocomplete="off">
                    <small>Nhập tiêu đề, slug sẽ tự động tạo từ bảng chữ cái</small>
                </div>

                <!-- Slug (URL) -->
                <div class="form-group">
                    <label>Slug (URL):</label>
                    <input type="hidden" name="slug_original" id="slugOriginal" value="<?php echo htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input class="input-slug" type="text" name="slug" id="slug" placeholder="[ Tự động tạo từ tiêu đề ]"
                        value="<?php echo htmlspecialchars($formData['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    <small>(Slug được tạo tự động từ tiêu đề, chỉ gồm chữ cái, số và dấu gạch ngang)</small>
                </div>

                <!-- Danh mục và Tác giả -->
                <div class="grid-2cols">
                    <div class="form-group">
                        <label>Danh mục:</label>
                        <select name="category_id">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo (($formData['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tác giả (trong hệ thống):</label>
                        <select name="author_id">
                            <option value="">-- Chọn tác giả (nếu có) --</option>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['id']; ?>"
                                    <?php echo (($formData['author_id'] ?? '') == $author['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($author['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Tên tác giả hiển thị và Ngày đăng -->
                <div class="grid-2cols">
                    <div class="form-group">
                        <label>Tên tác giả hiển thị: <span class="required">*</span></label>
                        <input type="text" name="author"
                            placeholder="VD: Nguyễn Văn A"
                            value="<?php echo htmlspecialchars($formData['author'] ?? ''); ?>">
                        <small>Tên sẽ hiển thị trên bài viết</small>
                    </div>
                    <div class="form-group">
                        <label>Ngày đăng: <span class="required">*</span></label>
                        <input type="date" name="publish_date"
                            value="<?php echo htmlspecialchars($formData['publish_date'] ?? date('Y-m-d')); ?>">
                        <small>Chọn ngày xuất bản bài viết</small>
                    </div>
                </div>

                <!-- Nội dung bài viết với Rich Text Editor -->
                <div class="form-group">
                    <div class="label-row">
                        <label>Nội dung bài viết: <span class="required">*</span></label>
                        <button type="button" class="ai-btn" onclick="generateAIContent()">✨ Gợi ý bằng AI</button>
                    </div>

                    <!-- Rich Text Editor Toolbar -->
                    <div class="editor-instructions">
                        <div class="rich-editor-toolbar">
                            <div class="tool-group">
                                <select id="fontFamily" title="Font chữ">
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Roboto">Roboto</option>
                                </select>
                            </div>

                            <div class="tool-group">
                                <button type="button" id="btnBold" onclick="wrapText('bold')" title="In đậm"><b>B</b></button>
                                <button type="button" id="btnItalic" onclick="wrapText('italic')" title="In nghiêng"><i>I</i></button>
                                <button type="button" id="btnUnderline" onclick="wrapText('underline')" title="Gạch chân"><u>U</u></button>
                            </div>

                            <div class="tool-group">
                                <button type="button" id="btnUnorderedList" onclick="wrapText('insertUnorderedList')" title="Danh sách có dấu chấm ở đầu dòng"><i class="fa-solid fa-list"></i></button>
                                <button type="button" id="btnOrderedList" onclick="wrapText('insertOrderedList')" title="Danh sách có số ở đầu dòng">1.2.3</button>
                            </div>

                            <div class="tool-group">
                                <button type="button" id="btnAlignLeft" onclick="wrapText('justifyLeft')" title="Căn trái"><i class="fa-solid fa-align-left"></i></button>
                                <button type="button" id="btnAlignCenter" onclick="wrapText('justifyCenter')" title="Căn giữa"><i class="fa-solid fa-align-center"></i></button>
                                <button type="button" id="btnAlignRight" onclick="wrapText('justifyRight')" title="Căn phải"><i class="fa-solid fa-align-right"></i></button>
                            </div>

                            <div class="tool-group">
                                <select id="headingSelect" onchange="applyHeadingToTextarea(this.value)" title="Định dạng tiêu đề hoặc đoạn văn">
                                    <option value="">Heading</option>
                                    <option value="h1">H1</option>
                                    <option value="h2">H2</option>
                                    <option value="h3">H3</option>
                                    <option value="h4">H4</option>
                                    <option value="p">Normal</option>
                                </select>
                            </div>

                            <div class="tool-group">
                                <div class="color-picker-wrapper">
                                    <button type="button" class="color-btn" id="textColorBtn" title="Màu chữ">
                                        <span><i class="fa-solid fa-paint-roller"></i></span>
                                        <div class="color-indicator" id="colorIndicator" style="background-color: #000000;"></div>
                                    </button>
                                    <div class="color-dropdown" id="colorDropdown" style="display: none;">
                                        <div class="color-section theme-section">
                                            <div class="color-title">Theme Colors</div>
                                            <div class="color-grid theme-colors"></div>
                                        </div>
                                        <div class="color-section standard-section">
                                            <div class="color-title">Standard Colors</div>
                                            <div class="color-grid standard-colors"></div>
                                        </div>
                                        <div class="color-divider"></div>
                                        <div class="color-option" id="noColorOption">
                                            <span>No Color</span>
                                        </div>
                                        <div class="color-divider"></div>
                                        <div class="color-option" id="moreColorsOption">
                                            <span>More Colors...</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Màu nền (Highlight) -->
                                <div class="color-picker-wrapper">
                                    <button type="button" class="color-btn" id="highlightColorBtn" title="Màu Hightlight">
                                        <span><i class="fa-solid fa-highlighter"></i></span>
                                        <div class="color-indicator" id="highlightIndicator" style="background-color: #ffffff;"></div>
                                    </button>
                                    <div class="color-dropdown" id="highlightDropdown" style="display: none;">
                                        <div class="color-section theme-section">
                                            <div class="color-title">Theme Colors</div>
                                            <div class="color-grid highlight-theme-colors"></div>
                                        </div>
                                        <div class="color-section standard-section">
                                            <div class="color-title">Standard Colors</div>
                                            <div class="color-grid highlight-standard-colors"></div>
                                        </div>
                                        <div class="color-divider"></div>
                                        <div class="color-option" id="noHighlightOption">
                                            <span>No Color</span>
                                        </div>
                                        <div class="color-divider"></div>
                                        <div class="color-option" id="moreHighlightColorsOption">
                                            <span>More Colors...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tool-group">
                                <button type="button" onclick="insertImageToTextarea()" title="Chèn ảnh"><i class="fa-solid fa-image"></i> Ảnh</button>
                                <button type="button" onclick="openVideoModalForTextarea()" title="Chèn video"><i class="fa-solid fa-video"></i> Video</button>
                            </div>

                            <div class="tool-group">
                                <button type="button" onclick="createLinkForTextarea()" title="Chèn link"><i class="fa-solid fa-link"></i> Link</button>
                                <button type="button" onclick="removeTextareaFormat()" title="Xóa định dạng"><i class="fa-solid fa-recycle"></i> Xóa format</button>
                            </div>
                        </div>

                        <textarea name="content" id="news_content_source" rows="20" tabindex="-1" aria-hidden="true" style="display: none;"><?php
                                                                                                                                            echo htmlspecialchars($formData['content'] ?? '', ENT_QUOTES);
                                                                                                                                            ?></textarea>
                        <div id="news_content" class="rich-editor-content" contenteditable="true" role="textbox" aria-multiline="true"
                            data-placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."></div>

                        <small>Hỗ trợ định dạng HTML. Bạn có thể sử dụng các công cụ định dạng phía trên.</small>
                    </div>

                </div>

                <!-- Cấu hình đăng bài -->
                <div class="bottom-layout">
                    <div class="publish-settings">
                        <h3>⚙️ CẤU HÌNH ĐĂNG BÀI</h3>

                        <label>TRẠNG THÁI</label>
                        <select name="status">
                            <option value="draft" <?php echo (($formData['status'] ?? 'draft') == 'draft') ? 'selected' : ''; ?>>
                                📝 Bản nháp (Draft)
                            </option>
                            <option value="published" <?php echo (($formData['status'] ?? '') == 'published') ? 'selected' : ''; ?>>
                                ✅ Đã đăng (Published)
                            </option>
                            <option value="archived" <?php echo (($formData['status'] ?? '') == 'archived') ? 'selected' : ''; ?>>
                                📦 Lưu trữ (Archived)
                            </option>
                        </select>

                        <small>
                            • <strong>Bản nháp</strong>: Chưa hiển thị ra ngoài<br>
                            • <strong>Đã đăng</strong>: Hiển thị công khai trên website<br>
                            • <strong>Lưu trữ</strong>: Ẩn khỏi giao diện người dùng
                        </small>
                    </div>

                    <div class="thumbnail-box">
                        <div class="thumb-header">
                            🖼️ ẢNH ĐẠI DIỆN BÀI VIẾT
                            <button type="button" class="ai-btn" onclick="generateAIImage()">✨ Tạo bằng AI</button>
                        </div>

                        <div class="upload-box" id="uploadBox" data-existing-image="<?php echo htmlspecialchars($existingImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
                            <span id="uploadIcon">📷</span>
                            <p id="uploadText"><?php echo $existingImageUrl ? 'Ảnh hiện tại (chọn ảnh mới để thay thế)' : 'Tải lên ảnh đại diện (JPG, PNG, WEBP)'; ?></p>
                            <small id="uploadInfo"><?php echo $existingImageUrl ? 'Bỏ trống nếu không muốn thay đổi ảnh' : 'Kích thước khuyến nghị: 1200x630px (Max 15MB)'; ?></small>
                        </div>
                        <input type="file" id="imageInput" name="featured_image" accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <div id="imagePreview" style="margin-top: 15px; display: <?php echo $existingImageUrl ? 'block' : 'none'; ?>;">
                            <img id="previewImg" src="<?php echo htmlspecialchars($existingImageUrl ?: '#', ENT_QUOTES, 'UTF-8'); ?>" alt="Preview" style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                            <button type="button" onclick="newsForm.removeImage()" style="display: block; margin-top: 8px; background: #fee2e2; color: #991b1b; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer;">✖️ Xóa ảnh</button>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="action-buttons">
                    <button type="submit" name="save_draft" value="0" class="btn-draft" onclick="syncEditorContent()">Lưu bản nháp</button>
                    <button type="submit" name="publish" value="1" class="btn-publish" onclick="syncEditorContent()">Đăng bài ngay</button>
                    <button type="submit" name="save_and_continue" value="1" class="btn-secondary" onclick="syncEditorContent()">Lưu và tiếp tục</button>
                </div>

                <div class="cancel-text" data-page="news">
                    ← Hủy bỏ và quay lại danh sách
                </div>

            </div>
        </form>
    </main>

    <?php if (!$isAdminLayout): ?>
    </body><?php endif; ?>
<script>
    // Truyền lỗi từ PHP session sang JavaScript
    window.serverErrors = <?php echo json_encode($errors); ?>;
</script>
<?php if (!$isAdminLayout): ?>
    <script src="<?php echo View::asset('js/admin/slug.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/news-create.js'); ?>"></script>
<?php endif; ?>


<?php if (!$isAdminLayout): ?>

    </html><?php endif; ?>