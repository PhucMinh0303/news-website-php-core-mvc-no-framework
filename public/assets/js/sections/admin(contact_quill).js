// Hàm xử lý contact (được gọi sau khi menu được tải)
function contactManager() {
  return {
    tab: "inbox",
    search: "",
    selectedMessage: null,
    newNote: "",

    messages: [
      {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
        title: "Story Tip: Local Council Corruption",
        date: "8/11/2023",
        content: "Sensitive investigation...",
        status: "inbox",
      },
    ],
    //Filter message theo search + tab
    get filtered() {
      let list = this.messages.filter((m) => m.status === this.tab);

      if (!this.search) return list;

      return list.filter(
        (m) =>
          m.name.toLowerCase().includes(this.search.toLowerCase()) ||
          m.title.toLowerCase().includes(this.search.toLowerCase()),
      );
    },
    // Animation khi click message
    openMessage(msg) {
      this.selectedMessage = msg;

      gsap.fromTo(
        ".detail-panel",
        { opacity: 0, x: 20 },
        { opacity: 1, x: 0, duration: 0.3 },
      );
    },
    // Animation khi move message
    animateRemove() {
      gsap.to(".detail-panel", {
        opacity: 0,
        y: 20,
        duration: 0.2,
      });
    },
    // Toast Notification
    toast(text) {
      Toastify({
        text: text,
        duration: 3000,
        gravity: "bottom",
        position: "center",
      }).showToast();
    },
    //Archive (move to archive)
    archiveMessage() {
      if (!this.selectedMessage) return;

      this.animateRemove();

      setTimeout(() => {
        this.selectedMessage.status = "archive";
        this.toast("Moved to Archive");
        this.autoSelect();
      }, 200);
    },
    // Delete (move to trash)
    deleteMessage() {
      if (!this.selectedMessage) return;

      this.animateRemove();

      setTimeout(() => {
        this.selectedMessage.status = "trash";
        this.toast("Moved to Trash");
        this.autoSelect();
      }, 200);
    },
    // Restore (move back to inbox)
    restoreMessage() {
      if (!this.selectedMessage) return;

      this.selectedMessage.status = "inbox";
      this.toast("Restored to Inbox");
      this.autoSelect();
    },
    // Auto select message (UX giống Gmail)
    autoSelect() {
      let list = this.filtered;
      this.selectedMessage = list.length ? list[0] : null;
    },

    addNote() {
      if (!this.newNote) return;

      this.selectedMessage.note = this.newNote;
      this.newNote = "";
    },
  };
}
