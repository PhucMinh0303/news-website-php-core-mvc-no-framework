    <!-- Load scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?php echo View::asset('js/script.js'); ?>"></script>
    <script src="<?php echo View::asset('js/product-service.js'); ?>"></script>

    <?php
    if (isset($view_animation_data)) {
        echo View::js('animationData', $view_animation_data);
    }
    ?>
    
    
  </body>
</html>
