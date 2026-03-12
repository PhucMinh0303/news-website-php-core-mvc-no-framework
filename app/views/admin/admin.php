<?php
$page_title = "Admin Panel";
?>
<!---->
<?php include VIEWS_PATH . 'admin/menu/head-root-admin.php'; ?>

<div id="menu-container">
  <?php include VIEWS_PATH . 'admin/menu/menu.php'; ?>
</div>

<div id="main-container">
  <?php include VIEWS_PATH . 'admin/main/dashboard_admin.php'; ?>
</div>

<?php include VIEWS_PATH . 'admin/menu/scripts-root-admin.php'; ?>
