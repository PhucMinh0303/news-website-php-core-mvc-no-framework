<?php
$recruitments = $jobs ?? $recruitments ?? [];
$currentPage = max(1, (int)($current_page ?? 1));
$totalPages = max(1, (int)($total_pages ?? 1));
$keyword = trim((string)($keyword ?? ''));
$location = trim((string)($location ?? ''));

$buildRecruitmentUrl = static function ($page) use ($keyword, $location) {
    $query = ['page' => max(1, (int)$page)];
    if ($keyword !== '') {
        $query['keyword'] = $keyword;
    }
    if ($location !== '') {
        $query['location'] = $location;
    }

    return Router::url('recruitment') . '?' . http_build_query($query);
};

$getRecruitmentImageUrl = static function ($image) {
    $image = trim((string)$image);
    if ($image === '' || $image === 'default-job.webp') {
        return View::asset('img/recruitment/default-job.webp');
    }

    $uploadDirectories = [
        'public/upload/recruitments/',
        'public/uploads/recruitments/'
    ];

    foreach ($uploadDirectories as $directory) {
        if (is_file(ROOT_PATH . $directory . $image)) {
            return BASE_URL . $directory . rawurlencode($image);
        }
    }

    return View::asset('img/recruitment/default-job.webp');
};
?>
<main class="section10">
    <section class="f_td r_p36">
        <div class="min_wrap_recruitment">

            <?php if (is_array($recruitments) && count($recruitments) > 0): ?>
                <ul class="list_td">
                    <?php foreach ($recruitments as $job): ?>
                        <li>
                            <div class="c1_list_td">
                                <a href="<?= htmlspecialchars($this->url('recruitment/' . ($job['slug'] ?? $job['id']))); ?>"
                                    title="<?= htmlspecialchars($job['title'] ?? ''); ?>">
                                    <figure class="img_list_td">
                                        <img
                                            src="<?= htmlspecialchars($getRecruitmentImageUrl($job['image'] ?? 'default-job.webp')); ?>"
                                            alt="<?= htmlspecialchars($job['title'] ?? ''); ?>" />
                                    </figure>
                                </a>

                                <div class="if_list_td">
                                    <h3 class="na_list_td link_hv">
                                        <a href="<?= htmlspecialchars($this->url('recruitment/' . ($job['slug'] ?? $job['id']))); ?>"
                                            class="link_hv"
                                            title="<?= htmlspecialchars($job['title'] ?? ''); ?>">
                                            <?= htmlspecialchars($job['title'] ?? ''); ?>
                                        </a>
                                    </h3>

                                    <p>
                                        Nơi làm
                                        việc: <?= htmlspecialchars($job['work_location'] ?? $job['location'] ?? 'Đang cập nhật'); ?>
                                    </p>

                                    <ol>
                                        <li>
                                            Bằng cấp:
                                            <strong><?= htmlspecialchars($job['degree'] ?? $job['education'] ?? 'Cao Đẳng - Đại Học'); ?></strong>
                                        </li>
                                        <li>
                                            Số lượng tuyển:
                                            <strong><?= (int)($job['quantity'] ?? 1); ?></strong>
                                        </li>
                                        <?php if (!empty($job['salary_range'])): ?>
                                            <li>
                                                Mức lương:
                                                <strong><?= htmlspecialchars($job['salary_range']); ?></strong>
                                            </li>
                                        <?php endif; ?>
                                    </ol>
                                </div>
                            </div>

                            <div class="c2_list_td">
                                <div class="date_list_td">
                                    <span>Hạn nộp hồ sơ</span>
                                    <strong>
                                        <?= isset($job['deadline']) ? date('d-m-Y', strtotime($job['deadline'])) : 'Đang cập nhật'; ?>
                                    </strong>
                                </div>

                                <div class="but_list_td">
                                    <a href="<?= htmlspecialchars($this->url('recruitment/' . ($job['slug'] ?? $job['id']))); ?>"
                                        class="but_03"
                                        title="<?= htmlspecialchars($job['title'] ?? ''); ?>">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-recruitment">
                    <p>Hiện tại chưa có vị trí tuyển dụng nào. Vui lòng quay lại sau!</p>
                </div>
            <?php endif; ?>

            <div class="page">
                <div class="PageNum">
                    <?php if ($totalPages > 1): ?>
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= htmlspecialchars($buildRecruitmentUrl($currentPage - 1)); ?>" aria-label="Trang trước">&laquo;</a>
                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                            <a href="<?= htmlspecialchars($buildRecruitmentUrl($page)); ?>"
                                class="<?= $page === $currentPage ? 'active' : ''; ?>">
                                <?= $page; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= htmlspecialchars($buildRecruitmentUrl($currentPage + 1)); ?>" aria-label="Trang sau">&raquo;</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </section>
</main>