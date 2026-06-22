<?php

/**
 * create-news.php - View thêm bài viết mới trong admin panel
 * Form gửi dữ liệu đến route /admin/main/news/store
 */

// Lấy dữ liệu từ session (nếu có lỗi từ controller)
$formData = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$categories = $data['categories'] ?? [];
$authors = $data['authors'] ?? [];

// Xóa session data sau khi lấy
unset($_SESSION['old_input']);
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Rich Text Editor Styles */
        .rich-editor-toolbar {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 8px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .rich-editor-toolbar .tool-group {
            display: flex;
            gap: 4px;
            border-right: 1px solid #e5e7eb;
            padding-right: 8px;
            margin-right: 4px;
        }

        .rich-editor-toolbar .tool-group:last-child {
            border-right: none;
        }

        .rich-editor-toolbar button {
            background: transparent;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .rich-editor-toolbar button:hover {
            background: #e5e7eb;
        }

        .rich-editor-toolbar button.active {
            background: #3b82f6;
            color: white;
        }

        .rich-editor-toolbar select {
            width: auto;
            padding: 5px 8px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: white;
        }

        .rich-editor-toolbar input[type="color"] {
            width: 32px;
            height: 32px;
            padding: 2px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            cursor: pointer;
        }

        .editor-instructions .rich-editor-content {
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
            min-height: 400px;
            width: 100%;
            padding: 16px;
            background: white;
            overflow-y: auto;
            font-family: inherit;
        }

        .editor-instructions .rich-editor-content:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .editor-instructions .rich-editor-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .editor-instructions .rich-editor-content iframe {
            max-width: 100%;
            border-radius: 8px;
        }

        .editor-instructions .rich-editor-content a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .editor-instructions .rich-editor-content a:hover {
            color: #2563eb;
        }

        /* Color Picker Styles - Giống MS Word */
        .color-picker-wrapper {
            position: relative;
            display: inline-block;
        }

        .color-btn {
            background: transparent;
            border: 1px solid #e5e7eb;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            min-width: 42px;
            transition: all 0.2s;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .color-btn:hover {
            background: #e5e7eb;
        }

        .color-btn span {
            font-size: 16px;
            font-weight: bold;
        }

        .color-indicator {
            width: 20px;
            height: 3px;
            border-radius: 2px;
            margin-top: 2px;
        }

        .color-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 5px;
            background: white;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 220px;
        }

        .color-section {
            margin-bottom: 12px;
        }

        .color-title {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 3px;
        }

        .color-grid.theme-colors,
        .color-grid.highlight-theme-colors {
            grid-template-columns: repeat(10, 1fr);
        }

        .color-grid.standard-colors,
        .color-grid.highlight-standard-colors {
            grid-template-columns: repeat(10, 1fr);
        }

        .color-item {
            width: 20px;
            height: 20px;
            border-radius: 2px;
            cursor: pointer;
            border: 1px solid #e0e0e0;
            transition: all 0.15s;
        }

        .color-item:hover {
            transform: scale(1.1);
            border-color: #1e88e5;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .color-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 8px 0;
        }

        .color-option {
            padding: 5px 8px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 3px;
            transition: all 0.15s;
            color: #333;
        }

        .color-option:hover {
            background: #f0f0f0;
        }

        /* More Colors Modal */
        .more-colors-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .more-colors-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
            width: 380px;
            max-width: 90%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .more-colors-content h3 {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .color-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .color-preview-box {
            width: 50px;
            height: 50px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            background: #000;
        }

        .color-values {
            flex: 1;
        }

        .color-values input {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #d0d0d0;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .color-slider {
            margin-bottom: 15px;
        }

        .color-slider label {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            color: #666;
            font-weight: 500;
        }

        .color-slider input {
            width: 100%;
            height: 4px;
            -webkit-appearance: none;
            background: #ddd;
            border-radius: 2px;
            outline: none;
        }

        .color-slider input::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #1e88e5;
            cursor: pointer;
        }

        .modal-buttons {
            margin-top: 20px;
            text-align: right;
        }

        .modal-buttons button {
            padding: 6px 16px;
            margin-left: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .modal-buttons .btn-primary {
            background: #1e88e5;
            color: white;
        }

        .modal-buttons .btn-primary:hover {
            background: #1565c0;
        }

        .modal-buttons .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .modal-buttons .btn-secondary:hover {
            background: #d0d0d0;
        }
    </style>
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
            TRANG ADMIN - ĐĂNG TIN TỨC MỚI
        </div>

        <form method="POST" action="/admin/main/news/store" enctype="multipart/form-data" id="newsForm">
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
                    <input type="hidden" name="slug_original" id="slug_original" >
                    <input class="input-slug" type="text" name="slug" id="slug" placeholder="[ Tự động tạo từ tiêu đề ]"
                         readonly>
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
                            value="<?php echo htmlspecialchars($formData['author'] ?? ''); ?>"
                            >
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
                                <button type="button" onclick="wrapText('insertUnorderedList')" title="Danh sách có dấu chấm ở đầu dòng"><i class="fa-solid fa-list"></i></button>
                                <button type="button" onclick="wrapText('insertOrderedList')" title="Danh sách có số ở đầu dòng">1.2.3</button>
                            </div>

                            <div class="tool-group">
                                <button type="button" onclick="wrapText('justifyLeft')" title="Căn trái"><i class="fa-solid fa-align-left"></i></button>
                                <button type="button" onclick="wrapText('justifyCenter')" title="Căn giữa"><i class="fa-solid fa-align-center"></i></button>
                                <button type="button" onclick="wrapText('justifyRight')" title="Căn phải"><i class="fa-solid fa-align-right"></i></button>
                                <button type="button" onclick="wrapText('justifyFull')" title="Căn đều"><i class="fa-solid fa-align-justify"></i></button>
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
                                        <div class="color-section">
                                            <div class="color-title">Theme Colors</div>
                                            <div class="color-grid theme-colors"></div>
                                        </div>
                                        <div class="color-section">
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
                                        <div class="color-section">
                                            <div class="color-title">Theme Colors</div>
                                            <div class="color-grid highlight-theme-colors"></div>
                                        </div>
                                        <div class="color-section">
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

                        <!-- Textarea duy nhất để soạn thảo nội dung -->
                        <textarea name="content" id="news_content" class="rich-editor-content" rows="20"
                            placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."><?php
                                                                                                                            echo htmlspecialchars($formData['content'] ?? '', ENT_QUOTES);
                                                                                                                            ?></textarea>

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

                        <div class="upload-box" id="uploadBox">
                            <span id="uploadIcon">📷</span>
                            <p id="uploadText">Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                            <small id="uploadInfo">Kích thước khuyến nghị: 1200x630px (Max 5MB)</small>
                        </div>
                        <input type="file" id="imageInput" name="featured_image" accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <div id="imagePreview" style="margin-top: 15px; display: none;">
                            <img id="previewImg" src="#" alt="Preview" style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                            <button type="button" onclick="removeImage()" style="display: block; margin-top: 8px; background: #fee2e2; color: #991b1b; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer;">✖️ Xóa ảnh</button>
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
</body>

</html>