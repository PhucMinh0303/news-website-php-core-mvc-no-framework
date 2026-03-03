<?php
/**
 * articles management view for admin panel
 */
?>
<main class="main">
  <div class="main-header">
    <h1>Articles</h1>
    <!-- Có thể thêm nút Create Article hoặc filter nếu cần -->
  </div>

  <div class="articles-list">
    <!-- Một article card -->
    <div class="article-card">
      <div class="article-header">
        <h2 class="article-title">
          New Policy Reform Impacts Small Businesses Across the Nation
        </h2>
        <div class="article-views">12,450 views</div>
      </div>

      <div class="article-meta-grid">
        <div class="meta-item">
          <span class="meta-label">CATEGORY</span>
          <span class="meta-value">Economy</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">STATUS</span>
          <span class="meta-value status-published">Published</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">AUTHOR</span>
          <span class="meta-value">Sarah Jenkins</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">ACTIONS</span>
          <div class="action-icons">
            <i class="fas fa-folder"></i>
            <!-- icon thư mục -->
            <i class="fas fa-file-alt"></i>
            <!-- icon tài liệu -->
          </div>
        </div>
      </div>
    </div>

    <!-- Có thể thêm nhiều article card khác nếu cần -->
  </div>
</main>
