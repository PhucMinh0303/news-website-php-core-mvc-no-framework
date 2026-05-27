    <!-- Admin URLs (used by admin.js to load partials) -->
    <script>
      window.ADMIN_URLS = {
        menu: "<?php echo View::url('admin/menu'); ?>",
        main: "<?php echo View::url('admin/main'); ?>"
      };
    </script>

    <!-- Load scripts -->
    <!-- Thêm jQuery 3.7.1 và file JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="<?php echo View::asset('js/admin/recruitment-form.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/admin.js'); ?>"></script>


    <?php
    if (isset($view_animation_data)) {
      echo View::js('animationData', $view_animation_data);
    }
    ?>
    <script>
      if (window.animationData) {
        console.log('Animation data from PHP:', window.animationData);
        // Example of using the data:
        // const element = document.querySelector(window.animationData.element);
        // if (element) {
        //   // Apply animation using a library or custom CSS
        // }
      }
      // Khởi tạo form khi document ready
      $(document).ready(function() {
        if (typeof recruitmentForm !== 'undefined') {
          recruitmentForm.init();
        }
      });
    </script>
    </body>

    </html>