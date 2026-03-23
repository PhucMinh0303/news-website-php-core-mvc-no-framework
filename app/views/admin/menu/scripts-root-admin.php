    <!-- Admin URLs (used by admin.js to load partials) -->
    <script>
      window.ADMIN_URLS = {
        menu: "<?php echo View::url('admin/menu'); ?>",
        main: "<?php echo View::url('admin/main'); ?>"
      };
    </script>

    <!-- Load scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="<?php echo View::asset('js/admin.js'); ?>"></script>
    

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
    </script>
  </body>
</html>
