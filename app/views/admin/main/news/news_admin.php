<?php

/**
 * Articles management view for admin panel
 */
// Dữ liệu được truyền từ controller
$stats = $stats ?? ['published' => 0, 'draft' => 0, 'archived' => 0, 'total' => 0];
$articles = $articles ?? [];
$status_filter = $status_filter ?? '';
$search_keyword = $search_keyword ?? '';
$page = $current_page ?? 1;
$total_pages = $total_pages ?? 1;
$total_records = $total_records ?? 0;

// Hiển thị thông báo
if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['success'];
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
    <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php foreach ($_SESSION['errors'] as $err): ?>
            <div><?php echo $err; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<?php
// Hàm lấy thông tin status
function getStatusInfo($status)
{
    switch ($status) {
        case 'published':
            return ['text' => 'Published', 'class' => 'status-published', 'dot' => '#10b981'];
        case 'draft':
            return ['text' => 'Draft', 'class' => 'status-draft', 'dot' => '#f59e0b'];
        case 'archived':
            return ['text' => 'Archived', 'class' => 'status-archived', 'dot' => '#6b7280'];
        default:
            return ['text' => 'Unknown', 'class' => 'status-unknown', 'dot' => '#9ca3af'];
    }
}

// Format ngày
function formatDate($date)
{
    if (empty($date)) return 'N/A';
    $datetime = new DateTime($date);
    return $datetime->format('d/m/Y');
}
?>



