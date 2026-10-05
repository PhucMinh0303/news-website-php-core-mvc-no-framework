    <!-- Admin URLs (used by admin.js to load partials) -->
    <script>
      window.ADMIN_URLS = {
        menu: "<?php echo View::url('admin/menu'); ?>",
        main: "<?php echo View::url('admin/main'); ?>"
      };
    </script>

    <!-- Thêm thư viện JS (hoặc jQuery)-->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- jQuery chính cho admin -->
    <script src="<?php echo View::asset('js/admin/contact-admin.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/recruitment-create.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/admin.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/slug.js'); ?>"></script>
    <script src="<?php echo View::asset('js/admin/news-create.js'); ?>"></script>

    <script>
      $(function() {
        var $message = $('#recruitment-success-message');
        if (!$message.length) {
          return;
        }

        var $toast = $('<div>', {
          text: $message.data('message'),
          css: {
            position: 'fixed',
            top: '24px',
            right: '24px',
            zIndex: 9999,
            padding: '14px 20px',
            borderRadius: '8px',
            background: '#16a34a',
            color: '#fff',
            boxShadow: '0 8px 24px rgba(0, 0, 0, .18)',
            fontWeight: '600'
          }
        }).hide().appendTo('body');

        $toast.fadeIn(250).delay(3000).fadeOut(400, function() {
          $(this).remove();
        });
      });
    </script>

    </body>

    </html>