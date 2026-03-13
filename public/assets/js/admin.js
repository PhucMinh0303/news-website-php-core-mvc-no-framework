// Hàm tải nội dung HTML vào một container
async function loadHTML(url, containerId) {
  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const html = await response.text();
    document.getElementById(containerId).innerHTML = html;
  } catch (error) {
    console.error("Lỗi tải file:", error);
    document.getElementById(containerId).innerHTML =
      `<p style="color:red">Không thể tải nội dung từ ${url}</p>`;
  }
}

// Tải menu và main mặc định khi trang load
window.addEventListener("load", async () => {
  const menuUrl = window.ADMIN_URLS?.menu || "menu/menu.html";
  const mainBaseUrl = (window.ADMIN_URLS?.main || "main/main.html").replace(
    /\/+$/,
    "",
  );

  await loadHTML(menuUrl, "menu-container");
  await loadHTML(mainBaseUrl, "main-container");

  // Sau khi menu được tải, gắn sự kiện click cho các mục
  attachMenuEvents(mainBaseUrl);
});

// Hàm xử lý click menu
function attachMenuEvents(mainBaseUrl) {
  const menuItems = document.querySelectorAll(".menu-item");
  const mainContainer = document.getElementById("main-container");

  const buildMainUrl = (page) => {
    if (!page || page === "main") return mainBaseUrl;

    // If base URL points to an HTML file, keep using the .html convention.
    if (mainBaseUrl.endsWith(".html")) {
      return `${mainBaseUrl.replace(/\.html$/, "")}/${page}.html`;
    }

    // Otherwise, treat it as a route base
    return `${mainBaseUrl.replace(/\/+$/, "")}/${page}`;
  };

  menuItems.forEach((item) => {
    item.addEventListener("click", async (e) => {
      e.preventDefault();

      // Bỏ class active khỏi tất cả menu items
      menuItems.forEach((i) => i.classList.remove("active"));

      // Thêm class active cho item được click
      item.classList.add("active");

      // Lấy tên trang từ data-page
      const page = item.dataset.page; // ví dụ: "articles", "contact", ...

      // Xây dựng đường dẫn file main tương ứng
      const mainFile = buildMainUrl(page);

      // Tải nội dung mới vào main-container
      await loadHTML(mainFile, "main-container");
    });
  });

  // Hỗ trợ chuyển trang dùng các nút/các thành phần khác có data-page (ví dụ: nút "Create Articles")
  document.addEventListener("click", async (e) => {
    const target = e.target.closest("[data-page]");
    if (!target) return;

    // Tránh xử lý lại cho menu-item (đã có handler riêng)
    if (target.classList.contains("menu-item")) return;

    e.preventDefault();
    const page = target.dataset.page;
    const mainFile = buildMainUrl(page);
    await loadHTML(mainFile, "main-container");
  });
}
// Hàm xử lý contact (được gọi sau khi menu được tải)
// ================= CONTACT SYSTEM =================

let selectedMessage = null;

/* OPEN MESSAGE */

function openMessage(element) {
  selectedMessage = element;

  const detail = document.getElementById("detailPanel");
  const empty = document.getElementById("emptyPanel");

  if (detail) detail.style.display = "block";
  if (empty) empty.style.display = "none";

  document
    .querySelectorAll(".message-item")
    .forEach((item) => item.classList.remove("active"));

  element.classList.add("active");
}

/* MOVE MESSAGE TO ARCHIVE */

function moveToArchive() {
  if (!selectedMessage) return;

  const archiveList = document.getElementById("archiveList");

  archiveList.appendChild(selectedMessage);

  selectedMessage.classList.remove("active");
  selectedMessage = null;

  updateInboxCount();
  checkArchiveEmpty();
}

/* CLICK BUTTON ARCHIVE */

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("btn-archive")) {
    moveToArchive();
  }
});

/* TAB SWITCHING */

