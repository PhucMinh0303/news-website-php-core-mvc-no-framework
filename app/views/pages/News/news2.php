<?php

/**
 * News Listing Page - All Articles
 * Data comes from NewsController@index via NewsModel (published records saved in admin/main/news)
 */

$newsItems = $newsItems ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;

// Resolve an image path the same way admin/main/news/news_admin.php does
function news2_image_url($image)
{
    $image = trim((string) $image);
    if ($image === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $image) || $image[0] === '/') {
        return $image;
    }
    return strpos($image, '/') === false
        ? BASE_URL . 'public/upload/news/' . rawurlencode($image)
        : BASE_URL . 'public/assets/news/' . ltrim($image, '/');
}

// Build a short excerpt from the article content
function news2_excerpt($content, $length = 200)
{
    $text = trim(strip_tags((string) $content));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length) . '...';
    } elseif (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}

$featuredItems = array_slice($newsItems, 0, 2);
$listItems = array_slice($newsItems, 2);
?>
<main class="section10">
    <section class="f_page r_p6">
        <div class="min_wrap-news">
            <article class="ct_page">
                <?php if (!empty($featuredItems)): ?>
                    <div class="news">
                        <?php foreach ($featuredItems as $item): ?>
                            <a href="<?php echo View::url('news/' . rawurlencode((string) ($item['slug'] ?? ''))); ?>"
                                class="news-card">
                                <div class="news-img">
                                    <img
                                        src="<?php echo View::escape(news2_image_url($item['image'] ?? $item['avatar_img'] ?? '')); ?>"
                                        alt="<?php echo View::escape($item['title'] ?? ''); ?>" />
                                </div>
                                <div class="news-info">
                                    <span class="category"><i class="fa-solid fa-calendar-week"></i> <?php echo !empty($item['publish_date']) ? date('d/m/Y', strtotime($item['publish_date'])) : ''; ?></span>
                                    <h3>
                                        <?php echo View::escape($item['title'] ?? ''); ?>
                                    </h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <!--end list_rh_4-->
                <?php endif; ?>

                <?php if (!empty($listItems)): ?>
                    <ul class="list_news">
                        <?php foreach ($listItems as $item): ?>
                            <li>
                                <a
                                    href="<?php echo View::url('news/' . rawurlencode((string) ($item['slug'] ?? ''))); ?>"
                                    title="<?php echo View::escape($item['title'] ?? ''); ?>">
                                    <figure class="img_list_news">
                                        <span class="kieu_New">
                                            <i class="fa-duotone fa-solid fa-image"></i>
                                        </span>

                                        <img
                                            src="<?php echo View::escape(news2_image_url($item['image'] ?? $item['avatar_img'] ?? '')); ?>"
                                            alt="<?php echo View::escape($item['title'] ?? ''); ?>" />
                                    </figure>
                                </a>

                                <div class="nd_list_news">
                                    <h3 class="na_list_news link_hv">
                                        <a
                                            href="<?php echo View::url('news/' . rawurlencode((string) ($item['slug'] ?? ''))); ?>"
                                            class="link_hv"
                                            title="<?php echo View::escape($item['title'] ?? ''); ?>"><?php echo View::escape($item['title'] ?? ''); ?></a>
                                    </h3>

                                    <div class="ti_tool">
                                        <span>
                                            <i class="fa-solid fa-calendar-week"></i>
                                            <?php echo !empty($item['publish_date']) ? date('d/m/Y', strtotime($item['publish_date'])) : ''; ?>
                                        </span>
                                    </div>

                                    <div class="des_list_news">
                                        <?php echo View::escape(news2_excerpt($item['content'] ?? '')); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <!--end list_news-->
                <?php endif; ?>

                <?php if (empty($newsItems)): ?>
                    <div class="des_list_news">Chưa có bài viết nào được đăng.</div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <div class="page"
                        style="text-align: left">
                        <div class="PageNum">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p == $currentPage): ?>
                                    <span><?php echo $p; ?></span>
                                <?php else: ?>
                                    <a rel="nofollow"
                                        href="<?php echo View::url('news/page/' . $p); ?>"><?php echo $p; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if ($currentPage < $totalPages): ?>
                                <a rel="nofollow"
                                    href="<?php echo View::url('news/page/' . ($currentPage + 1)); ?>">
                                    » </a><a rel="nofollow"
                                    href="<?php echo View::url('news/page/' . ($currentPage + 1)); ?>">
                                    ›
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="clear"></div>
                    </div>
                <?php endif; ?>
            </article>
            <!--end ct_page-->

            <aside class="sb_page">
                <div class="l_qhcd">
                    <div class="r1_l_qhcd">
                        <strong>HOSE: <?php echo View::escape(SITE_NAME); ?></strong>

                        <span>Cập nhật mới nhất <?php echo date('d/m/Y'); ?></span>
                    </div>

                    <div class="r2_l_qhcd">
                        <strong>34,30</strong>

                        <span>Giá hiện thời</span>
                    </div>
                </div>
                <!--end l_qhcd-->

                <ul class="r_qhcd">
                    <li>
                        <div class="">Giá mở cửa</div>

                        <div class="">Thay đổi</div>

                        <div class="">Giá cao nhất trong ngày</div>
                    </li>

                    <li>
                        <div class="">35,00</div>

                        <div class="clor_r_qhcd_1">
                            <i class="fa-solid fa-arrow-down"></i>
                            -0,60 (0,00%)
                        </div>

                        <div class="">35,05</div>
                    </li>

                    <li>
                        <div class="">Giá đóng cửa hôm trước</div>

                        <div class="">Khối lượng</div>

                        <div class="">Giá thấp nhất trong ngày</div>
                    </li>

                    <li>
                        <div class="">34,30</div>

                        <div class="clor_r_qhcd_2">
                            <i class="fa-solid fa-arrow-up"></i>

                            23.177.900,00
                        </div>

                        <div class="">34,05</div>
                    </li>
                </ul>
                <!--end r_qhcd-->

                <ul class="list-wrap-news">
                    <li>
                        <a href="<?php echo View::url('page/asset-management'); ?>">
                            <figure class="logo-wrap-news">
                                <img
                                    src="<?php echo View::asset('img/product-service/logo/m-a-va-tai-cau-truc-doanh-nghiep-1763277283-cngpf.svg'); ?>" />
                            </figure>
                            <span>Quản lý tài sản</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo View::url('page/portfolio-management'); ?>">
                            <figure class="logo-wrap-news">
                                <img
                                    src="<?php echo View::asset('img/product-service/logo/quan-ly-danh-muc-dau-tu-1763277274-6hzt.svg'); ?>" />
                            </figure>
                            <span>Quản lý danh mục đầu tư</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo View::url('page/business-management-consulting'); ?>">
                            <figure class="logo-wrap-news">
                                <img
                                    src="<?php echo View::asset('img/product-service/logo/tu-van-quan-tri-doanh-nghiep-1763277277-waxnv.svg'); ?>" />
                            </figure>
                            <span>Tư vấn quản trị doanh nghiệp</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo View::url('page/m-a-project-consulting'); ?>">
                            <figure class="logo-wrap-news">
                                <img
                                    src="<?php echo View::asset('img/product-service/logo/tu-van-dau-tu-phat-trien-du-an-1763277280-fpcox.svg'); ?>" />
                            </figure>
                            <span>Tư vấn dự án M&A</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo View::url('page/m-a-and-corporate-restructuring'); ?>">
                            <figure class="logo-wrap-news">
                                <img
                                    src="<?php echo View::asset('img/product-service/logo/m-a-va-tai-cau-truc-doanh-nghiep-1763277283-cngpf.svg'); ?>" />
                            </figure>
                            <span>M&A và tái cấu trúc doanh nghiệp </span>
                        </a>
                    </li>
                </ul>
                <!--end list_sp_sb-->
            </aside>
            <!--end sb_page-->
        </div>
        <!--end min_wrap-->
    </section>
</main>