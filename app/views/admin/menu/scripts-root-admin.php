    <!-- Admin URLs (used by admin.js to load partials) -->
    <script>
      window.ADMIN_URLS = {
        menu: "<?php echo View::url('admin/menu'); ?>",
        main: "<?php echo View::url('admin/main'); ?>"
      };
    </script>

    <!-- Thêm thư viện JS (hoặc jQuery)-->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <!-- jQuery chính cho admin -->
    <script src="<?php echo View::asset('js/admin/contact-application_admin.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/recruitment-create.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/admin.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/news-create.js'); ?>"></script>




    </body>

    </html>