document.addEventListener("click", function (e) {
  if (!e.target.classList.contains("tab")) return;

  const tabs = document.querySelectorAll(".tab");
  tabs.forEach((t) => t.classList.remove("active"));

  e.target.classList.add("active");

  const inbox = document.getElementById("inboxList");
  const archive = document.getElementById("archiveList");

  if (!inbox || !archive) return;

  if (e.target.id === "tabInbox") {
    inbox.style.display = "block";
    archive.style.display = "none";
  }

  if (e.target.id === "tabArchive") {
    inbox.style.display = "none";
    archive.style.display = "block";
  }
});

/* UPDATE INBOX COUNT */

function updateInboxCount() {
  const inbox = document.getElementById("inboxList");
  const count = inbox ? inbox.children.length : 0;

  const inboxTab = document.getElementById("tabInbox");

  if (inboxTab) {
    inboxTab.innerText = "Inbox (" + count + ")";
  }
}

/* CHECK ARCHIVE EMPTY */

function checkArchiveEmpty() {
  const archive = document.getElementById("archiveList");
  const empty = document.getElementById("archiveEmpty");

  if (!archive || !empty) return;

  if (archive.children.length === 0) {
    empty.style.display = "block";
  } else {
    empty.style.display = "none";
  }
}
/* JavaScript Editor (Word-like functions) */
document.addEventListener("DOMContentLoaded", function () {
  const editor = document.getElementById("editor");

  /* ===== BASIC COMMANDS ===== */

  document.querySelectorAll("[data-cmd]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const cmd = btn.dataset.cmd;
      const value = btn.dataset.value || null;

      document.execCommand(cmd, false, value);
    });
  });

  /* ===== HEADINGS ===== */

  document.querySelectorAll("[data-heading]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tag = btn.dataset.heading;

      document.execCommand("formatBlock", false, tag);
    });
  });

  /* ===== FONT FAMILY ===== */

  document.getElementById("fontFamily").addEventListener("change", function () {
    wrapSelection("span", { fontFamily: this.value });
  });

  /* ===== TEXT STYLE ===== */

  document.querySelector('[data-cmd="bold"]').onclick = () => {
    wrapSelection("b");
  };

  document.querySelector('[data-cmd="italic"]').onclick = () => {
    wrapSelection("i");
  };

  document.querySelector('[data-cmd="underline"]').onclick = () => {
    wrapSelection("span", { textDecoration: "underline" });
  };

  /* ===== TEXT COLOR ===== */

  document.getElementById("textColor").addEventListener("input", function () {
    wrapSelection("span", { color: this.value });
  });

  /* ===== ADD LINK ===== */

  document.getElementById("addLink").addEventListener("click", function () {
    const url = prompt("Nhập link:");

    if (url) {
      document.execCommand("createLink", false, url);
    }
  });

  /* ===== INSERT IMAGE ===== */

  document.getElementById("insertImage").onclick = () => {
    document.getElementById("imageUpload").click();
  };

  document
    .getElementById("imageUpload")
    .addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();

      reader.onload = function (e) {
        const img = document.createElement("img");

        img.src = e.target.result;
        img.style.maxWidth = "100%";

        const range = getSelectionRange();

        if (range) {
          range.insertNode(img);
        } else {
          editor.appendChild(img);
        }
      };

      reader.readAsDataURL(file);
    });

  /* ===== INSERT VIDEO ===== */

  document.getElementById("insertVideo").onclick = () => {
    const url = prompt("Nhập link video");

    if (!url) return;

    let element;

    if (url.includes("youtube")) {
      const embed = url.replace("watch?v=", "embed/");

      element = document.createElement("iframe");

      element.src = embed;
      element.width = "560";
      element.height = "315";
      element.allowFullscreen = true;
    } else {
      element = document.createElement("video");

      element.src = url;
      element.controls = true;
      element.style.maxWidth = "100%";
    }

    const range = getSelectionRange();

    if (range) {
      range.insertNode(element);
    } else {
      editor.appendChild(element);
    }
  };
});
