<?php
$job = isset($recruitment) ? $recruitment : [];
$relatedRecruitments = $relatedRecruitments ?? [];
$successMessage = $successMessage ?? null;
$errorMessage = $errorMessage ?? null;
$oldData = $oldData ?? [];
$canApply = !empty($job['can_apply']);
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
    .f_td.r_p36 {
        margin-top: 40px;
    }

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

    /* Danh sách dạng lưới */
    .list_td_grid {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Thẻ Card */
    .job-card {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .job-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #003B6F;
    }

    .job-card-thumb {
        width: 120px;
        height: 80px;
        flex-shrink: 0;
        margin-right: 20px;
        border-radius: 4px;
        overflow: hidden;
    }

    .job-card-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .job-card:hover .job-card-thumb img {
        transform: scale(1.05);
    }

    .job-card-content {
        flex: 1;
        padding-right: 20px;
    }

    .job-card-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .job-card-title a {
        color: #003B6F;
        text-decoration: none;
    }

    .job-card-title a:hover {
        color: #0056b3;
    }

    .job-card-info p {
        margin: 0 0 4px 0;
        font-size: 13px;
        color: #444;
        line-height: 1.4;
    }

    .job-card-info strong {
        font-weight: 700;
        color: #000;
    }

    .separator {
        margin: 0 5px;
        color: #ccc;
    }

    .job-card-deadline {
        width: 160px;
        text-align: center;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-right: 1px solid #eee;
        padding-right: 15px;
        margin-right: 15px;
    }

    .job-card-deadline .label {
        font-size: 13px;
        color: #333;
        margin-bottom: 4px;
    }

    .job-card-deadline .date {
        font-size: 15px;
        font-weight: 700;
        color: #000;
    }

    .job-card-action {
        width: 140px;
        text-align: right;
        flex-shrink: 0;
    }

    .btn-view-detail {
        display: inline-block;
        padding: 8px 15px;
        border: 1px solid #003B6F;
        color: #003B6F;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 4px;
        background: transparent;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-view-detail:hover {
        background-color: #003B6F;
        color: #fff;
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

        .job-card {
            flex-direction: column;
            align-items: flex-start;
            position: relative;
        }

        .job-card-thumb {
            width: 100%;
            height: 180px;
            margin-bottom: 15px;
            margin-right: 0;
        }

        .job-card-content {
            padding-right: 0;
            margin-bottom: 10px;
        }

        .job-card-deadline {
            width: 100%;
            text-align: left;
            border-right: none;
            border-top: 1px solid #eee;
            padding-top: 10px;
            padding-right: 0;
            margin-right: 0;
            margin-bottom: 10px;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .job-card-action {
            width: 100%;
            text-align: left;
        }

        .btn-view-detail {
            width: 100%;
            text-align: center;
            box-sizing: border-box;
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
                        <strong>Mô tả công việc</strong>
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
                                            <input type="file" name="filechon" />
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

                    <div class="list_td_grid">
                        <?php if (!empty($relatedRecruitments)): ?>
                            <?php foreach ($relatedRecruitments as $related): ?>
                                <?php
                                // Xử lý ảnh cho từng item (mặc định nếu không có)
                                $relImageUrl = View::asset('img/recruitment/default-job.webp');
                                if (!empty($related['image']) && $related['image'] !== 'default-job.webp') {
                                    foreach (['public/upload/recruitments/', 'public/uploads/recruitments/'] as $directory) {
                                        if (is_file(ROOT_PATH . $directory . $related['image'])) {
                                            $relImageUrl = BASE_URL . $directory . rawurlencode($related['image']);
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <div class="job-card">
                                    <div class="job-card-thumb">
                                        <a href="<?php echo $this->url('recruitment/' . $related['slug']); ?>">
                                            <img src="<?php echo $relImageUrl; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                        </a>
                                    </div>

                                    <div class="job-card-content">
                                        <h3 class="job-card-title">
                                            <a href="<?php echo $this->url('recruitment/' . $related['slug']); ?>">
                                                <?php echo htmlspecialchars($related['title']); ?>
                                            </a>
                                        </h3>
                                        <div class="job-card-info">
                                            <p><strong>Nơi làm việc:</strong> <?php echo htmlspecialchars($related['work_location'] ?? 'Đang cập nhật'); ?></p>
                                            <p>
                                                <strong>Bằng cấp:</strong> <?php echo htmlspecialchars($related['degree'] ?? 'Đang cập nhật'); ?>
                                                <span class="separator">|</span>
                                                <strong>Số lượng tuyển:</strong> <?php echo str_pad((int)($related['quantity'] ?? 0), 2, '0', STR_PAD_LEFT); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="job-card-deadline">
                                        <span class="label">Hạn nộp hồ sơ</span>
                                        <span class="date"><?php echo !empty($related['deadline']) ? date('d-m-Y', strtotime($related['deadline'])) : 'Đang cập nhật'; ?></span>
                                    </div>

                                    <div class="job-card-action">
                                        <a href="<?php echo $this->url('recruitment/' . $related['slug']); ?>" class="btn-view-detail">
                                            XEM CHI TIẾT
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Hiện chưa có tin tuyển dụng nào khác.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <!-- KẾT THÚC: Phần Tuyển dụng khác -->

        </div> <!-- Kết thúc .min_wrap2 -->
    </section>

</main>