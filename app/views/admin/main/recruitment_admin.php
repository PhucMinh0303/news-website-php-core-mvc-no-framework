<?php
/**
 * recruitment management view for admin panel
 */
?>
<main class="main">
  <div class="main-header">
    <h1>Recruitment</h1>
    <!-- Có thể thêm nút Create Article hoặc filter nếu cần -->
     <div class="filters">

      <!-- STATUS -->
      <select class="filter-select">
        <option>All Statuses</option>
        <option>Draft</option>
        <option>Published</option>
        <option>Archived</option>
        <option>Pending</option>
      </select>

      <!-- CATEGORY -->
      <select class="filter-select">
        <option>All Categories</option>
        <option>Politics</option>
        <option>Technology</option>
        <option>Health</option>
        <option>World</option>
        <option>Science</option>
        <option>Sports</option>
        <option>Economy</option>
      </select>

    </div>
  </div>
  <!-- Topbar -->
  <div class="topbar">
    <input type="text" placeholder="Search recruitment..." />
    <button class="btn-primary" data-page="addRecruitment">+ Post Job</button>
  </div>

  <div class="posts-table">
    <div class="table-header">
      <div class="col-title">TITLE</div>
      <div class="col-category">CATEGORY</div>
      <div class="col-status">STATUS</div>
      <div class="col-author">AUTHOR</div>
      <div class="col-actions">ACTIONS</div>
    </div>

    <div class="table-row">
      <div class="col-title title-box">
        <img src="thumb.jpg" class="thumb" />

        <div class="title-info">
          <div class="post-title">
            New Policy Reform Impacts Small Businesses Across the Nation
          </div>

          <div class="post-views">12.450 views</div>
        </div>
      </div>

      <div class="col-category">
        <span class="badge category">Economy</span>
      </div>

      <div class="col-status">
        <span class="badge status">
          <span class="dot"></span>
          Published
        </span>
      </div>

      <div class="col-author">Sarah Jenkins</div>

      <div class="col-actions">
        <button class="icon-btn"><i class="fa-solid fa-pen"></i></button>
        <button class="icon-btn"><i class="fa-solid fa-trash"></i></button>
      </div>
    </div>
  </div>
</main>
