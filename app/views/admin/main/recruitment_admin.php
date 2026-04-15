<?php
/**
 * recruitment management view for admin panel
 * Hiển thị danh sách tin tuyển dụng từ database
 */

// Dữ liệu được truyền từ controller
$recruitments = $data['recruitments'] ?? [];
$status_filter = $data['status_filter'] ?? null;
$search_keyword = $data['search_keyword'] ?? '';
$page = $data['page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$total_records = $data['total_records'] ?? 0;

// Hiển thị thông báo
if (isset($_SESSION['admin_success'])): ?>
    <div class="alert alert-success"
         style="background: #d1fae5; color: #065f46; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['admin_success'];
        unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="alert alert-error"
         style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
        <?php echo $_SESSION['admin_error'];
        unset($_SESSION['admin_error']); ?>
    </div>
<?php endif; ?>

<?php
// Map status sang text và class
function getStatusInfo($status)
{
    switch ((int)$status) {
        case 1:
            return ['text' => 'Published', 'class' => 'status-published'];
        case 0:
            return ['text' => 'Draft', 'class' => 'status-draft'];
        case 2:
            return ['text' => 'Archived', 'class' => 'status-archived'];
        default:
            return ['text' => 'Unknown', 'class' => 'status-unknown'];
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
                    <option value="1" <?php echo $status_filter === 1 ? 'selected' : ''; ?>>Published</option>
                    <option value="0" <?php echo $status_filter === 0 ? 'selected' : ''; ?>>Draft</option>
                    <option value="2" <?php echo $status_filter === 2 ? 'selected' : ''; ?>>Archived</option>
                </select>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_keyword); ?>">
                <input type="hidden" name="p" value="<?php echo $page; ?>">
            </form>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <form method="GET" action="" style="display: flex; gap: 10px; flex: 1;">
            <input type="text" name="search" placeholder="Search recruitment..."
                   value="<?php echo htmlspecialchars($search_keyword); ?>"/>
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
            <button type="submit" class="btn-secondary">Search</button>
            <?php if (!empty($search_keyword) || $status_filter !== null): ?>
                <a href="?page=recruitment" class="btn-secondary">Clear Filter</a>
            <?php endif; ?>
        </form>
        <button class="btn-primary" data-page="add-recruitment">+ Post Job</button>
    </div>

    <!-- Hiển thị thông báo nếu không có dữ liệu -->
    <?php if (empty($recruitments)): ?>
        <div class="empty-state" style="text-align: center; padding: 50px;">
            <p>No recruitment posts found.</p>
        </div>
    <?php else: ?>
        <div class="posts-table">
            <div class="table-header">
                <div class="col-title">TITLE</div>
                <div class="col-location">WORK LOCATION</div>
                <div class="col-deadline">DEADLINE</div>
                <div class="col-status">STATUS</div>
                <div class="col-actions">ACTIONS</div>
            </div>

            <?php foreach ($recruitments as $job): ?>
                <div class="table-row">
                    <div class="col-title title-box">
                        <?php
                        // Đường dẫn ảnh đúng
                        $image_path = !empty($job['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/uploads/' . $job['image'])
                                ? 'uploads/' . $job['image']
                                : 'assets/images/default-job.webp';
                        ?>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="thumb"
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                             onerror="this.src='assets/images/default-job.webp'"/>

                        <div class="title-info">
                            <div class="post-title">
                                <a href="?page=edit-recruitment&id=<?php echo $job['id']; ?>"
                                   style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </div>
                            <div class="post-views">
                                <?php
                                $quantity = $job['quantity'] ?? 1;
                                echo "Quantity: {$quantity} position(s)";
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-location">
                        <?php echo htmlspecialchars(substr($job['work_location'] ?? 'Not set', 0, 50)); ?>
                        <?php echo strlen($job['work_location'] ?? '') > 50 ? '...' : ''; ?>
                    </div>

                    <div class="col-deadline">
                        <?php
                        $deadline = new DateTime($job['deadline']);
                        $now = new DateTime();
                        $interval = $now->diff($deadline);
                        $is_expired = $deadline < $now;
                        ?>
                        <span class="deadline <?php echo $is_expired ? 'expired' : ($interval->days <= 7 ? 'soon' : ''); ?>">
                            <?php echo date('d/m/Y', strtotime($job['deadline'])); ?>
                            <?php if ($is_expired): ?>
                                <span class="badge expired">Expired</span>
                            <?php elseif ($interval->days <= 7): ?>
                                <span class="badge soon"><?php echo $interval->days; ?> days left</span>
                            <?php endif; ?>
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
                        <button class="icon-btn" onclick="editRecruitment(<?php echo $job['id']; ?>)">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="icon-btn"
                                onclick="deleteRecruitment(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars($job['title']); ?>')">
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
                    <a href="?page=recruitment&p=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_keyword); ?>"
                       class="pagination-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?page=recruitment&p=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_keyword); ?>"
                       class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=recruitment&p=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_keyword); ?>"
                       class="pagination-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 10px; text-align: center; color: #6b7280;">
            Total: <?php echo $total_records; ?> records
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
</script>

<style>
    .status-published .dot {
        background-color: #10b981;
    }

    .status-draft .dot {
        background-color: #f59e0b;
    }

    .status-archived .dot {
        background-color: #6b7280;
    }

    .deadline.expired {
        color: #ef4444;
    }

    .deadline.soon {
        color: #f59e0b;
    }

    .badge.expired {
        background: #fee2e2;
        color: #ef4444;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .badge.soon {
        background: #fed7aa;
        color: #f59e0b;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .pagination-link {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        text-decoration: none;
        color: #374151;
    }

    .pagination-link.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .btn-secondary {
        padding: 8px 16px;
        background: #e5e7eb;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        color: #374151;
        display: inline-block;
    }

    .btn-primary {
        padding: 8px 16px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
    }

    .table-header, .table-row {
        display: grid;
        grid-template-columns: 3fr 2fr 1.5fr 1fr 0.8fr;
        gap: 15px;
        padding: 12px;
        align-items: center;
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
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .table-header, .table-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }
    }
</style>