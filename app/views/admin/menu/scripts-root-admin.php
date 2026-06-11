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
    <script src="<?php echo View::asset('js/admin/contact-application_admin.js'); ?>"></script>

    <script src="<?php echo View::asset('js/admin/recruitment-create.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/admin.js'); ?>"></script>



    </body>

    </html>