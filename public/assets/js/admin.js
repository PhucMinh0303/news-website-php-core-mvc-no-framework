// Hàm tải nội dung HTML vào một container
async function loadHTML(url, containerId) {
  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const html = await response.text();
    const container = document.getElementById(containerId);
    container.innerHTML = html;
    initQuillEditor(container);
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

/* QUILL EDITOR (Word-like functions) */

/**
 * Initialize Quill-based editor tooling in a given DOM scope.
 * This is called after dynamic HTML injection (AJAX) so it can
 * attach to newly-loaded editor instances.
 */
function initQuillEditor(scope = document) {
  const editorEl = scope.querySelector("#editor");
  if (!editorEl) return;

  // Prevent double initialization on the same element
  if (editorEl.__quillInstance) return;

  const quill = new Quill(editorEl, {
    theme: "snow",
    modules: {
      toolbar: false,
    },
  });

  editorEl.__quillInstance = quill;

  const contentInput = scope.querySelector("#contentInput");
  const syncContent = () => {
    if (contentInput) {
      contentInput.value = quill.root.innerHTML;
    }
  };

  // Keep hidden input in sync with editor content
  quill.on("text-change", syncContent);
  syncContent();

  // Basic formatting buttons (toggle on/off)
  const applyFormat = (selector, format) => {
    const button = scope.querySelector(selector);
    if (!button) return;

    button.addEventListener("click", () => {
      const currentFormat = quill.getFormat();
      const isActive = currentFormat[format] === true;

      quill.format(format, !isActive);
    });
  };

  applyFormat('[data-cmd="bold"]', "bold");
  applyFormat('[data-cmd="italic"]', "italic");
  applyFormat('[data-cmd="underline"]', "underline");

  // Headings
  scope.querySelectorAll("[data-heading]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tag = btn.dataset.heading;

      const headerValue = tag.startsWith("h")
        ? parseInt(tag.slice(1), 10)
        : parseInt(tag, 10);

      if (!Number.isFinite(headerValue)) return;

      const currentFormat = quill.getFormat();
      const isActive = currentFormat.header === headerValue;

      // toggle heading
      quill.format("header", isActive ? false : headerValue);
    });
  });
  // Font family
  const fontSelect = scope.querySelector("#fontFamily");
  if (fontSelect) {
    const Font = Quill.import("formats/font");
    Font.whitelist = [
      "serif",
      "arial",
      "times-new-roman",
      "courier-new",
      "roboto",
      "monospace",
    ];
    Quill.register(Font, true);

    fontSelect.addEventListener("change", function () {
      quill.format("font", this.value);
    });
  }

  // Text color
  const colorInput = scope.querySelector("#textColor");
  if (colorInput) {
    colorInput.addEventListener("input", function () {
      quill.format("color", this.value);
    });
  }

  // Link
  const addLinkBtn = scope.querySelector("#addLink");
  if (addLinkBtn) {
    addLinkBtn.addEventListener("click", () => {
      const url = prompt("Nhập link:");
      if (!url) return;
      const range = quill.getSelection();
      if (range) {
        quill.format("link", url);
      }
    });
  }

  // Image
  const imageUpload = scope.querySelector("#imageUpload");
  if (imageUpload) {
    const insertImageBtn = scope.querySelector("#insertImage");
    if (insertImageBtn) {
      insertImageBtn.addEventListener("click", () => {
        imageUpload.click();
      });
    }

    imageUpload.addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        const range = quill.getSelection(true);
        quill.insertEmbed(range.index, "image", e.target.result);
      };
      reader.readAsDataURL(file);
    });
  }

  // Video
  const insertVideoBtn = scope.querySelector("#insertVideo");

  if (insertVideoBtn) {
    insertVideoBtn.addEventListener("click", () => {
      const url = prompt("Nhập link video:");
      if (!url) return;

      const embedUrl = getVideoEmbedUrl(url);

      if (!embedUrl) {
        alert("Link video không hợp lệ");
        return;
      }

      const range = quill.getSelection(true);

      const videoHTML = `
      <div class="editor-video">
        <iframe 
          src="${embedUrl}" 
          frameborder="0"
          allowfullscreen>
        </iframe>
      </div>
    `;

      quill.clipboard.dangerouslyPasteHTML(range.index, videoHTML);
    });
  }
  function getVideoEmbedUrl(url) {
    // YouTube dạng: https://www.youtube.com/watch?v=xxxx
    let match = url.match(/youtube\.com.*v=([^&]+)/);
    if (match) {
      return `https://www.youtube.com/embed/${match[1]}`;
    }

    // YouTube dạng: https://youtu.be/xxxx
    match = url.match(/youtu\.be\/([^?]+)/);
    if (match) {
      return `https://www.youtube.com/embed/${match[1]}`;
    }

    // Vimeo
    match = url.match(/vimeo\.com\/(\d+)/);
    if (match) {
      return `https://player.vimeo.com/video/${match[1]}`;
    }

    return null;
  }
}

