<?php
/**
 * Section 5 - News and Events
 */

$homepageNewsItems = $homepageNewsItems ?? [];
$featuredItems = array_slice($homepageNewsItems, 0, 2);
$listItems = array_slice($homepageNewsItems, 2, 3);

$section5ImageUrl = static function ($image): string {
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
};
?>
<section class="section5">
  <div class="news-container">
    <div class="news-title">
      <h4>Tin tức và Sự kiện</h4>
    </div>
    <div class="news1">
      <?php foreach ($featuredItems as $item): ?>
        <a href="<?php echo View::url('news/' . rawurlencode((string) ($item['slug'] ?? ''))); ?>" class="news-card">
          <div class="news-img">
            <img
              src="<?php echo View::escape($section5ImageUrl($item['image'] ?? $item['avatar_img'] ?? '')); ?>"
              alt="<?php echo View::escape($item['title'] ?? ''); ?>"
            />
          </div>
          <div class="news-info">
            <span class="category"><i class="fa-solid fa-calendar-week"></i> <?php echo !empty($item['publish_date']) ? date('d/m/Y', strtotime($item['publish_date'])) : ''; ?></span>
            <h3><?php echo View::escape($item['title'] ?? ''); ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
      <div class="news-column">
        <div class="new">
          <div class="news-list">
            <?php foreach ($listItems as $index => $item): ?>
              <a href="<?php echo View::url('news/' . rawurlencode((string) ($item['slug'] ?? ''))); ?>" class="news-item">
                <img
                  src="<?php echo View::escape($section5ImageUrl($item['image'] ?? $item['avatar_img'] ?? '')); ?>"
                  alt="<?php echo View::escape($item['title'] ?? ''); ?>"
                />
                <div class="news-text">
                  <h3><?php echo View::escape($item['title'] ?? ''); ?></h3>
                  <p class="date">
                    <i class="fa-solid fa-calendar-week"></i> <?php echo !empty($item['publish_date']) ? date('d/m/Y', strtotime($item['publish_date'])) : ''; ?>
                  </p>
                </div>
              </a>
              <?php if ($index < count($listItems) - 1): ?>
                <span class="line-hea-r"></span>
              <?php endif; ?>
            <?php endforeach; ?>
            <?php if (empty($homepageNewsItems)): ?>
              <p class="date">Chưa có bài viết nào được đăng.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
