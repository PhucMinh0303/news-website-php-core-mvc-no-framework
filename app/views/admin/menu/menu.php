<?php

/**
 * Menu management view for admin panel
 */
?>

<aside class="sidebar">
    <div class="logo">
        <img
            src="<?php echo View::asset('img/footer/logo-2-png-20260518145348BHDjhSZzcy.png'); ?>"
            alt="<?php echo View::escape(SITE_NAME); ?>" />
    </div>

    <ul class="menu-items">
        <li class="menu-item <?php echo (($admin_page ?? '') === 'dashboard') ? 'active' : ''; ?>" data-page="dashboard"><a href="<?php echo View::escape(View::url('admin/dashboard')); ?>"><i class="fa-solid fa-chart-line"></i><span>Thống kê</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'news') ? 'active' : ''; ?>" data-page="news"><a href="<?php echo View::escape(View::url('admin/news')); ?>"><i class="fa-solid fa-newspaper"></i><span>Tin tức</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'recruitment') ? 'active' : ''; ?>" data-page="recruitment"><a href="<?php echo View::escape(View::url('admin/recruitment')); ?>"><i class="fa-solid fa-briefcase"></i><span>Tuyển dụng</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'contact') ? 'active' : ''; ?>" data-page="contact"><a href="<?php echo View::escape(View::url('admin/contact')); ?>"><i class="fa-solid fa-inbox"></i><span>Phản hồi</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'application') ? 'active' : ''; ?>" data-page="application"><a href="<?php echo View::escape(View::url('admin/application')); ?>"><i class="fa-solid fa-file-signature"></i><span>Đơn ứng tuyển</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'analytics') ? 'active' : ''; ?>" data-page="analytics"><a href="<?php echo View::escape(View::url('admin/analytics')); ?>"><i class="fa-solid fa-chart-pie"></i><span>Analytics</span></a></li>
        <li class="menu-item <?php echo (($admin_page ?? '') === 'test-db') ? 'active' : ''; ?>" data-page="test-db"><a href="<?php echo View::escape(View::url('admin/test-db')); ?>"><i class="fa-solid fa-database"></i><span>Test SQL</span></a></li>
    </ul>

    <div class="profile">
        <div class="avatar"></div>
        <div>
            <strong>Alex Editor</strong>
            <p>Chief Editor</p>
        </div>
    </div>
</aside>