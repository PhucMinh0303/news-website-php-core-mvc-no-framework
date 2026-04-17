<?php
// app/views/recruitment/index.php
// Đảm bảo biến $recruitments đã được truyền từ controller
?>
<main class="section10">
    <section class="f_td r_p36">
        <div class="min_wrap_recruitment">

            <?php if (isset($recruitments) && is_array($recruitments) && count($recruitments) > 0): ?>
                <ul class="list_td">
                    <?php foreach ($recruitments as $job): ?>
                        <li>
                            <div class="c1_list_td">
                                <a href="<?= htmlspecialchars($this->url('recruitment/' . ($job['slug'] ?? $job['id']))); ?>"
                                   title="<?= htmlspecialchars($job['title'] ?? ''); ?>">
                                    <figure class="img_list_td">
                                        <img
                                                src="<?= htmlspecialchars($this->asset('img/recruitment/' . ($job['image'] ?? 'default-job.webp'))); ?>"
                                                alt="<?= htmlspecialchars($job['title'] ?? ''); ?>"
                                        />
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
                    <?php if (isset($pagination) && is_array($pagination)): ?>
                        <!-- Thêm pagination nếu cần -->
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </section>
</main>