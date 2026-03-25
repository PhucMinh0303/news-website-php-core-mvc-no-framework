<?php
// app/views/recruitment/index.php
?>
    <main class="section10">
        <section class="f_td r_p36">
            <div class="min_wrap_recruitment">

                <?php if (isset($recruitments) && count($recruitments) > 0): ?>
                    <ul class="list_td">
                        <?php foreach ($recruitments as $job): ?>
                            <li>
                                <div class="c1_list_td">
                                    <a href="<?php echo $this->url('recruitment/' . $job['slug']); ?>"
                                       title="<?php echo htmlspecialchars($job['recruitment_title']); ?>">
                                        <figure class="img_list_td">
                                            <img
                                                    src="<?php echo $this->asset('img/recruitment/' . ($job['image'] ?? 'default-job.webp')); ?>"
                                                    alt="<?php echo htmlspecialchars($job['recruitment_title']); ?>"
                                            />
                                        </figure>
                                    </a>

                                    <div class="if_list_td">
                                        <h3 class="na_list_td link_hv">
                                            <a href="<?php echo $this->url('recruitment/' . $job['slug']); ?>"
                                               class="link_hv"
                                               title="<?php echo htmlspecialchars($job['recruitment_title']); ?>">
                                                <?php echo htmlspecialchars($job['recruitment_title']); ?>
                                            </a>
                                        </h3>

                                        <p>Nơi làm việc: <?php echo htmlspecialchars($job['location']); ?></p>

                                        <ol>
                                            <li>
                                                Bằng cấp:
                                                <strong><?php echo htmlspecialchars($job['education'] ?? 'Cao Đẳng - Đại Học'); ?></strong>
                                            </li>
                                            <li>
                                                Số lượng tuyển:
                                                <strong><?php echo $job['quantity']; ?></strong>
                                            </li>
                                            <?php if (!empty($job['salary_range'])): ?>
                                                <li>
                                                    Mức lương:
                                                    <strong><?php echo htmlspecialchars($job['salary_range']); ?></strong>
                                                </li>
                                            <?php endif; ?>
                                        </ol>
                                    </div>
                                </div>

                                <div class="c2_list_td">
                                    <div class="date_list_td">
                                        <span>Hạn nộp hồ sơ</span>
                                        <strong><?php echo date('d-m-Y', strtotime($job['deadline'])); ?></strong>
                                    </div>

                                    <div class="but_list_td">
                                        <a href="<?php echo $this->url('recruitment/' . $job['slug']); ?>"
                                           class="but_03"
                                           title="<?php echo htmlspecialchars($job['recruitment_title']); ?>">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <!--end list_td-->
                <?php else: ?>
                    <div class="no-recruitment">
                        <p>Hiện tại chưa có vị trí tuyển dụng nào. Vui lòng quay lại sau!</p>
                    </div>
                <?php endif; ?>

                <div class="page">
                    <div class="PageNum"></div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--end min_wrap-->
        </section>
    </main>

<?php
// Helper methods for View
function url($path)
{
    return '/' . ltrim($path, '/');
}

function asset($path)
{
    return '/public/' . ltrim($path, '/');
}

?>