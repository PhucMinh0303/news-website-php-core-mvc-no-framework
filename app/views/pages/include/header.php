<header class="header">
  <!-- LOGO -->
  <div class="logo">
    <a href="<?php echo View::url(''); ?>" class="logo-bg">
      <img
        src="<?php echo View::asset('img/section1/logo-capital-am-png-2025120910123384zwFTaMUk.png'); ?>"
        alt="<?php echo View::escape(SITE_NAME); ?>"
      />
    </a>
  </div>
  <div class="header-container">
    <!-- NAVIGATION -->
    <nav class="nav-menu">
      <ul>
        <li>
          <i class="fa-solid fa-house"></i>
        </li>

        <li>
          <a href="<?php echo View::url('introduction'); ?>">GIỚI THIỆU</a>
        </li>

        <li class="dropdown">
          <a href="#"
            >SẢN PHẨM & DỊCH VỤ<i class="fa-solid fa-angle-down"></i
          ></a>
          <ul class="dropdown-menu">
            <li><a href="<?= View::url(route: 'asset-management'); ?>">Quản lý tài sản</a></li>
            <li>
              <a href="<?php echo View::url('portfolio-management'); ?>">Quản lý danh mục đầu tư</a>
            </li>
            <li>
              <a href="<?php echo View::url('business-management'); ?>"
                >Tư vấn quản lý doanh nghiệp</a
              >
            </li>
            <li>
              <a href="<?php echo View::url('m&a-project'); ?>">Tư vấn dự án M&A</a>
            </li>
            <li>
              <a href="<?php echo View::url('m&a-restructuring'); ?>"
                >M&A và tái cấu trúc doanh nghiệp</a
              >
            </li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#"
            >QUAN HỆ NHÀ ĐẦU TƯ<i class="fa-solid fa-angle-down"></i
          ></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo View::url('financial-information'); ?>">Thông tin tài chính</a></li>
            <li><a href="<?php echo View::url('annual-report'); ?>">Báo cáo thường niên</a></li>
            <li><a href="<?php echo View::url('information-disclosure'); ?>">Công bố thông tin</a></li>
            <li><a href="<?php echo View::url('shareholder-information'); ?>">Thông tin cổ đông</a></li>
            <li><a href="<?php echo View::url('corporate-governance'); ?>">Quản trị doanh nghiệp</a></li>
          </ul>
        </li>

        <li><a href="<?php echo View::url('Recruitment'); ?>">TUYỂN DỤNG</a></li>
        <li><a href="<?php echo View::url('News'); ?>">TIN TỨC</a></li>
        <li><a href="<?php echo View::url('Contact'); ?>">LIÊN HỆ</a></li>
        <!-- SEARCH ICON -->
        <li></li>
      </ul>
    </nav>

    <!-- RIGHT ACTIONS -->
    <div class="header-right">
      <span class="icon_menu_mobile">
        <i class="fa-solid fa-bars"></i>
      </span>
      <!-- HOTLINE -->
      <div class="hotline">
        <i class="fa-solid fa-phone-volume"></i>
        <div>
          <span>BẠN CẦN TƯ VẤN</span><br />
          <strong>1900.888.988</strong>
        </div>
      </div>
      <span class="line-hea-r"> </span>
      <div class="search">
        <i id="search-icon" class="fa-solid fa-magnifying-glass"></i>
      </div>
      <div class="search-box" id="search-box">
        <input type="text" placeholder="Tìm kiếm..." />
        <button type="button" id="search-btn">Tìm kiếm</button>
      </div>
      <button class="btn btn-green">
        ĐĂNG NHẬP <i class="fa-solid fa-arrow-right-to-bracket"></i>
      </button>
    </div>
  </div>
</header>

<div class="menu_mobile">
  <span class="close_menu_mobile"></span>
  <div class="logo_mb">
    <a href="<?php echo View::url(''); ?>">
      <img
        src="<?php echo View::asset('img/section1/logo-capital-am-png-2025120910123384zwFTaMUk.png'); ?>"
        alt="<?php echo View::escape(SITE_NAME); ?>"
      />
    </a>
  </div>
  <div class="dkdn_mb">
    <a href="tel:1900888988" class="hotline_dkdn_mb">
      <p>Bạn cần tư vấn</p>
      <span>1900.888.988</span>
    </a>
  </div>
  <div class="menu_accordion">
    <ul class="ul_ma_1">
      <li>
        <a href="<?php echo View::url(''); ?>">Trang chủ</a>
      </li>
      <li>
        <a href="<?php echo View::url('introduction'); ?>">Giới thiệu</a>
      </li>
      <li class="">
        <a href="<?php echo View::url('asset-management'); ?>">Sản phẩm &amp; Dịch vụ</a>
        <i class="arrown_menu_accordion"></i>
        <ul class="ul_ma_2">
          <li>
            <a href="<?php echo View::url('asset-management'); ?>">Quản lý tài sản</a>
          </li>
          <li>
            <a href="<?php echo View::url('portfolio-management'); ?>">Quản lý danh mục đầu tư</a>
          </li>
          <li>
            <a href="<?php echo View::url('business-management'); ?>"
              >Tư vấn quản trị doanh nghiệp</a
            >
          </li>
          <li>
            <a href="<?php echo View::url('m&a-project'); ?>">Tư vấn dự án M&amp;A</a>
          </li>
          <li>
            <a href="<?php echo View::url('m&a-restructuring'); ?>"
              >M&amp;A và tái cấu trúc doanh nghiệp</a
            >
          </li>
        </ul>
        <!-- End .ul_ma_2 -->
      </li>
      <li>
        <a href="<?php echo View::url('investor-relations'); ?>">Quan hệ nhà đầu tư</a>
        <i class="arrown_menu_accordion"></i>
        <ul class="ul_ma_2">
          <li>
            <a href="<?php echo View::url(route: 'financial-information'); ?>">Thông tin tài chính</a>
          </li>
          <li>
            <a href="<?php echo View::url('annual-report'); ?>">Báo cáo thường niên</a>
          </li>
          <li>
            <a href="<?php echo View::url('information-disclosure'); ?>">Công bố thông tin</a>
          </li>
          <li>
            <a href="<?php echo View::url('shareholder-information'); ?>">Thông tin cổ đông</a>
          </li>
          <li>
            <a href="<?php echo View::url('corporate-governance'); ?>">Quản trị doanh nghiệp</a>
          </li>
        </ul>
        <!-- End .ul_ma_2 -->
      </li>
      <li>
        <a href="<?php echo View::url('recruitment'); ?>">Tuyển dụng</a>
      </li>
      <li>
        <a href="<?php echo View::url('news'); ?>">Tin tức</a>
      </li>
      <li>
        <a href="<?php echo View::url('contact'); ?>" title="Liên hệ">Liên hệ</a>
      </li>
    </ul>
    <!-- End .ul_ma_1 -->
  </div>
  <!-- End .menu_accordion -->
</div>
