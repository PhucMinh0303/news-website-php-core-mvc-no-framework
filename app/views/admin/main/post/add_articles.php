<?php
/**
 * add articles management view for admin panel
 */
?>

<main class="main">
  
  <!-- ================== PAGE 2 : ADD ARTICLE ================== -->
  

  <!-- HEADER -->
  <div class="add-header">
    TRANG ADMIN - ĐĂNG BÀI
  </div>

  <div class="add-container">

    <!-- TITLE -->
    <div class="form-group">
      <div class="label-row">
        <label>Tiêu đề:</label>
        <span class="ai-btn">✨ Gợi ý bằng AI</span>
      </div>

      <input class="input-title"
        type="text"
        placeholder="[ Nhập tiêu đề bài báo tại đây... ]">
    </div>


    <!-- SUMMARY -->
    <div class="form-group">

      <div class="label-row">
        <label>Tóm tắt:</label>
        <span class="ai-btn">✨ Tóm tắt bằng AI</span>
      </div>

      <textarea class="input-summary"
        placeholder="[ Nhập tóm tắt nội dung... ] (có thể kéo dài nhiều dòng)">
      </textarea>

    </div>


    <!-- CONTENT -->
    <div class="form-group">

      <label>Nội dung bài viết (hỗ trợ chèn hình ảnh vào giữa văn bản):</label>

      <div class="editor-box">

        <div class="editor-toolbar">

          <!-- Font -->
          <select id="fontFamily">
            <option value="serif">Font: Serif</option>
            <option value="Arial">Arial</option>
            <option value="Times New Roman">Times New Roman</option>
            <option value="Courier New">Courier New</option>
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
          <button data-heading="h1">H1</button>
          <button data-heading="h2">H2</button>

          <span class="divider"></span>

          <!-- QUOTE -->
          <button data-cmd="formatBlock" data-value="blockquote">❝</button>

          <!-- LINK -->
          <button id="addLink">🔗</button>

          <span class="divider"></span>

          <!-- IMAGE -->
          <button id="insertImage">🖼 Chèn hình ảnh</button>

          <!-- VIDEO -->
          <button id="insertVideo">🎥 Chèn video</button>

        </div>

        <textarea id="editor" class="editor-area" contenteditable="true"
        placeholder="Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác..."></textarea>

      </div>
      <input type="file" id="imageUpload" accept="image/*" hidden>

    </div>


    <!-- TAG -->
    <div class="form-group">

      <label>Tags:</label>

      <input class="input-tags"
        type="text"
        placeholder="[ công nghệ, giáo dục, đời sống ... ]">

      <small>(Nhập các tag cách nhau bằng dấu phẩy hoặc chọn từ danh sách)</small>

    </div>


    <!-- BOTTOM LAYOUT -->
    <div class="bottom-layout">

      <!-- LEFT -->
      <div class="publish-settings">

        <h3>CẤU HÌNH XUẤT BẢN</h3>

        <label>CHUYÊN MỤC</label>
        <select>
          <option>Politics</option>
          <option>Technology</option>
          <option>Health</option>
        </select>

        <label>TRẠNG THÁI</label>
        <select>
          <option>Bản nháp</option>
          <option>Đã đăng</option>
        </select>

        <label>TÁC GIẢ</label>
        <input type="text" value="Alex Editor">

      </div>


      <!-- RIGHT -->
      <div class="thumbnail-box">

        <div class="thumb-header">
          ẢNH ĐẠI DIỆN
          <span class="ai-btn">✨ Tạo bằng AI</span>
        </div>

        <div class="upload-box">
          <span>📷</span>
          <p>Tải lên hoặc dùng AI</p>
        </div>

      </div>

    </div>


    <!-- ACTION BUTTON -->
    <div class="action-buttons">

      <button class="btn-draft">
        Lưu nháp
      </button>

      <button class="btn-publish">
        Đăng bài
      </button>

    </div>

    <div class="cancel-text">
      Hủy bỏ và quay lại
    </div>

  </div>

</div>

</main>