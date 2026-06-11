<?php

/**
 * recruitment management view for admin panel
 * Hiển thị danh sách tin tuyển dụng từ database
 */

// Dữ liệu được truyền từ controller
$recruitments = $data['recruitments'] ?? [];
$status_filter = $data['status_filter'] ?? '';
$search_keyword = $data['search_keyword'] ?? '';
$page = $data['page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$total_records = $data['total_records'] ?? 0;

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
// Map status sang text và class theo database (status: 1=published/open, 0=draft, 2=closed/archived)
function getStatusInfo($status)
{
    switch ((int)$status) {
        case 1:
            return ['text' => 'Published', 'class' => 'status-published', 'dot' => '#10b981'];
        case 0:
            return ['text' => 'Draft', 'class' => 'status-draft', 'dot' => '#f59e0b'];
        case 2:
            return ['text' => 'Archived', 'class' => 'status-archived', 'dot' => '#6b7280'];
        default:
            return ['text' => 'Unknown', 'class' => 'status-unknown', 'dot' => '#9ca3af'];
    }
}

// Format deadline
function formatDeadline($deadline)
{
    if (empty($deadline))
        return ['date' => 'N/A', 'badge' => '', 'is_expired' => false];
    $deadline_date = new DateTime($deadline);
    $now = new DateTime();
    $interval = $now->diff($deadline_date);
    $is_expired = $deadline_date < $now;

    $formatted_date = $deadline_date->format('d/m/Y');

    if ($is_expired) {
        return [
            'date' => $formatted_date,
            'badge' => '<span class="badge expired">Expired</span>',
            'is_expired' => true
        ];
    } elseif ($interval->days <= 7) {
        return [
            'date' => $formatted_date,
            'badge' => '<span class="badge soon">' . $interval->days . ' days left</span>',
            'is_expired' => false
        ];
    } else {
        return [
            'date' => $formatted_date,
            'badge' => '',
            'is_expired' => false
        ];
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
    <div class="main-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h1>Quản lý Tuyển dụng</h1>
        <button type="button" class="btn-primary" data-page="create-recruitment">
            <i class="fa-solid fa-plus"></i> Đăng tin mới
        </button>
    </div>

    <!-- Stats Cards - Bổ sung từ news_admin -->
    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-number"><?php echo $total_records; ?></div>
            <div class="stat-label">Tổng số tin</div>
        </div>
        <div class="stat-card published">
            <div class="stat-number">
                <?php
                $published_count = count(array_filter($recruitments, function ($job) {
                    return $job['status'] == 1;
                }));
                echo $published_count;
                ?>
            </div>
            <div class="stat-label">Đang tuyển</div>
        </div>
        <div class="stat-card draft">
            <div class="stat-number">
                <?php
                $draft_count = count(array_filter($recruitments, function ($job) {
                    return $job['status'] == 0;
                }));
                echo $draft_count;
                ?>
            </div>
            <div class="stat-label">Bản nháp</div>
        </div>
        <div class="stat-card archived">
            <div class="stat-number">
                <?php
                $archived_count = count(array_filter($recruitments, function ($job) {
                    return $job['status'] == 2;
                }));
                echo $archived_count;
                ?>
            </div>
            <div class="stat-label">Đã đóng</div>
        </div>
    </div>

    <!-- Filter Bar - Cải tiến từ news_admin -->
    <div class="filter-bar">
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm theo tiêu đề hoặc mô tả..."
                value="<?php echo htmlspecialchars($search_keyword); ?>" />
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
            <input type="hidden" name="page" value="recruitment">
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
        </form>

        <select class="filter-select" onchange="window.location.href=this.value">
            <option value="?page=recruitment&p=1<?php echo $search_keyword ? '&search=' . urlencode($search_keyword) : ''; ?>">Tất cả trạng thái</option>
            <option value="?page=recruitment&status=1&p=1<?php echo $search_keyword ? '&search=' . urlencode($search_keyword) : ''; ?>" <?php echo $status_filter == '1' ? 'selected' : ''; ?>>Đang tuyển</option>
            <option value="?page=recruitment&status=0&p=1<?php echo $search_keyword ? '&search=' . urlencode($search_keyword) : ''; ?>" <?php echo $status_filter == '0' ? 'selected' : ''; ?>>Bản nháp</option>
            <option value="?page=recruitment&status=2&p=1<?php echo $search_keyword ? '&search=' . urlencode($search_keyword) : ''; ?>" <?php echo $status_filter == '2' ? 'selected' : ''; ?>>Đã đóng</option>
        </select>

        <?php if (!empty($search_keyword) || $status_filter !== ''): ?>
            <a href="?page=recruitment&p=1" class="btn-secondary">Xóa bộ lọc</a>
        <?php endif; ?>
    </div>
    <div class="posts-table">
        <div class="table-header">
            <div>TIÊU ĐỀ</div>
            <div>ĐỊA ĐIỂM</div>
            <div>YÊU CẦU</div>
            <div>HẠN NỘP</div>
            <div>TRẠNG THÁI</div>
            <div>THAO TÁC</div>

        </div>
        <!-- Hiển thị thông báo nếu không có dữ liệu -->
        <?php if (empty($recruitments)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-briefcase" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>Chưa có tin tuyển dụng nào.</p>
                <?php if (!empty($search_keyword) || $status_filter !== ''): ?>
                    <a href="?page=recruitment&p=1" class="btn-primary" style="margin-top: 10px;">Xem tất cả tin</a>
                <?php else: ?>
                    <button class="btn-primary" data-page="create-recruitment" style="margin-top: 10px;">Tạo tin đầu tiên</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($recruitments as $job): ?>
                <?php
                // Xác định đường dẫn ảnh đúng
                $image_path = 'assets/images/default-job.webp';
                if (!empty($job['image']) && $job['image'] != 'default-job.webp') {
                    $image_path = 'uploads/recruitments/' . $job['image'];
                }

                $deadline_info = formatDeadline($job['deadline']);
                $quantity = (int)($job['quantity'] ?? 1);
                $degree = $job['degree'] ?? 'Cao Đẳng - Đại Học';
                $salary_range = $job['salary_range'] ?? 'Thỏa thuận';
                $statusInfo = getStatusInfo($job['status']);
                ?>
                <div class="table-row">
                    <div class="col-title">
                        <?php if ($image_path && $image_path != 'assets/images/default-job.webp'): ?>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" class="post-thumb" alt="Thumbnail" onerror="this.src='assets/images/default-job.webp'">
                        <?php else: ?>
                            <div class="post-thumb" style="background: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-image" style="color: #9ca3af;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="title-info">
                            <div class="post-title">
                                <a href="/recruitment/<?php echo htmlspecialchars($job['slug'] ?? $job['id']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </div>
                            <div class="post-meta">
                                <span class="meta-item">
                                    <i class="fa-solid fa-users"></i> SL: <?php echo $quantity; ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fa-solid fa-chart-simple"></i> Lương: <?php echo htmlspecialchars($salary_range); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fa-regular fa-calendar"></i> Đăng: <?php echo formatDate($job['created_at'] ?? $job['publish_date'] ?? ''); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-location">
                        <span class="location-badge">
                            <i class="fa-solid fa-location-dot"></i>
                            <?php
                            $location = $job['work_location'] ?? '';
                            echo htmlspecialchars(strlen($location) > 60 ? substr($location, 0, 60) . '...' : ($location ?: 'Chưa đặt'));
                            ?>
                        </span>
                    </div>

                    <div class="col-degree">
                        <i class="fa-solid fa-graduation-cap" style="color: #6b7280;"></i>
                        <?php echo htmlspecialchars($degree); ?>
                    </div>

                    <div class="col-deadline">
                        <div class="deadline <?php echo $deadline_info['is_expired'] ? 'expired' : ''; ?>">
                            <i class="fa-regular fa-calendar"></i> <?php echo $deadline_info['date']; ?>
                            <?php echo $deadline_info['badge']; ?>
                        </div>
                    </div>

                    <div class="col-status">
                        <span class="status-badge <?php echo $statusInfo['class']; ?>">
                            <span class="dot" style="background: <?php echo $statusInfo['dot']; ?>"></span>
                            <?php echo $statusInfo['text']; ?>
                        </span>
                    </div>

                    <div class="col-actions">
                        <div class="action-buttons">
                            <a href="/admin/main/recruitment/edit/<?php echo $job['id']; ?>" class="icon-btn edit" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="/admin/main/recruitment/toggle-status/<?php echo $job['id']; ?>" class="icon-btn status" title="Đổi trạng thái" onclick="return confirm('Bạn có chắc muốn đổi trạng thái tin này?')">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </a>
                            <a href="/admin/main/recruitment/delete/<?php echo $job['id']; ?>" class="icon-btn delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa tin này? Hành động này không thể hoàn tác.')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>



            <div style="margin-top: 16px; text-align: center; color: #6b7280; font-size: 13px;">
                <i class="fa-regular fa-file-lines"></i> Hiển thị <?php echo count($recruitments); ?> / <?php echo $total_records; ?> tin tuyển dụng
                <?php if ($status_filter !== ''): ?>
                    <span style="margin-left: 10px;">
                        <a href="?page=recruitment" style="color: #3b82f6;">Xem tất cả</a>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=recruitment&p=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="?page=recruitment&p=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>"
                        class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=recruitment&p=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="margin-top: 16px; text-align: center; color: #6b7280; font-size: 13px;">
        Hiển thị <?php echo count($recruitments); ?> / <?php echo $total_records; ?> bài viết
    </div>

</main>

<script>
    // Hàm hỗ trợ cho các action (giữ lại từ original)
    function editRecruitment(id) {
        window.location.href = '/admin/main/recruitment/edit/' + id;
    }

    function deleteRecruitment(id, title) {
        if (confirm('Bạn có chắc muốn xóa tin "' + title + '"? Hành động này không thể hoàn tác.')) {
            window.location.href = '/admin/main/recruitment/delete/' + id;
        }
    }

    function toggleStatus(id, currentStatus) {
        var action = currentStatus == 1 ? 'đóng' : 'mở';
        if (confirm('Bạn có chắc muốn ' + action + ' tin tuyển dụng này?')) {
            window.location.href = '/admin/main/recruitment/toggle-status/' + id;
        }
    }

    // Xử lý sự kiện cho các button có data-page
    document.querySelectorAll('[data-page]').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = this.getAttribute('data-page');
            if (page === 'create-recruitment') {
                window.location.href = '/admin/main/recruitment/create';
            }
        });
    });
</script>