<main class="main">
    <div class="main-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Quản lý Bài viết</h1>
        <button type="button" class="btn-primary" data-page="create-news"
            data-url="<?php echo View::escape(View::url('admin/news/create')); ?>"
            data-main-url="<?php echo View::escape(View::url('admin/news/create')); ?>"
            data-menu-page="news">
            <i class="fa-solid fa-plus"></i> Viết bài mới
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
            <div class="stat-label">Tổng số bài viết</div>
        </div>
        <div class="stat-card published">
            <div class="stat-number"><?php echo $stats['published'] ?? 0; ?></div>
            <div class="stat-label">Đã đăng</div>
        </div>
        <div class="stat-card draft">
            <div class="stat-number"><?php echo $stats['draft'] ?? 0; ?></div>
            <div class="stat-label">Bản nháp</div>
        </div>
        <div class="stat-card archived">
            <div class="stat-number"><?php echo $stats['archived'] ?? 0; ?></div>
            <div class="stat-label">Lưu trữ</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm theo tiêu đề hoặc tác giả..."
                value="<?php echo htmlspecialchars($search_keyword); ?>" />
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
        </form>

        <select class="filter-select" onchange="window.location.href=this.value">
            <option value="?p=1<?php echo $search_keyword ? '&search=' . $search_keyword : ''; ?>">Tất cả trạng thái</option>
            <option value="?status=published&p=1<?php echo $search_keyword ? '&search=' . $search_keyword : ''; ?>" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Đã đăng</option>
            <option value="?status=draft&p=1<?php echo $search_keyword ? '&search=' . $search_keyword : ''; ?>" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
            <option value="?status=archived&p=1<?php echo $search_keyword ? '&search=' . $search_keyword : ''; ?>" <?php echo $status_filter == 'archived' ? 'selected' : ''; ?>>Lưu trữ</option>
        </select>

        <?php if (!empty($search_keyword) || !empty($status_filter)): ?>
            <a href="?p=1" class="btn-secondary">Xóa bộ lọc</a>
        <?php endif; ?>
    </div>

    <!-- Articles Table -->
    <div class="posts-table">
        <div class="table-header">
            <div>TIÊU ĐỀ</div>
            <div>DANH MỤC</div>
            <div>TRẠNG THÁI</div>
            <div>THAO TÁC</div>
        </div>
        <!-- Hiển thị thông báo nếu không có dữ liệu -->
        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-newspaper" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>Chưa có bài viết nào.</p>
                <button type="button" class="btn-primary" data-page="create-news"
                    data-url="<?php echo View::escape(View::url('admin/news/create')); ?>"
                    data-main-url="<?php echo View::escape(View::url('admin/news/create')); ?>"
                    data-menu-page="news">
                    <i class="fa-solid fa-plus"></i> Viết bài mới
                </button>
            </div>
            <!-- Hiển thị nếu có dữ liệu -->
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <?php $statusInfo = getStatusInfo($article['status']); ?>
                <?php $editUrl = !empty($article['slug'])
                    ? View::url('admin/news', [$article['slug']])
                    : View::url('admin/news/edit', [$article['id']]); ?>
                <?php
                $image = $article['featured_image'] ?? $article['image'] ?? '';
                $imageUrl = $image;
                if ($image !== '' && !preg_match('#^(https?:)?//#i', $image) && $image[0] !== '/') {
                    $imageUrl = strpos($image, '/') === false
                        ? BASE_URL . 'public/upload/news/' . rawurlencode($image)
                        : BASE_URL . 'public/assets/news/' . ltrim($image, '/');
                }
                ?>
                <div class="table-row">
                    <!-- Cột TIÊU ĐỀ -->
                    <div class="col-title">
                        <?php if ($imageUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" class="post-thumb" alt="Thumbnail">
                        <?php else: ?>
                            <div class="post-thumb" style="background: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-image" style="color: #9ca3af;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="title-info">
                            <div class="post-title">
                                <a href="<?php echo $editUrl; ?>" style="color: #1f2937; text-decoration: none;">
                                    <?php echo htmlspecialchars($article['title'] ?? 'Không có tiêu đề'); ?>
                                </a>
                            </div>
                            <div class="post-meta">
                                <span><i class="fa-regular fa-calendar"></i> <?php echo formatDate($article['publish_date'] ?? ''); ?></span>
                                <span><i class="fa-regular fa-eye"></i> <?php echo number_format($article['views'] ?? 0); ?> lượt xem</span>
                                <?php if (!empty($article['slug'])): ?>
                                    <span><i class="fa-regular fa-link"></i> /<?php echo htmlspecialchars($article['slug']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Cột DANH MỤC -->
                    <div class="col-category">
                        <span class="category-badge">
                            <?php echo htmlspecialchars($article['category_name'] ?? 'Chưa phân loại'); ?>
                        </span>
                    </div>

                    <!-- Cột TRẠNG THÁI -->
                    <div class="col-status">
                        <span class="status-badge <?php echo $statusInfo['class']; ?>">
                            <span class="dot" style="background: <?php echo $statusInfo['dot']; ?>"></span>
                            <?php echo $statusInfo['text']; ?>
                        </span>
                        <!-- Hiển thị thêm thông tin trạng thái chi tiết -->
                        <?php if ($article['status'] === 'draft'): ?>
                            <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Bản nháp</div>
                        <?php elseif ($article['status'] === 'published'): ?>
                            <div style="font-size: 10px; color: #10b981; margin-top: 2px;">Đã xuất bản</div>
                        <?php elseif ($article['status'] === 'archived'): ?>
                            <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">Đã lưu trữ</div>
                        <?php endif; ?>
                    </div>



                    <!-- Cột THAO TÁC -->
                    <div class="col-actions">
                        <div class="action-buttons">
                            <a href="<?php echo $editUrl; ?>" class="icon-btn edit" title="Sửa bài viết">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="<?php echo View::url('admin/news/toggle-status', [$article['id']]); ?>" class="icon-btn status" title="Đổi trạng thái" onclick="return confirm('Bạn có chắc muốn đổi trạng thái bài viết này?')">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </a>
                            <a href="<?php echo View::url('admin/news/delete', [$article['id']]); ?>" class="icon-btn delete" title="Xóa bài viết" onclick="return confirm('Bạn có chắc muốn xóa bài viết này? Hành động này không thể hoàn tác.')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?p=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end = min($total_pages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="?p=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>"
                    class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?p=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 16px; text-align: center; color: #6b7280; font-size: 13px;">
        Hiển thị <?php echo count($articles); ?> / <?php echo $total_records; ?> bài viết
    </div>
</main>