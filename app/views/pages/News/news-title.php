<?php
/**
 * News Title Page - Article Detail View
 */
// views/news/detail.php

?>
<main class="section10">
    <section class="f_page r_p6">
        <div class="min_wrap-news">
            <article class="ct_page">
                <h1 class="til_news_D">
                    <?php echo htmlspecialchars($news['title']); ?>
                </h1>
                <div class="bot_til_td_D">
                    <p class="dc_td_D">
            <span>
              <i class="fa-solid fa-calendar-week"></i>
              <?php echo date('d/m/Y H:i', strtotime($news['published_at'] ?? $news['publish_date'])); ?>
            </span>
                        <span>
              <i class="fa-regular fa-eye"></i>
              <?php echo number_format($news['views']); ?>
            </span>
                    </p>
                    <div class="share_D">
                        <span>Share</span>
                        <ul class="list_share_D">
                            <li>
                                <a
                                        class="copy_links"
                                        href="javascript:void(0)"
                                        title="Copy link"
                                        val="<?php echo $currentUrl; ?>"
                                        onclick="copyToClipboard(this.getAttribute('val'))">
                                    >
                                    <i class="fa-solid fa-link"></i>
                                </a>
                            </li>
                            <li>
                                <a
                                        href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>&t=<?php echo urlencode($news['title']); ?>"
                                        target="_blank"
                                        title="Chia sẻ bài viết lên Facebook"
                                        onclick="trackShare('facebook', <?php echo $news['news_id']; ?>)">
                                    >
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a
                                        href="http://www.twitter.com/share?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($news['title']); ?>"
                                        target="_blank"
                                        title="Chia sẻ bài viết lên Twitter"
                                        onclick="trackShare('twitter', <?php echo $news['news_id']; ?>)">
                                    >
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Author Info -->
                <?php if (!empty($news['author_name'])): ?>
                    <div class="author-info">
                        <div class="author-avatar">
                            <?php if ($news['author_avatar']): ?>
                                <img src="<?php echo htmlspecialchars($news['author_avatar']); ?>"
                                     alt="<?php echo htmlspecialchars($news['author_name']); ?>">
                            <?php else: ?>
                                <i class="fa-solid fa-user-circle"></i>
                            <?php endif; ?>
                        </div>
                        <div class="author-details">
                            <strong><?php echo htmlspecialchars($news['author_name']); ?></strong>
                            <?php if ($news['author_bio']): ?>
                                <p><?php echo htmlspecialchars($news['author_bio']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <h2 class="des_news_D">
                    <?php echo htmlspecialchars($news['description']); ?>
                </h2>
                <!-- Article Content -->
                <div class="f-detail clearfix">
                    <?php if ($news['featured_image']): ?>
                        <div class="featured-image">
                            <img alt="<?php echo htmlspecialchars($news['title']); ?>"
                                 src="<?php echo htmlspecialchars($news['featured_image']); ?>"
                                 style="width: 100%; max-width: 800px; height: auto;"/>
                            <?php if ($news['featured_image_caption']): ?>
                                <p class="image-caption"><?php echo htmlspecialchars($news['featured_image_caption']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php echo $news['content']; ?>
                </div>
                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="news-tags">
                        <strong><i class="fa-solid fa-tags"></i> Tags:</strong>
                        <?php foreach ($tags as $tag): ?>
                            <a href="/news/tag/<?php echo htmlspecialchars($tag['slug']); ?>" class="tag-link">
                                #<?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
        </div>
    </section>
</main>
