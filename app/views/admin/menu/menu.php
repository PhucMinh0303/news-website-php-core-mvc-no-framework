<?php

/**
 * Menu management view for admin panel
 */
?>

<aside class="sidebar">
    <div class="logo">
        <img
            src="<?php echo View::asset('img/footer/logo-cas-png-20251209102030yGW1WOGlvr.png'); ?>"
            alt="<?php echo View::escape(SITE_NAME); ?>" />
    </div>

    <ul class="menu-items">
        <li class="menu-item active" data-page="dashboard">Thống kê</li>
        <li class="menu-item" data-page="news">Tin tức</li>
        <li class="menu-item" data-page="recruitment">Tuyển dụng</li>
        <li class="menu-item" data-page="contact">Phản hồi</li>
        <li class="menu-item" data-page="application">Đơn ứng tuyển</li>
        <li class="menu-item" data-page="analytics">Analytics</li>
        <li class="menu-item" data-page="test-db">Test SQL</li>
    </ul>

    <div class="profile">
        <div class="avatar"></div>
        <div>
            <strong>Alex Editor</strong>
            <p>Chief Editor</p>
        </div>
    </div>
</aside>