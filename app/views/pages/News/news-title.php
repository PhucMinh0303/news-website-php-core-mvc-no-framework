<?php

/**
 * News Title Page - Article Detail View
 */
// views/news/detail.php

$news = $news ?? [];
$tags = $tags ?? [];
$currentUrl = $currentUrl ?? '';
$publishedAt = $news['published_at'] ?? $news['publish_date'] ?? null;
$publishedTimestamp = $publishedAt ? strtotime($publishedAt) : false;
?>
<main class="section10">
    <section class="f_page r_p6">
        <div class="min_wrap-news">
            <article class="ct_page">
                <h1 class="til_news_D">
                    <?php echo htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <div class="bot_til_td_D">
                    <p class="dc_td_D">
                        <?php if ($publishedTimestamp !== false): ?>
                            <span>
                                <i class="fa-solid fa-calendar-week"></i>
                                <?php echo date('d/m/Y H:i', $publishedTimestamp); ?>
                            </span>
                        <?php endif; ?>
                        <span>
                            <i class="fa-regular fa-eye"></i>
                            <?php echo number_format((int) ($news['views'] ?? 0)); ?>
                        </span>
                    </p>
                    
                    <div class="share_D">
                        <span>Share</span>
                        <ul class="list_share_D">
                            <li>
                                <a
                                    class="copy_links"
                                    href="#"
                                    title="Copy link"
                                    val="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    onclick="event.preventDefault(); copyToClipboard(this.getAttribute('val'))">
                                    <i class="fa-solid fa-link"></i>
                                </a>
                            </li>
                            <li>
                                <a
                                    href="<?php echo htmlspecialchars('https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => $currentUrl, 't' => $news['title'] ?? '']), ENT_QUOTES, 'UTF-8'); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Chia sẻ bài viết lên Facebook">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a
                                    href="<?php echo htmlspecialchars('https://twitter.com/intent/tweet?' . http_build_query(['url' => $currentUrl, 'text' => $news['title'] ?? '']), ENT_QUOTES, 'UTF-8'); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Chia sẻ bài viết lên Twitter">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                

                <?php if (!empty($news['description'])): ?>
                    <h2 class="des_news_D">
                        <?php echo htmlspecialchars($news['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                <?php endif; ?>
                <!-- Article Content -->
                <div class="f-detail clearfix">
                    <?php echo $news['content'] ?? ''; ?>
                </div>
                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="news-tags">
                        <strong><i class="fa-solid fa-tags"></i> Tags:</strong>
                        <?php foreach ($tags as $tag): ?>
                            <a href="/news/tag/<?php echo rawurlencode((string) ($tag['slug'] ?? '')); ?>" class="tag-link">
                                #<?php echo htmlspecialchars($tag['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
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
    </section>
</main>
<script>
    window.copyToClipboard = async function(text) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return;
            }

            const field = document.createElement('textarea');
            field.value = text;
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();
            const copied = document.execCommand('copy');
            field.remove();
            if (!copied) {
                throw new Error('Clipboard copy failed');
            }
        } catch (error) {
            console.error('Unable to copy article link:', error);
        }
    };
</script>