<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-main">
      <div class="footer-section footer-info">
        <div class="footer-logo">
          <img
            src="<?php echo View::asset('img/footer/logo-cas-png-20251209102030yGW1WOGlvr.png'); ?>"
            alt="<?php echo View::escape(SITE_NAME); ?>"
          />
        </div>
        <div class="footer-img">
          <img src="<?php echo View::asset('img/footer/footer-logo.png'); ?>" alt="<?php echo View::escape(SITE_NAME); ?>" />
        </div>
      </div>
      <div class="footer-section footer-subscribe">
        <h3>Đăng ký nhận bản tin từ chúng tôi!</h3>

        <div class="newsletter-form">
          <div class="input-group">
            <input type="email" placeholder="Nhập email của bạn" />
            <button>
              <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
          </div>
        </div>
        <div class="social-media">
          <h4>Mạng xã hội</h4>
          <div class="social-icons">
            <!-- Thêm icon mạng xã hội ở đây -->
            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-section footer-addresses">
        <h3>Hội sở</h3>
        <p>
          <i class="fas fa-building"></i> Hasco Building, 98 Xuân Thủy, Phường
          An Khánh, Tp. Hồ Chí Minh
        </p>

        <h3>Chi nhánh</h3>
        <p>
          <i class="fas fa-map-marker-alt"></i> Tầng 8, Toà nhà số 2A Đại Cố
          Việt, Phường Hai Bà Trưng, Tp. Hà Nội
        </p>
        <div class="contact-info">
          <p><i class="fas fa-envelope"></i> <a href="mailto:info@capitalam.vn">info@capitalam.vn</a></p>
          <p><i class="fas fa-phone-alt"></i> <a href="tel:1900888988">1900.888.988</a></p>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>Copyright © <?php echo date('Y'); ?> <?php echo View::escape(SITE_NAME); ?>, All Rights Reserved.</p>
      <div class="footer-links">
        <a href="<?php echo View::url('page/terms-of-service'); ?>">Điều khoản sử dụng</a>
        <a href="<?php echo View::url('page/privacy-policy'); ?>">Chính sách bảo mật</a>
      </div>
    </div>
  </div>
</footer>
