<?php
/**
 * contact management view for admin panel
 */
?>

<main class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="search-box">
      <input type="text" placeholder="Search feedback..." />
    </div>
    <div class="new-messages">1 new messages</div>
  </div>

  <!-- HEADER -->
  <div class="contact-header">
    <div class="contact-header-left">
      <h1>Contact Feedback</h1>
      <p>Manage audience inquiries and story tips</p>
    </div>

    <div class="contact-header-right">
      <button class="btn-archive" onclick="moveToArchive()">
        Jump to Archive
      </button>

      <div class="tabs">
        <div class="tab active" id="tabInbox">Inbox (1)</div>
        <div class="tab" id="tabArchive">Archived</div>
        <div class="tab" id="tabDelete">Recycle Bin</div>
      </div>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="container">
    <!-- LEFT: MESSAGE LIST -->
    <div class="message-list" id="inboxList">
      <div class="message-item" onclick="openMessage(this)">
        <h3>John Doe</h3>
        <div class="title">Story Tip: Local Council Corruption</div>
        <div class="date">8/11/2023</div>
      </div>
    </div>
    <div class="message-list" id="archiveList" style="display: none"></div>
    <div class="empty-panel" id="emptyPanel">
      Select a message to view the detailed reading pane.
    </div>

    <!-- RIGHT: DETAIL PANEL -->
    <div class="detail-panel" id="detailPanel">
      <div class="detail-header">
        <div class="tools">
          <button class="btn-icon" title="Expand">
            <i class="fa-solid fa-expand"></i>
          </button>

          <button class="btn-icon" title="Archive">
            <i class="fa-solid fa-box-archive"></i>
          </button>
          <button class="btn-icon" title="Delete">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
        <span></span>
        <button class="reply-btn">Reply</button>
      </div>

      <div class="detail-title">Story Tip: Local Council Corruption</div>

      <div class="sender-info">
        John Doe — john@example.com <br />
        8/11/2023 17:15
      </div>

      <div class="message-content">
        I have evidence of council members taking bribes for development
        projects. Please contact me securely. This is a very sensitive matter
        and I would like to speak to someone in the investigative team
        specifically.
      </div>

      <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb" />

      <!-- INTERNAL NOTES -->
      <div class="notes-section">
        <h4>INTERNAL ADMINISTRATIVE NOTES</h4>

        <div class="note-box">
          <strong>Alex Editor</strong> — 19:00 8/11/2023 <br />
          Spoke to legal about this. Need more verification.
        </div>

        <div class="add-note">
          <input type="text" placeholder="Add a private note for editors..." />
          <button>Add</button>
        </div>
      </div>

      <!-- CONTEXT METADATA -->
      <div class="metadata">
        <p><strong>Location:</strong> New York, USA (Mocked)</p>
        <p><strong>Platform:</strong> Chrome v122 / MacOS</p>
        <p><strong>IP Address:</strong> 192.168.1.104</p>
        <p><strong>Page Source:</strong> /stories/latest-news</p>
      </div>
    </div>
  </div>
</main>
