<?php
$page_title = "Admin Panel";
?>
<!---->
<?php include VIEWS_PATH . 'admin/menu/head-root-admin.php'; ?>

<script>
  window.ADMIN_INITIAL_PAGE = <?php echo json_encode($admin_page ?? 'dashboard'); ?>;
</script>

<div id="menu-container">
  <?php include VIEWS_PATH . 'admin/menu/menu.php'; ?>
</div>

<div id="main-container">
  <?php
  $admin_view = $admin_view ?? 'admin/main/dashboard_admin';
  include VIEWS_PATH . $admin_view . '.php';
  ?>
</div>

<?php include VIEWS_PATH . 'admin/menu/scripts-root-admin.php'; ?>