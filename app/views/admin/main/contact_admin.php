<?php

/**
 * Contact Management View for Admin Panel
 * Outlook-style interface for managing audience feedback and story tips
 */
$contacts = $contacts ?? [];
$stats = $stats ?? [];
$archivedContacts = array_values(array_filter($contacts, static function ($contact) {
  return ($contact['status'] ?? '') === 'archived';
}));
$inboxContacts = array_values(array_filter($contacts, static function ($contact) {
  return !in_array($contact['status'] ?? '', ['spam', 'archived'], true);
}));
$deletedContacts = array_values(array_filter($contacts, static function ($contact) {
  return ($contact['status'] ?? '') === 'spam';
}));
$selectedContact = $inboxContacts[0] ?? ($archivedContacts[0] ?? ($deletedContacts[0] ?? null));
$selectedContactStatus = $selectedContact['status'] ?? 'new';
$selectedTab = $selectedContactStatus === 'spam' ? 'deleted' : ($selectedContactStatus === 'archived' ? 'archive' : 'inbox');
$selectedContactId = (int) ($selectedContact['id'] ?? 0);
$escapeContactValue = static function ($value) {
  return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<main class="main" id="contactAdmin" data-status-url="<?php echo View::escape(View::url('admin/contact/update')); ?>">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="contact-header-left">
      <h1>Thư phản hồi</h1>

    </div>
  </div>


  <!-- HEADER -->
  <div class="contact-header">
    <div class="contact-header-left">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Tìm trong thư..." />
      </div>
    </div>


    <div class="contact-header-right">
      <div class="new-messages" id="newMessagesBadge"><?php echo (int) ($stats['new'] ?? 0); ?> thư mới</div>
      <div class="tabs" id="tabsContainer">
        <div class="tab <?php echo $selectedTab === 'inbox' ? 'active' : ''; ?>" data-tab="inbox" role="tab" aria-selected="<?php echo $selectedTab === 'inbox' ? 'true' : 'false'; ?>">
          Thư mục (<span id="inboxCount"><?php echo count($inboxContacts); ?></span>)
        </div>
        <div class="tab <?php echo $selectedTab === 'archive' ? 'active' : ''; ?>" data-tab="archive" role="tab" aria-selected="<?php echo $selectedTab === 'archive' ? 'true' : 'false'; ?>">
          Lưu trữ (<span id="archiveCount"><?php echo count($archivedContacts); ?></span>)
        </div>
        <div class="tab <?php echo $selectedTab === 'deleted' ? 'active' : ''; ?>" data-tab="deleted" role="tab" aria-selected="<?php echo $selectedTab === 'deleted' ? 'true' : 'false'; ?>">
          Mục đã xóa (<span id="deletedCount"><?php echo count($deletedContacts); ?></span>)
        </div>
      </div>

    </div>
  </div>

  <!-- MAIN CONTENT (Outlook style) -->
  <div class="container">
    <!-- INBOX LIST (visible by default) -->

    <div id="inboxListContainer" class="message-list">
      <?php foreach ($contacts as $index => $contact): ?>
        <?php
        $contactStatus = $contact['status'] ?? 'new';
        $contactTab = $contactStatus === 'spam' ? 'deleted' : ($contactStatus === 'archived' ? 'archive' : 'inbox');
        ?>
        <div class="message-item <?php echo (int) $contact['id'] === $selectedContactId ? 'active' : ''; ?>"
          data-message-id="<?php echo (int)$contact['id']; ?>" data-tab="<?php echo $contactTab; ?>">
          <div class="message-item-header">
            <span class="sender-name"><?php echo htmlspecialchars($contact['customer_name'] ?? ''); ?></span>
            <span class="message-date"><?php echo $escapeContactValue($contact['created_at'] ?? $contact['date_sent'] ?? ''); ?></span>
          </div>
          <div class="message-item-email"><?php echo htmlspecialchars($contact['email'] ?? ''); ?></div>
          <div class="message-item-preview"><?php echo htmlspecialchars($contact['content'] ?? ''); ?></div>
          <div class="message-item-status">
            <span class="status-badge <?php echo htmlspecialchars($contactStatus); ?>">
              <span class="dot"></span><?php echo htmlspecialchars(strtoupper($contactStatus)); ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- ARCHIVE EMPTY PLACEHOLDER (hidden) -->
    <div id="archiveEmpty" style="display:none;">
      <h3>YOUR ARCHIVE IS EMPTY</h3>
      <p>There are no messages to display here right now.</p>
    </div>

    <!-- DELETED EMPTY PLACEHOLDER (hidden) -->
    <div id="deletedEmpty" style="display:none;">
      <h3>RECYCLE BIN IS EMPTY</h3>
      <p>Deleted messages appear here.</p>
    </div>

    <!-- EMPTY PANEL (no message selected) -->
    <div class="empty-panel" id="emptyPanel" style="display: <?php echo $selectedContact ? 'none' : 'block'; ?>;">
      Chưa có thư liên hệ.
    </div>

    <!-- RIGHT DETAIL PANEL -->
    <div class="detail-panel" id="detailPanel" style="display: <?php echo $selectedContact ? 'block' : 'none'; ?>;">
      <div class="detail-header">
        <div class="tools">
          <button class="btn-icon" id="expandBtn" title="Mở rộng"><i class="fa-solid fa-expand"></i></button>
          <button class="btn-icon" id="archiveMsgBtn" title="Lưu trữ"><i class="fa-solid fa-box-archive"></i></button>
          <button class="btn-icon" id="deleteMsgBtn" title="Xoá"><i class="fa-solid fa-trash"></i></button>
        </div>
        <button class="reply-btn" id="replyBtn">Reply</button>
      </div>
      <div class="detail-title" id="detailTitle">
        <div class="detail-subject" id="detailSubject"><?php echo $escapeContactValue($selectedContact['contact_type'] ?? 'Liên hệ'); ?></div>

        <div class="detail-sender-info">
          <div class="sender-name-large" id="detailSenderName"><?php echo $escapeContactValue($selectedContact['customer_name'] ?? ''); ?></div>
          <div class="sender-email" id="detailSenderEmail"><?php echo $escapeContactValue($selectedContact['email'] ?? ''); ?></div>
          <div class="sender-phone" id="detailSenderPhone"><?php echo $escapeContactValue($selectedContact['phone'] ?? ''); ?></div>
          <div class="sender-to" id="detailStatus">Trạng thái: <?php echo $escapeContactValue($selectedContact['status'] ?? 'new'); ?></div>
        </div>

        <div class="detail-divider"></div>

        <div class="message-content" id="detailContent"><?php echo nl2br($escapeContactValue($selectedContact['content'] ?? '')); ?></div>

        <hr />

        <div class="notes-section">
          <h4>PHẢN HỒI QUẢN TRỊ</h4>
          <div class="note-box" id="internalNoteBox"><?php echo nl2br($escapeContactValue($selectedContact['response_content'] ?? 'Chưa có phản hồi.')); ?></div>
        </div>

        <div class="metadata" id="metadataArea">
          <p><strong>IP:</strong> <span id="detailIpAddress"><?php echo $escapeContactValue($selectedContact['ip_address'] ?? ''); ?></span></p>
          <p><strong>Thiết bị:</strong> <span id="detailUserAgent"><?php echo $escapeContactValue($selectedContact['user_agent'] ?? ''); ?></span></p>
          <p><strong>Trang gửi:</strong> <span id="detailPageUrl"><?php echo $escapeContactValue($selectedContact['page_url'] ?? ''); ?></span></p>
          <p><strong>Thời gian:</strong> <span id="detailCreatedAt"><?php echo $escapeContactValue($selectedContact['created_at'] ?? $selectedContact['date_sent'] ?? ''); ?></span></p>
        </div>
      </div>

    </div>
  </div>
</main>

<script>
  window.ADMIN_CONTACTS = <?php echo json_encode($contacts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
</script>