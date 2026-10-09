<?php
$job = isset($recruitment) ? $recruitment : [];
$relatedRecruitments = $relatedRecruitments ?? [];
$successMessage = $successMessage ?? null;
$errorMessage = $errorMessage ?? null;
$oldData = $oldData ?? [];
$canApply = !empty($job['can_apply']);
$getRecruitmentImageUrl = static function ($image) {
    $image = trim((string)$image);
    if ($image === '' || $image === 'default-job.webp') {
        return View::asset('img/recruitment/default-job.webp');
    }

    foreach (['public/upload/recruitments/', 'public/uploads/recruitments/'] as $directory) {
        if (is_file(ROOT_PATH . $directory . $image)) {
            return BASE_URL . $directory . rawurlencode($image);
        }
    }

    return View::asset('img/recruitment/default-job.webp');
};
$imageUrl = View::asset('img/recruitment/default-job.webp');
if (!empty($job['image']) && $job['image'] !== 'default-job.webp') {
    foreach (['public/upload/recruitments/', 'public/uploads/recruitments/'] as $directory) {
        if (is_file(ROOT_PATH . $directory . $job['image'])) {
            $imageUrl = BASE_URL . $directory . rawurlencode($job['image']);
            break;
        }
    }
}
?>
<style>
    /* --- Bố cục 2 cột --- */
    .ct_page {
        display: flex;
        gap: 30px;
        background: #fff;
        padding: 20px 0;
        border-radius: 8px;
    }

    /* Cột trái: Nội dung */
    .ct_td_D {
        flex: 1;
        width: 100%;
    }

    /* Cột phải: Sidebar */
    .sb_page {
        width: 280px;
        flex-shrink: 0;
    }

    .sb_page .fcb_td {
        position: static;
        transform: none;
        z-index: auto;
        width: 100%;
        max-width: none;
        padding: 0;
        border-radius: 0;
        opacity: 1;
        visibility: visible;
        display: block;
    }

    /* --- Phần Tiêu đề & Thông tin cơ bản --- */
    .til_td_D {
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        position: relative;
    }

    .til_news_D {
        font-size: 28px;
        font-weight: 700;
        color: #000;
        margin-bottom: 15px;
        text-transform: uppercase;
        line-height: 1.3;
    }

    .share-buttons {
        position: absolute;
        top: 0;
        right: 0;
        font-size: 14px;
        color: #666;
    }

    /* Meta info trong bài viết chính */
    .job-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 14px;
        color: #444;
    }

    .job-meta span {
        display: inline-block;
        margin-right: 15px;
    }

    /* --- Nội dung chi tiết --- */
    .f-detail {
        font-size: 15px;
        line-height: 1.6;
        color: #333;
    }

    .f-detail strong {
        display: block;
        font-size: 18px;
        font-weight: 700;
        margin-top: 25px;
        margin-bottom: 10px;
        color: #000;
    }

    .f-detail div {
        margin-bottom: 15px;
    }

    .f-detail ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .f-detail ul li {
        position: relative;
        padding-left: 15px;
        margin-bottom: 8px;
    }

    .f-detail ul li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #000;
        font-weight: bold;
    }

    /* --- Sidebar (Cột phải) --- */
    .sb_td_D.sty_sticky {
        position: sticky;
        top: 20px;
    }

    /* Khung thông tin việc làm (Box trắng viền xanh) */
    .if_td_D {
        background-color: #fff;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
        border: 1px solid #dceef7;
        /* Viền xanh nhạt */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Tiêu đề sidebar */
    .til_sb_td_D {
        font-size: 16px;
        font-weight: 700;
        color: #003B6F;
        /* Màu xanh đậm */
        margin-bottom: 15px;
        margin-top: 0;
        text-transform: uppercase;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    /* Thông tin tóm tắt trong sidebar */
    .if_td_D p {
        margin-bottom: 10px;
        line-height: 1.6;
        color: #333;
        display: flex;
        flex-direction: column;
    }

    .if_td_D p strong {
        font-weight: 700;
        color: #000;
        margin-bottom: 3px;
    }

    /* --- Form Ứng tuyển --- */
    .ul_r_f_contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ul_r_f_contact li {
        margin-bottom: 12px;
    }

    /* Input, Textarea styles */
    .ipt_f_contact,
    .txt_f_contact {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        background: #fff;
        box-sizing: border-box;
    }

    .txt_f_contact {
        height: 100px;
        resize: vertical;
    }

    /* Nút Ứng tuyển */
    .but_contact {
        width: 100%;
        background-color: #003B6F;
        /* Màu xanh đậm như hình */
        color: #fff;
        border: none;
        padding: 12px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 4px;
        cursor: pointer;
        text-transform: uppercase;
        margin-top: 10px;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .but_contact:hover {
        background-color: #002a50;
    }

    /* Giả lập icon nếu chưa có FontAwesome */
    .but_contact::before {
        content: "📄";
        /* Hoặc dùng icon font nếu có */
        font-size: 18px;
    }

    /* Thông báo lỗi/thành công */
    .alert {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* --- Phần Tuyển dụng khác --- */
    

    .tit_cont_1 {
        margin-bottom: 20px;
        border-bottom: none;
    }

    .na_til_cont {
        font-size: 26px;
        font-weight: 700;
        color: #003B6F;
        text-transform: uppercase;
        margin: 0;
        border-left: 5px solid #003B6F;
        padding-left: 15px;
    }

    /* --- Responsive --- */
    @media (max-width: 991px) {
        .ct_page {
            flex-direction: column;
        }

        .sb_page {
            width: 100%;
            margin-top: 30px;
        }

    }
</style>
<main class="section10">
    <section class="f_td_D">
        <div class="min_wrap2">
            <article class="ct_page">
                <div class="ct_td_D">
                    <div class="til_td_D">
                        <h1 class="til_news_D"><?php echo htmlspecialchars($job['title'] ?? ''); ?></h1>

                        <div class="job-meta">
                            <span><strong>Hình thức:</strong> <?php echo htmlspecialchars($job['work_type'] ?? ''); ?></span>
                            <span><strong>Nơi làm việc:</strong> <?php echo htmlspecialchars($job['work_location'] ?? ''); ?></span>
                            <span><strong>Bằng cấp:</strong> <?php echo htmlspecialchars($job['degree'] ?? ''); ?></span>
                            <span><strong>Số lượng:</strong> <?php echo (int)($job['quantity'] ?? 0); ?></span>
                            <span><strong>Hạn nộp:</strong> <?php echo !empty($job['deadline']) ? date('d-m-Y', strtotime($job['deadline'])) : 'Đang cập nhật'; ?></span>
                            <?php if (!empty($job['salary_range'])): ?>
                                <span><strong>Lương:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="f-detail clearfix">
                        
                        <div><?php echo !empty($job['description']) ? $job['description'] : '<p>Chưa có mô tả</p>'; ?></div>

                        <strong>Yêu cầu công việc</strong>
                        <div><?php echo !empty($job['requirements']) ? $job['requirements'] : '<p>Chưa có yêu cầu</p>'; ?></div>

                        <strong>Quyền lợi</strong>
                        <div><?php echo !empty($job['benefits']) ? $job['benefits'] : '<p>Chưa có quyền lợi</p>'; ?></div>

                        <br />
                        <div>
                            <strong>Liên hệ</strong><br />
                            <span>Người liên hệ: <?php echo htmlspecialchars($job['contact_person'] ?? ''); ?></span><br />
                            <span>ĐT/Zalo: <?php echo htmlspecialchars($job['contact_phone'] ?? ''); ?></span><br />
                            <span>Email: <?php echo htmlspecialchars($job['contact_email'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>

                <aside class="sb_page">
                    <div class="sb_td_D sty_sticky">
                        <div class="if_td_D">
                            <?php if (!$canApply): ?>
                                <h3 class="til_sb_td_D">Tin tuyển dụng đã đóng</h3>
                                <p><?php echo htmlspecialchars($job['closed_reason'] ?? 'Tin tuyển dụng hiện không còn nhận hồ sơ.'); ?></p>
                            <?php else: ?>
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
                                        value="<?php echo htmlspecialchars($job['id'] ?? ''); ?>" />
                                    <input type="hidden" name="slug"
                                        value="<?php echo htmlspecialchars($job['slug'] ?? ''); ?>" />

                                    <ul class="ul_r_f_contact">
                                        <li>
                                            <input type="text" placeholder="Họ tên *" name="ten"
                                                value="<?php echo htmlspecialchars($oldData['ten'] ?? ''); ?>"
                                                class="ipt_f_contact box-sizing-fix" />
                                        </li>
                                        <li>
                                            <input type="text" placeholder="Điện thoại *" name="dt"
                                                value="<?php echo htmlspecialchars($oldData['dt'] ?? ''); ?>"
                                                class="ipt_f_contact box-sizing-fix" />
                                        </li>
                                        <li>
                                            <input type="text" placeholder="Email *" name="email"
                                                value="<?php echo htmlspecialchars($oldData['email'] ?? ''); ?>"
                                                class="ipt_f_contact box-sizing-fix" />
                                        </li>
                                        <li>
                                            <textarea placeholder="Nội dung" name="noidung"
                                                class="txt_f_contact box-sizing-fix"><?php echo htmlspecialchars($oldData['noidung'] ?? ''); ?></textarea>
                                        </li>
                                        <li>
                                            <strong>Hồ sơ của bạn: (Cho nộp file CV)</strong><br />
                                            <input type="file" name="filechon" accept=".pdf,application/pdf" />
                                        </li>
                                    </ul>

                                    <button type="submit" name="guituyendung" class="but_contact">Ứng tuyển</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </article>

            <section class="f_td r_p36 section-related-jobs">
                <div class="min_wrap_recruitment">
                    <div class="tit_cont_1">
                        <h2 class="na_til_cont">Tuyển dụng khác</h2>
                    </div>

                    <ul class="list_td">
                        <?php if (!empty($relatedRecruitments)): ?>
                            <?php foreach ($relatedRecruitments as $related): ?>
                                <?php $relatedUrl = $this->url('recruitment/' . ($related['slug'] ?? $related['id'])); ?>
                                <li>
                                    <div class="c1_list_td">
                                        <a href="<?= htmlspecialchars($relatedUrl); ?>"
                                            title="<?= htmlspecialchars($related['title'] ?? ''); ?>">
                                            <figure class="img_list_td">
                                                <img
                                                    src="<?= htmlspecialchars($getRecruitmentImageUrl($related['image'] ?? 'default-job.webp')); ?>"
                                                    alt="<?= htmlspecialchars($related['title'] ?? ''); ?>" />
                                            </figure>
                                        </a>

                                        <div class="if_list_td">
                                            <h3 class="na_list_td link_hv">
                                                <a href="<?= htmlspecialchars($relatedUrl); ?>"
                                                    class="link_hv"
                                                    title="<?= htmlspecialchars($related['title'] ?? ''); ?>">
                                                    <?= htmlspecialchars($related['title'] ?? ''); ?>
                                                </a>
                                            </h3>

                                            <p>
                                                Nơi làm
                                                việc: <?= htmlspecialchars($related['work_location'] ?? $related['location'] ?? 'Đang cập nhật'); ?>
                                            </p>

                                            <ol>
                                                <li>
                                                    Bằng cấp:
                                                    <strong><?= htmlspecialchars($related['degree'] ?? $related['education'] ?? 'Cao Đẳng - Đại Học'); ?></strong>
                                                </li>
                                                <li>
                                                    Số lượng tuyển:
                                                    <strong><?= (int)($related['quantity'] ?? 1); ?></strong>
                                                </li>
                                                <?php if (!empty($related['salary_range'])): ?>
                                                    <li>
                                                        Mức lương:
                                                        <strong><?= htmlspecialchars($related['salary_range']); ?></strong>
                                                    </li>
                                                <?php endif; ?>
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="c2_list_td">
                                        <div class="date_list_td">
                                            <span>Hạn nộp hồ sơ</span>
                                            <strong>
                                                <?= !empty($related['deadline']) ? date('d-m-Y', strtotime($related['deadline'])) : 'Đang cập nhật'; ?>
                                            </strong>
                                        </div>

                                        <div class="but_list_td">
                                            <a href="<?= htmlspecialchars($relatedUrl); ?>"
                                                class="but_03"
                                                title="<?= htmlspecialchars($related['title'] ?? ''); ?>">
                                                Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="no-recruitment">
                                Hiện chưa có tin tuyển dụng nào khác.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>
            <!-- KẾT THÚC: Phần Tuyển dụng khác -->

        </div> <!-- Kết thúc .min_wrap2 -->
    </section>

</main>