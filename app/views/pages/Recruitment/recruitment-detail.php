<?php
$job = isset($recruitment) ? $recruitment : [];
?>
<main class="section10">
    <section class="f_td_D">
        <div class="min_wrap2">
            <article class="ct_page">
                <div class="ct_td_D">
                    <div class="til_td_D">
                        <h1 class="til_news_D"><?php echo htmlspecialchars($job['title'] ?? ''); ?></h1>
                        <div class="job-meta">
                            <span><strong>Vị trí:</strong> <?php echo htmlspecialchars($job['position'] ?? ''); ?></span>
                            <span><strong>Nơi làm việc:</strong> <?php echo htmlspecialchars($job['work_location'] ?? ''); ?></span>
                            <span><strong>Bằng cấp:</strong> <?php echo htmlspecialchars($job['degree'] ?? ''); ?></span>
                            <span><strong>Số lượng:</strong> <?php echo htmlspecialchars($job['quantity'] ?? ''); ?></span>
                            <span><strong>Hạn nộp:</strong> <?php echo !empty($job['deadline']) ? date('d-m-Y', strtotime($job['deadline'])) : ''; ?></span>
                            <?php if (!empty($job['salary'])): ?>
                                <span><strong>Lương:</strong> <?php echo htmlspecialchars($job['salary']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="f-detail clearfix">
                        <strong>Mô tả công việc</strong>
                        <div><?php echo !empty($job['description']) ? $job['description'] : '<p>Chưa có mô tả</p>'; ?></div>

                        <strong>Yêu cầu công việc</strong>
                        <div><?php echo !empty($job['requirements']) ? $job['requirements'] : '<p>Chưa có yêu cầu</p>'; ?></div>

                        <strong>Quyền lợi</strong>
                        <div><?php echo !empty($job['benefits']) ? $job['benefits'] : '<p>Chưa có quyền lợi</p>'; ?></div>

                        <br/>
                        <div>
                            <strong>Liên hệ</strong><br/>
                            <span>Người liên hệ: <?php echo htmlspecialchars($job['contact_person'] ?? ''); ?></span><br/>
                            <span>ĐT/Zalo: <?php echo htmlspecialchars($job['contact_phone'] ?? ''); ?></span><br/>
                            <span>Email: <?php echo htmlspecialchars($job['contact_email'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>

                <aside class="sb_page">
                    <div class="sb_td_D sty_sticky">
                        <div class="if_td_D">
                            <h3 class="til_sb_td_D">Ứng tuyển trực tuyến</h3>
                            <?php if (!empty($successMessage)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                            <?php endif; ?>

                            <form id="fcb_td" class="fcb_td" method="post" enctype="multipart/form-data"
                                  action="<?php echo $this->url('recruitment/apply'); ?>">
                                <input type="hidden" name="recruitment_id"
                                       value="<?php echo htmlspecialchars($job['id'] ?? ''); ?>"/>
                                <input type="hidden" name="slug"
                                       value="<?php echo htmlspecialchars($job['slug'] ?? ''); ?>"/>

                                <ul class="ul_r_f_contact">
                                    <li>
                                        <input type="text" placeholder="Họ tên *" name="ten"
                                               value="<?php echo htmlspecialchars($oldData['ten'] ?? ''); ?>"
                                               class="ipt_f_contact box-sizing-fix"/>
                                    </li>
                                    <li>
                                        <input type="text" placeholder="Điện thoại *" name="dt"
                                               value="<?php echo htmlspecialchars($oldData['dt'] ?? ''); ?>"
                                               class="ipt_f_contact box-sizing-fix"/>
                                    </li>
                                    <li>
                                        <input type="text" placeholder="Email *" name="email"
                                               value="<?php echo htmlspecialchars($oldData['email'] ?? ''); ?>"
                                               class="ipt_f_contact box-sizing-fix"/>
                                    </li>
                                    <li>
                                        <textarea placeholder="Nội dung" name="noidung"
                                                  class="txt_f_contact box-sizing-fix"><?php echo htmlspecialchars($oldData['noidung'] ?? ''); ?></textarea>
                                    </li>
                                    <li>
                                        <strong>Hồ sơ của bạn: (Cho nộp file CV)</strong><br/>
                                        <input type="file" name="filechon"/>
                                    </li>
                                </ul>

                                <button type="submit" name="guituyendung" class="but_contact">Ứng tuyển</button>
                            </form>
                        </div>
                    </div>
                </aside>
            </article>

            <section class="f_td r_p36">
                <div class="min_wrap_recruitment">
                    <div class="tit_cont_1">
                        <h2 class="na_til_cont">Tuyển dụng khác</h2>
                    </div>
                    <ul class="list_td">
                        <?php foreach ($relatedRecruitments as $related): ?>
                            <li>
                                <a href="<?php echo $this->url('recruitment/' . $related['slug']); ?>"><?php echo htmlspecialchars($related['title']); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </div>
    </section>
</main>
