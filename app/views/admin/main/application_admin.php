<?php
$applications = $applications ?? [];
$statusLabels = [
  'pending' => 'Chờ xem xét',
  'reviewed' => 'Đã xem xét',
  'interviewed' => 'Phỏng vấn',
  'accepted' => 'Đã nhận',
  'rejected' => 'Từ chối',
  'archived' => 'Lưu trữ',
  'deleted' => 'Đã xóa',
];
$getApplicationTab = static function ($application) {
  $status = $application['status'] ?? 'pending';
  return $status === 'deleted' ? 'deleted' : ($status === 'archived' ? 'archive' : 'inbox');
};
$escapeApplicationValue = static function ($value) {
  return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};
$inboxApplications = array_values(array_filter($applications, static function ($application) use ($getApplicationTab) {
  return $getApplicationTab($application) === 'inbox';
}));
$archivedApplications = array_values(array_filter($applications, static function ($application) use ($getApplicationTab) {
  return $getApplicationTab($application) === 'archive';
}));
$deletedApplications = array_values(array_filter($applications, static function ($application) use ($getApplicationTab) {
  return $getApplicationTab($application) === 'deleted';
}));
$selectedApplication = $inboxApplications[0] ?? ($archivedApplications[0] ?? ($deletedApplications[0] ?? null));
$selectedTab = $selectedApplication ? $getApplicationTab($selectedApplication) : 'inbox';
$selectedApplicationId = (int) ($selectedApplication['id'] ?? 0);
$pendingApplications = array_filter($inboxApplications, static function ($application) {
  return ($application['status'] ?? 'pending') === 'pending';
});
$getCvUrl = static function ($filename) {
  $filename = basename((string) $filename);
  if ($filename === '') {
    return '';
  }

  $path = ROOT_PATH . 'public/uploads/cvs/' . $filename;
  return is_file($path) ? BASE_URL . 'public/uploads/cvs/' . rawurlencode($filename) : '';
};
$adminApplications = array_map(static function ($application) use ($getCvUrl) {
  $application['cv_url'] = $getCvUrl($application['cv_file'] ?? '');
  return $application;
}, $applications);
?>
<main class="main" id="applicationAdmin" data-status-url="<?php echo $escapeApplicationValue(View::url('admin/application/update')); ?>">
  <div class="topbar">
    <div class="contact-header-left">
      <h1>Đơn ứng tuyển</h1>
    </div>
  </div>

  <div class="contact-header">
    <div class="contact-header-left">
      <div class="search-box">
        <input type="text" id="applicationSearchInput" placeholder="Tìm hồ sơ ứng tuyển..." />
      </div>
    </div>

    <div class="contact-header-right">
      <div class="new-messages" id="newMessagesBadge"><?php echo count($pendingApplications); ?> hồ sơ mới</div>
      <div class="tabs" id="tabsContainer">
        <div class="tab <?php echo $selectedTab === 'inbox' ? 'active' : ''; ?>" data-tab="inbox" role="tab" aria-selected="<?php echo $selectedTab === 'inbox' ? 'true' : 'false'; ?>">
          Thư mục (<span id="inboxCount"><?php echo count($inboxApplications); ?></span>)
        </div>
        <div class="tab <?php echo $selectedTab === 'archive' ? 'active' : ''; ?>" data-tab="archive" role="tab" aria-selected="<?php echo $selectedTab === 'archive' ? 'true' : 'false'; ?>">
          Lưu trữ (<span id="archiveCount"><?php echo count($archivedApplications); ?></span>)
        </div>
        <div class="tab <?php echo $selectedTab === 'deleted' ? 'active' : ''; ?>" data-tab="deleted" role="tab" aria-selected="<?php echo $selectedTab === 'deleted' ? 'true' : 'false'; ?>">
          Mục đã xóa (<span id="deletedCount"><?php echo count($deletedApplications); ?></span>)
        </div>
      </div>

    </div>
  </div>

  <div class="container">
    <div id="applicationListContainer" class="message-list">
      <?php foreach ($applications as $application): ?>
        <?php
        $applicationStatus = $application['status'] ?? 'pending';
        $applicationName = $application['full_name'] ?? '';
        $applicationEmail = $application['email'] ?? '';
        $applicationJob = $application['recruitment_title'] ?? 'Tin tuyển dụng không còn tồn tại';
        $applicationDate = $application['created_at'] ?? '';
        $applicationContent = $application['content'] ?? '';
        ?>
        <div
          class="message-item <?php echo (int) ($application['id'] ?? 0) === $selectedApplicationId ? 'active' : ''; ?>"
          data-application-id="<?php echo (int) ($application['id'] ?? 0); ?>" data-tab="<?php echo $getApplicationTab($application); ?>">
          <div class="message-item-header">
            <span class="sender-name"><?php echo $escapeApplicationValue($applicationName); ?></span>
            <span class="message-date"><?php echo $escapeApplicationValue($applicationDate); ?></span>
          </div>
          <div class="message-item-email"><?php echo $escapeApplicationValue($applicationEmail); ?></div>
          <div class="message-item-preview"><?php echo $escapeApplicationValue('Tin tuyển dụng: ' . $applicationJob . ($applicationContent !== '' ? ' · ' . $applicationContent : '')); ?></div>
          <div class="message-item-status">
            <span class="status-badge status-<?php echo $escapeApplicationValue($applicationStatus); ?>">
              <span class="dot"></span><?php echo $escapeApplicationValue($statusLabels[$applicationStatus] ?? $applicationStatus); ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="empty-panel" id="applicationEmptyPanel" style="display: <?php echo $selectedApplication ? 'none' : 'flex'; ?>;">
      <?php echo $applications ? 'Không tìm thấy hồ sơ phù hợp.' : 'Chưa có hồ sơ ứng tuyển.'; ?>
    </div>

    <div class="detail-panel" id="applicationDetailPanel" style="display: <?php echo $selectedApplication ? 'flex' : 'none'; ?>;">
      <div class="detail-header">
        <div class="tools">
          <button class="btn-icon" id="expandBtn" title="Mở rộng"><i class="fa-solid fa-expand"></i></button>
          <button class="btn-icon" id="archiveMsgBtn" title="<?php echo $selectedTab === 'archive' ? 'quay về thư mục' : 'Lưu trữ'; ?>"><i class="fa-solid <?php echo $selectedTab === 'archive' ? 'fa-reply' : 'fa-box-archive'; ?>"></i></button>
          <button class="btn-icon" id="deleteMsgBtn" title="Xoá"><i class="fa-solid fa-trash"></i></button>
        </div>
        <p>
          <a
            class="reply-btn"
            id="applicationCvLink"
            href="<?php echo $escapeApplicationValue($getCvUrl($selectedApplication['cv_file'] ?? '')); ?>"
            target="_blank"
            rel="noopener"
            style="<?php echo $getCvUrl($selectedApplication['cv_file'] ?? '') ? '' : 'display:none;'; ?>">Mở CV</a>
        </p>
      </div>

      <div class="detail-title">
        <div class="detail-subject" id="applicationJobTitle"><?php echo $escapeApplicationValue($selectedApplication['recruitment_title'] ?? 'Tin tuyển dụng không còn tồn tại'); ?></div>
        <div class="detail-sender-info">
          <div class="sender-name-large" id="applicationName"><?php echo $escapeApplicationValue($selectedApplication['full_name'] ?? ''); ?></div>
          <div class="sender-email" id="applicationEmail"><?php echo $escapeApplicationValue($selectedApplication['email'] ?? ''); ?></div>
          <div class="sender-to" id="applicationPhone"><?php echo $escapeApplicationValue($selectedApplication['phone'] ?? ''); ?></div>
          <div class="sender-to" id="applicationStatus">
            Trạng thái: <?php echo $escapeApplicationValue($statusLabels[$selectedApplication['status'] ?? 'pending'] ?? ($selectedApplication['status'] ?? 'pending')); ?>
          </div>
        </div>

        <div class="detail-divider"></div>



        <div class="message-content" id="applicationContent"><?php echo nl2br($escapeApplicationValue($selectedApplication['content'] ?? '')); ?></div>
        <hr />

        <div class="notes-section">
          <h4>GHI CHÚ</h4>
          <div class="note-box" id="applicationNotes"><?php echo nl2br($escapeApplicationValue($selectedApplication['notes'] ?? 'Chưa có ghi chú.')); ?></div>
        </div>

        <div class="metadata">
          <p><strong>Mã hồ sơ:</strong> <span id="applicationId"><?php echo $selectedApplicationId; ?></span></p>
          <p><strong>Ngày ứng tuyển:</strong> <span id="applicationDate"><?php echo $escapeApplicationValue($selectedApplication['created_at'] ?? ''); ?></span></p>
        </div>
      </div>
    </div>
  </div>
  <script type="application/json" id="adminApplicationsData">
    <?php echo json_encode($adminApplications, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>
  </script>
</main>