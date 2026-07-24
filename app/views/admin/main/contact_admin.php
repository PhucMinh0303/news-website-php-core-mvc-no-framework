<?php

/**
 * Contact Management View for Admin Panel
 * Outlook-style interface for managing audience feedback and story tips
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Contact Management</title>
  <!-- Font Awesome 6 (free icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Google Fonts: Inter for modern look -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">

</head>

<body>
  <main class="main">
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
        <div class="new-messages" id="newMessagesBadge">1 thư mới</div>
        <div class="tabs" id="tabsContainer">
          <div class="tab active" data-tab="inbox" role="tab" aria-selected="true">
            Thư mục (<span id="inboxCount">3</span>)
          </div>
          <div class="tab" data-tab="archive" role="tab" aria-selected="false">
            Lưu trữ (<span id="archiveCount">2</span>)
          </div>
          <div class="tab" data-tab="deleted" role="tab" aria-selected="false">
            Mục đã xóa (<span id="deletedCount">1</span>)
          </div>
        </div>

      </div>
    </div>

    <!-- MAIN CONTENT (Outlook style) -->
    <div class="container">
      <!-- INBOX LIST (visible by default) -->

      <div id="inboxListContainer" class="message-list">
        <!-- Message 1 - Michael Chen -->
        <div class="message-item active">
          <div class="message-item-header">
            <span class="sender-name">Michael Chen</span>
            <span class="message-date">APPLIED 7/11/2023</span>
          </div>
          <div class="message-item-email">m.chen@example.com</div>
          <div class="message-item-preview">Editorial - Don't show again</div>
          <div class="message-item-status">
            <span class="status-badge reviewing">
              <span class="dot"></span>REVIEWING
            </span>
            <span class="status-badge new">
              <span class="dot"></span>NEW
            </span>
          </div>
        </div>

        <!-- Message 2 - Emily Watson -->
        <div class="message-item">
          <div class="message-item-header">
            <span class="sender-name">Emily Watson</span>
            <span class="message-date">APPLIED 5/11/2023</span>
          </div>
          <div class="message-item-email">emily.w@example.com</div>
          <div class="message-item-preview">Don't show again</div>
          <div class="message-item-status">
            <span class="status-badge new">
              <span class="dot"></span>NEW
            </span>
          </div>
        </div>
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
      <div class="empty-panel" id="emptyPanel">
        Select a message to view the detailed reading pane.
      </div>

      <!-- RIGHT DETAIL PANEL -->
      <div class="detail-panel" id="detailPanel" style="display: none;">
        <div class="detail-header">
          <div class="tools">
            <button class="btn-icon" id="expandBtn" title="Expand"><i class="fa-solid fa-expand"></i></button>
            <button class="btn-icon" id="archiveMsgBtn" title="Archive"><i class="fa-solid fa-box-archive"></i></button>
            <button class="btn-icon" id="deleteMsgBtn" title="Delete"><i class="fa-solid fa-trash"></i></button>
          </div>
          <button class="reply-btn" id="replyBtn">Reply</button>
        </div>

        <div class="detail-subject" id="detailSubject">Story Tip: Local Council Corruption</div>

        <div class="detail-sender-info">
          <div class="sender-name-large" id="detailSenderName">John Doe</div>
          <div class="sender-email" id="detailSenderEmail">john@example.com</div>
          <div class="sender-to">To: NovaNews Support Team</div>
        </div>

        <div class="detail-divider"></div>

        <div class="message-content" id="detailContent">
          I have evidence of council members taking bribes for development projects. Please contact me securely.<br />
          This is a very sensitive matter and I would like to speak to someone in the investigative team specifically.
        </div>

        <hr />

        <div class="notes-section">
          <h4>INTERNAL ADMINISTRATIVE NOTES</h4>
          <div class="note-box" id="internalNoteBox">
            <strong>Alex Editor</strong> — 19:00 8/11/2023 <br />
            Spoke to legal about this. Need more verification.
          </div>
          <div class="add-note">
            <input type="text" id="newNoteInput" placeholder="Add a private note for editors..." />
            <button id="addNoteBtn">Add</button>
          </div>
        </div>

        <div class="metadata" id="metadataArea">
          <p><strong>Location:</strong> New York, USA (Mocked)</p>
          <p><strong>Platform:</strong> Chrome v122 / MacOS</p>
          <p><strong>IP Address:</strong> 192.168.1.104</p>
          <p><strong>Page Source:</strong> /stories/latest-news</p>
        </div>
      </div>
    </div>
  </main>
</body>
<script src="<?php echo View::asset('js/admin/contact-admin.js'); ?>"></script>

</html>