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
    <div class="alert alert-success"
        style="background: #d1fae5; color: #065f46; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['success'];
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"
        style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
    <div class="alert alert-error"
        style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
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
            return ['text' => 'Open', 'class' => 'status-published'];
        case 0:
            return ['text' => 'Draft', 'class' => 'status-draft'];
        case 2:
            return ['text' => 'Closed', 'class' => 'status-archived'];
        default:
            return ['text' => 'Unknown', 'class' => 'status-unknown'];
    }
}

// Format deadline
function formatDeadline($deadline)
{
    if (empty($deadline))
        return 'N/A';
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
?>

<main class="main">
    <div class="main-header">
        <h1>Recruitment Management</h1>
        <div class="filters">
            <!-- STATUS FILTER -->
            <form method="GET" action="" id="filterForm" style="display: inline;">
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="1" <?php echo $status_filter == '1' ? 'selected' : ''; ?>>Open</option>
                    <option value="0" <?php echo $status_filter == '0' ? 'selected' : ''; ?>>Draft</option>
                    <option value="2" <?php echo $status_filter == '2' ? 'selected' : ''; ?>>Closed</option>
                </select>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_keyword); ?>">
                <input type="hidden" name="page" value="recruitment">
            </form>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <form method="GET" action="" style="display: flex; gap: 10px; flex: 1;">
            <input type="text" name="search" placeholder="Search by title or description..."
                value="<?php echo htmlspecialchars($search_keyword); ?>" />
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
            <input type="hidden" name="page" value="recruitment">
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
            <?php if (!empty($search_keyword) || $status_filter !== ''): ?>
                <a href="?page=recruitment" class="btn-secondary">Clear Filter</a>
            <?php endif; ?>
        </form>
        <button class="btn-primary" data-page="create-recruitment">
            <i class="fa-solid fa-plus"></i> Post New Job
        </button>
    </div>

    <!-- Hiển thị thông báo nếu không có dữ liệu -->
    <?php if (empty($recruitments)): ?>
        <div class="empty-state" style="text-align: center; padding: 50px; background: #f9fafb; border-radius: 8px;">
            <i class="fa-solid fa-briefcase" style="font-size: 48px; color: #9ca3af;"></i>
            <p style="margin-top: 16px; color: #6b7280;">No recruitment posts found.</p>
            <?php if (!empty($search_keyword) || $status_filter !== ''): ?>
                <a href="?page=recruitment" class="btn-primary" style="margin-top: 16px;">View All Jobs</a>
            <?php else: ?>
                <button class="btn-primary" data-page="create-recruitment" style="margin-top: 16px;">Create First Job Post
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="posts-table">
            <div class="table-header">
                <div class="col-title">TITLE & INFO</div>
                <div class="col-location">WORK LOCATION</div>
                <div class="col-degree">DEGREE</div>
                <div class="col-deadline">DEADLINE</div>
                <div class="col-status">STATUS</div>
                <div class="col-actions">ACTIONS</div>
            </div>

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
                ?>
                <div class="table-row">
                    <div class="col-title title-box">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="thumb"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                            onerror="this.src='assets/images/default-job.webp'" />

                        <div class="title-info">
                            <div class="post-title">
                                <a href="?page=edit-recruitment&id=<?php echo $job['id']; ?>"
                                    style="text-decoration: none; color: #1f2937; font-weight: 500;">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </div>
                            <div class="post-meta">
                                <span class="meta-item">
                                    <i class="fa-solid fa-users"></i> Qty: <?php echo $quantity; ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fa-solid fa-chart-simple"></i> Salary: <?php echo htmlspecialchars($salary_range); ?>
                                </span>
                                <?php if (!empty($job['slug'])): ?>
                                    <span class="meta-item">
                                        <i class="fa-solid fa-link"></i> slug: <?php echo htmlspecialchars($job['slug']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-location">
                        <i class="fa-solid fa-location-dot" style="color: #6b7280;"></i>
                        <?php
                        $location = $job['work_location'] ?? '';
                        echo htmlspecialchars(strlen($location) > 60 ? substr($location, 0, 60) . '...' : ($location ?: 'Not set'));
                        ?>
                    </div>

                    <div class="col-degree">
                        <i class="fa-solid fa-graduation-cap" style="color: #6b7280;"></i>
                        <?php echo htmlspecialchars($degree); ?>
                    </div>

                    <div class="col-deadline">
                        <span class="deadline <?php echo $deadline_info['is_expired'] ? 'expired' : ''; ?>">
                            <i class="fa-regular fa-calendar"></i> <?php echo $deadline_info['date']; ?>
                            <?php echo $deadline_info['badge']; ?>
                        </span>
                    </div>

                    <div class="col-status">
                        <?php $status_info = getStatusInfo($job['status']); ?>
                        <span class="badge status <?php echo $status_info['class']; ?>">
                            <span class="dot"></span>
                            <?php echo $status_info['text']; ?>
                        </span>
                    </div>

                    <div class="col-actions">
                        <button class="icon-btn" onclick="toggleStatus(<?php echo $job['id']; ?>, <?php echo $job['status']; ?>)"
                            title="<?php echo $job['status'] == 1 ? 'Close job' : 'Open job'; ?>">
                            <i class="fa-solid <?php echo $job['status'] == 1 ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                        </button>
                        <button class="icon-btn" onclick="editRecruitment(<?php echo $job['id']; ?>)"
                            title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="icon-btn" onclick="deleteRecruitment(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars($job['title']); ?>')"
                            title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <?php if ($page > 1): ?>
                    <a href="?page=recruitment&p=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>"
                        class="pagination-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?page=recruitment&p=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>"
                        class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=recruitment&p=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_keyword); ?>"
                        class="pagination-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 15px; text-align: center; color: #6b7280; font-size: 14px;">
            <i class="fa-regular fa-file-lines"></i> Total: <?php echo $total_records; ?> records
            <?php if ($status_filter !== ''): ?>
                <span style="margin-left: 10px;">
                    <a href="?page=recruitment" style="color: #3b82f6;">Show all</a>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    function editRecruitment(id) {
        window.location.href = '?page=edit-recruitment&id=' + id;
    }

    function deleteRecruitment(id, title) {
        if (confirm('Are you sure you want to delete "' + title + '"? This action cannot be undone.')) {
            window.location.href = '?page=delete-recruitment&id=' + id;
        }
    }

    function toggleStatus(id, currentStatus) {
        var action = currentStatus == 1 ? 'close' : 'open';
        if (confirm('Are you sure you want to ' + action + ' this recruitment?')) {
            window.location.href = '?page=toggle-recruitment-status&id=' + id;
        }
    }
