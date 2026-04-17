<?php
/**
 * add articles management view for admin panel
 */
?>

<main class="main">

    <!-- ================== PAGE 2 : ADD ARTICLE ================== -->

    <!-- HEADER -->
    <div class="add-header">
        TRANG ADMIN - ĐĂNG BÀI VIẾT
    </div>

    <div class="add-container">

        <!-- TITLE -->
        <div class="form-group">
            <div class="label-row">
                <label>Tiêu đề bài viết:</label>
                <span class="ai-btn">✨ Gợi ý bằng AI</span>
            </div>
            <input class="input-title" type="text" placeholder="[ Nhập tiêu đề bài viết tại đây... ]">
        </div>

        <!-- SLUG (auto-generated from title) -->
        <div class="form-group">
            <label>Slug (URL):</label>
            <input class="input-slug" type="text" placeholder="[ Tự động tạo từ tiêu đề ]" readonly>
            <small>(Slug sẽ được tạo tự động từ tiêu đề)</small>
        </div>

        <!-- CATEGORY (liên kết với bảng categories) -->
        <div class="form-group">
            <label>Chuyên mục:</label>
            <select class="input-category">
                <option value="">-- Chọn chuyên mục --</option>
                <option value="1">Chính trị</option>
                <option value="2">Công nghệ</option>
                <option value="3">Sức khỏe</option>
                <option value="4">Thế giới</option>
                <option value="5">Khoa học</option>
                <option value="6">Thể thao</option>
                <option value="7">Kinh tế</option>
            </select>
            <small>(Liên kết với bảng categories)</small>
        </div>

        <!-- AUTHOR (liên kết với bảng authors) -->
        <div class="form-group">
            <label>Tác giả:</label>
            <select class="input-author-id">
                <option value="">-- Chọn tác giả --</option>
                <option value="1">Nguyễn Văn A</option>
                <option value="2">Trần Thị B</option>
                <option value="3">Lê Văn C</option>
            </select>
            <small>(Liên kết với bảng authors)</small>
        </div>

        <!-- AUTHOR NAME (tên hiển thị) -->
        <div class="form-group">
            <label>Tên tác giả hiển thị:</label>
            <input class="input-author" type="text" placeholder="[ Tên tác giả hiển thị trên bài viết ]">
            <small>(Tên sẽ được lưu trực tiếp vào cột author)</small>
        </div>

        <!-- PUBLISH DATE -->
        <div class="form-group">
            <label>Ngày xuất bản:</label>
            <input class="input-publish-date" type="date" value="<?php echo date('Y-m-d'); ?>">
        </div>

        <!-- CONTENT (full editor) -->
        <div class="form-group">
            <label>Nội dung bài viết (hỗ trợ chèn hình ảnh vào giữa văn bản):</label>
            <div class="editor-box">
                <div class="editor-toolbar">
                    <!-- Font -->
                    <select id="fontFamily">
                        <option value="serif">Font: Serif</option>
                        <option value="arial">Arial</option>
                        <option value="times-new-roman">Times New Roman</option>
                        <option value="courier-new">Courier New</option>
                        <option value="roboto">Roboto</option>
                        <option value="monospace">Monospace</option>
                    </select>
                    <span class="divider"></span>
                    <!-- TEXT STYLE -->
                    <button data-cmd="bold"><b>B</b></button>
                    <button data-cmd="italic"><i>I</i></button>
                    <button data-cmd="underline"><u>U</u></button>
                    <span class="divider"></span>
                    <!-- COLOR -->
                    <input type="color" id="textColor">
                    <span class="divider"></span>
                    <!-- HEADINGS -->
                    <button data-heading="1">H1</button>
                    <button data-heading="2">H2</button>
                    <span class="divider"></span>
                    <!-- QUOTE -->
                    <button data-cmd="formatBlock" data-value="blockquote">❝</button>
                    <!-- LINK -->
                    <button id="addLink"><i class="fas fa-link"></i></button>
                    <span class="divider"></span>
                    <!-- IMAGE -->
                    <button id="insertImage"><i class="fas fa-image"></i> Chèn hình ảnh</button>
                    <!-- VIDEO -->
                    <button id="insertVideo"><i class="fa-solid fa-video"></i> Chèn video</button>
                </div>
                <div id="editor" class="editor-area" contenteditable="true"
                     placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."></div>
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
                <select class="input-status">
                    <option value="draft">Bản nháp (Draft)</option>
                    <option value="published" selected>Đã đăng (Published)</option>
                    <option value="archived">Lưu trữ (Archived)</option>
                </select>
                <small>(draft, published, archived)</small>

                <label>LƯỢT XEM (VIEWS)</label>
                <input class="input-views" type="number" value="0" placeholder="Số lượt xem">
                <small>(Sẽ tự động cập nhật khi có người xem)</small>
            </div>

            <!-- RIGHT -->
            <div class="thumbnail-box">
                <div class="thumb-header">
                    ẢNH ĐẠI DIỆN BÀI VIẾT
                    <span class="ai-btn">✨ Tạo bằng AI</span>
                </div>
                <div class="upload-box">
                    <span>📷</span>
                    <p>Tải lên ảnh đại diện (JPG, PNG, WEBP)</p>
                    <small>Định dạng: JPG, PNG, WEBP</small>
                </div>
            </div>

        </div>

        <!-- ACTION BUTTON -->
        <div class="action-buttons">
            <button class="btn-draft">Lưu nháp</button>
            <button class="btn-publish">Đăng bài</button>
        </div>

        <div class="cancel-text">Hủy bỏ và quay lại</div>

    </div>

</main>