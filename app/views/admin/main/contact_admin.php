<?php
/**
 * contact management view for admin panel
 */
?>

<main class="main" x-data="contactManager()" x-init="autoSelect()">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="search-box">
      <input type="text" placeholder="Search feedback..." x-model="search" />
    </div>

    <div class="new-messages">
      <span x-text="messages.filter(m=>m.status==='inbox').length"></span> new
      messages
    </div>
  </div>

  <!-- HEADER -->
  <div class="contact-header">
    <div class="contact-header-left">
      <h1>Contact Feedback</h1>
      <p>Manage audience inquiries and story tips</p>
    </div>

    <div class="contact-header-right">
      <button class="btn-archive" @click="tab='archive'">
        Jump to Archive
      </button>

      <div class="tabs">
        <div
          class="tab"
          :class="{active:tab==='inbox'}"
          @click="tab='inbox'; autoSelect()"
        >
          Inbox (<span
            x-text="messages.filter(m=>m.status==='inbox').length"
          ></span
          >)
        </div>

        <div
          class="tab"
          :class="{active:tab==='archive'}"
          @click="tab='archive'; autoSelect()"
        >
          Archived
        </div>

        <div
          class="tab"
          :class="{active:tab==='trash'}"
          @click="tab='trash'; autoSelect()"
        >
          Recycle Bin
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="container">
    <!-- LEFT: MESSAGE LIST -->
    <div class="message-list">
      <template x-if="filtered.length===0">
        <div class="empty-panel">No messages found.</div>
      </template>

      <template x-for="msg in filtered" :key="msg.id">
        <div
          class="message-item"
          :class="{active:selectedMessage?.id===msg.id}"
          @click="openMessage(msg)"
        >
          <h3 x-text="msg.name"></h3>
          <div class="title" x-text="msg.title"></div>
          <div class="date" x-text="msg.date"></div>
        </div>
      </template>
    </div>

    <!-- EMPTY PANEL -->
    <div class="empty-panel" x-show="!selectedMessage && filtered.length>0">
      Select a message to view the detailed reading pane.
    </div>

    <!-- RIGHT: DETAIL PANEL -->
    <div class="detail-panel" x-show="selectedMessage">
      <div class="detail-header">
        <div class="tools">
          <button class="btn-icon">
            <i class="fa-solid fa-expand"></i>
          </button>

          <button class="btn-icon" title="Archive" @click="archiveMessage()">
            <i class="fa-solid fa-box-archive"></i>
          </button>

          <button class="btn-icon" title="Delete" @click="deleteMessage()">
            <i class="fa-solid fa-trash"></i>
          </button>

          <button
            class="btn-icon"
            title="Restore"
            x-show="tab==='trash'"
            @click="restoreMessage()"
          >
            ♻️
          </button>
        </div>

        <span></span>
        <button class="reply-btn">Reply</button>
      </div>

      <div class="detail-title" x-text="selectedMessage?.title"></div>

      <div class="sender-info">
        <span x-text="selectedMessage?.name"></span> —
        <span x-text="selectedMessage?.email"></span><br />
        <span x-text="selectedMessage?.date"></span>
      </div>

      <div class="message-content" x-text="selectedMessage?.content"></div>

      <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb" />

      <!-- NOTES -->
      <div class="notes-section">
        <h4>INTERNAL ADMINISTRATIVE NOTES</h4>

        <div class="note-box" x-show="selectedMessage?.note">
          <strong>Admin</strong><br />
          <span x-text="selectedMessage?.note"></span>
        </div>

        <div class="add-note">
          <input
            type="text"
            placeholder="Add a private note..."
            x-model="newNote"
          />
          <button @click="addNote()">Add</button>
        </div>
      </div>
    </div>
  </div>
</main>