</script>

<style>
    .status-published .dot {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    .status-draft .dot {
        background-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }

    .status-archived .dot {
        background-color: #6b7280;
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.2);
    }

    .deadline.expired {
        color: #ef4444;
    }

    .deadline {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .badge.expired {
        background: #fee2e2;
        color: #ef4444;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge.soon {
        background: #fed7aa;
        color: #f59e0b;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .pagination {
        margin: 20px 0;
        flex-wrap: wrap;
    }

    .pagination-link {
        padding: 8px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        text-decoration: none;
        color: #374151;
        transition: all 0.2s;
    }

    .pagination-link:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .pagination-link.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .btn-secondary {
        padding: 8px 16px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        color: #374151;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-primary {
        padding: 8px 20px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .icon-btn:hover {
        background: #f3f4f6;
    }

    /* Table Grid - 6 columns */
    .table-header,
    .table-row {
        display: grid;
        grid-template-columns: 3fr 2fr 1fr 1.2fr 0.8fr 0.6fr;
        gap: 12px;
        padding: 12px 16px;
        align-items: center;
    }

    .table-header {
        background: #f9fafb;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.5px;
    }

    .table-row {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .table-row:hover {
        background: #fafafa;
    }

    .title-box {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    .title-info {
        flex: 1;
    }

    .post-title {
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .post-meta {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: #6b7280;
    }

    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge.status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-published {
        background: #d1fae5;
        color: #065f46;
    }

    .status-draft {
        background: #fed7aa;
        color: #92400e;
    }

    .status-archived {
        background: #e5e7eb;
        color: #374151;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Responsive */
    @media (max-width: 1024px) {

        .table-header,
        .table-row {
            grid-template-columns: 2fr 1.5fr 1fr 1fr 0.7fr 0.5fr;
        }
    }

    @media (max-width: 768px) {
        .table-header {
            display: none;
        }

        .table-row {
            display: block;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .table-row>div {
            margin-bottom: 8px;
        }

        .col-degree,
        .col-location,
        .col-deadline,
        .col-status,
        .col-actions {
            padding-left: 62px;
        }

        .col-actions {
            margin-bottom: 0;
        }

        .topbar {
            flex-direction: column;
        }

        .topbar form {
            width: 100%;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
    }
</style>