// Initialize on full page load (cover cases where editor is in the initial DOM)
document.addEventListener("DOMContentLoaded", () => {
  initQuillEditor();
});

// Hàm xử lý contact (được gọi sau khi menu được tải)
$(document).ready(function () {
  // =========================
  // 1. SWITCH TAB
  // =========================
  $(".tab").click(function () {
    $(".tab").removeClass("active");
    $(this).addClass("active");

    let tabId = $(this).attr("id");

    if (tabId === "tabInbox") {
      $("#inboxList").show();
      $("#archiveEmpty").hide();
    }

    if (tabId === "tabArchive") {
      $("#inboxList").hide();
      $("#archiveEmpty").show();
    }

    if (tabId === "tabDelete") {
      $("#inboxList").hide();
      $("#archiveEmpty").html("<h3>Recycle Bin Empty</h3>").show();
    }

    $("#detailPanel").hide();
    $("#emptyPanel").show();
  });

  // =========================
  // 2. OPEN MESSAGE
  // =========================
  $(".message-item").click(function () {
    $(".message-item").removeClass("active");
    $(this).addClass("active");

    $("#emptyPanel").hide();
    $("#detailPanel").show();
  });

  // =========================
  // 3. ARCHIVE BUTTON (TOP)
  // =========================
  $(".btn-archive").click(function () {
    let activeMsg = $(".message-item.active");

    if (activeMsg.length) {
      activeMsg.remove();

      alert("Moved to Archive");

      $("#detailPanel").hide();
      $("#emptyPanel").show();
    } else {
      alert("Please select a message");
    }
  });

  // =========================
  // 4. ARCHIVE ICON (RIGHT PANEL)
  // =========================
  $(".fa-box-archive").click(function () {
    let activeMsg = $(".message-item.active");

    if (activeMsg.length) {
      activeMsg.remove();

      alert("Archived successfully");

      $("#detailPanel").hide();
      $("#emptyPanel").show();
    }
  });

  // =========================
  // 5. DELETE MESSAGE
  // =========================
  $(".fa-trash").click(function () {
    let activeMsg = $(".message-item.active");

    if (activeMsg.length) {
      activeMsg.remove();

      alert("Deleted successfully");

      $("#detailPanel").hide();
      $("#emptyPanel").show();
    }
  });

  // =========================
  // 6. ADD NOTE
  // =========================
  $(".add-note button").click(function () {
    let input = $(".add-note input");
    let text = input.val().trim();

    if (text === "") return;

    let time = new Date().toLocaleString();

    let noteHtml = `
      <div class="note-box">
        <strong>You</strong> — ${time} <br />
        ${text}
      </div>
    `;

    $(".notes-section").append(noteHtml);

    input.val("");
  });

  // =========================
  // 7. SEARCH (basic)
  // =========================
  $(".search-box input").on("keyup", function () {
    let value = $(this).val().toLowerCase();

    $(".message-item").filter(function () {
      $(this).toggle($(this).text().toLowerCase().includes(value));
    });
  });
});
