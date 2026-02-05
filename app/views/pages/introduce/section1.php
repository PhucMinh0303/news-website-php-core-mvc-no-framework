<?php
/**
 * Section 1 - Hero Slider
 */
?>
<section class="section1 swiper hero-swiper1">
  <div class="swiper-wrapper1">
    <!-- Slide 1 -->
    <div class="swiper-slide1 hero-slide1">
      <div class="hero-container">
        <div class="hero-content">
          <h1>
            CHÀO MỪNG ĐẾN VỚI<br />
            <strong>Tài chính thông minh</strong>
          </h1>
          <p>
            Chúng tôi kiến tạo giải pháp quản lý tài sản đẳng cấp và chiến lược
            đầu tư khác biệt, được cá nhân hoá nhằm tối ưu giá trị và duy trì
            tăng trưởng bền vững cho Quý nhà đầu tư.
          </p>

          <div class="button-group">
            <button class="btn btn-green">
              <a href="<?php echo View::url('introduction'); ?>">
                TÌM HIỂU THÊM <i class="fa-solid fa-circle-arrow-right"></i>
              </a>
            </button>
            <button class="btn btn-blue">
              <a href="<?php echo View::url('contact'); ?>">
                LIÊN HỆ NGAY <i class="fa-solid fa-circle-arrow-right"></i>
              </a>
            </button>
          </div>
          <!-- Pagination -->
          <div class="swiper-pagination">
            <span class="swiper-pagination-bullet">1</span>
            <span class="swiper-pagination-bullet">2</span>
            <span class="swiper-pagination-bullet">3</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mask -->
  <div class="mask_bot_slide"></div>
</section>
<script>
window.APP_CONFIG = {
    BASE_URL: "<?= BASE_URL ?>",
    ASSET_URL: "<?= BASE_URL ?>public/assets/"
};
</script>
