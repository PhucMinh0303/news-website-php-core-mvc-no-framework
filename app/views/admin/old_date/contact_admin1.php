<?php
/**
 * contact management view for admin panel
 */
?>

<main class="main" id="contact-manager">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="search-box">
      <input type="text" id="search-messages" placeholder="Search feedback..." />
    </div>

    <div class="new-messages">
      <span id="new-messages-count">0</span> new messages
    </div>
  </div>

  <!-- HEADER -->
  <div class="contact-header">
    <div class="contact-header-left">
      <h1>Contact Feedback</h1>
      <p>Manage audience inquiries and story tips</p>
    </div>

    <div class="contact-header-right">
      <button class="btn-archive" id="jump-to-archive">Jump to Archive</button>

      <div class="tabs">
        <div class="tab active" data-tab="inbox">
          Inbox (<span id="inbox-count">0</span>)
        </div>

        <div class="tab" data-tab="archive">
          Archived
        </div>

        <div class="tab" data-tab="trash">
          Recycle Bin
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="container">
    <!-- LEFT: MESSAGE LIST -->
    <div id="message-list" class="message-list">
      <!-- messages will be inserted here by jQuery -->
    </div>

    <!-- EMPTY PANEL (shown when no message selected but list not empty) -->
    <div id="empty-panel" class="empty-panel" style="display: none;">
      Select a message to view the detailed reading pane.
    </div>

    <!-- RIGHT: DETAIL PANEL -->
    <div id="detail-panel" class="detail-panel" style="display: none;">
      <div class="detail-header">
        <div class="tools">
          <button class="btn-icon" id="btn-expand">
            <i class="fa-solid fa-expand"></i>
          </button>

          <button class="btn-icon" id="btn-archive" title="Archive">
            <i class="fa-solid fa-box-archive"></i>
          </button>

          <button class="btn-icon" id="btn-delete" title="Delete">
            <i class="fa-solid fa-trash"></i>
          </button>

          <button class="btn-icon" id="btn-restore" title="Restore" style="display: none;">
            ♻️
          </button>
        </div>

        <span></span>
        <button class="reply-btn" id="btn-reply">Reply</button>
      </div>

      <div class="detail-title" id="detail-title"></div>

      <div class="sender-info" id="sender-info"></div>

      <div class="message-content" id="message-content"></div>

      <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb" />

      <!-- NOTES -->
      <div class="notes-section">
        <h4>INTERNAL ADMINISTRATIVE NOTES</h4>

        <div id="note-display" class="note-box" style="display: none;">
          <strong>Admin</strong><br />
          <span id="note-text"></span>
        </div>

        <div class="add-note">
          <input type="text" id="note-input" placeholder="Add a private note..." />
          <button id="btn-add-note">Add</button>
        </div>
      </div>
    </div>
  </div>
</main>