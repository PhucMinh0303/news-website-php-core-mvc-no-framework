// Hàm tải nội dung HTML vào một container
async function loadHTML(url, containerId) {
  try {
    const response = await fetch(url, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const html = await response.text();
    const container = document.getElementById(containerId);
    container.innerHTML = html;
    window.recruitmentForm?.init?.(container);
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
  const initialPage = window.ADMIN_INITIAL_PAGE || "dashboard";
  const initialMainUrl =
    initialPage === "dashboard" ? mainBaseUrl : `${mainBaseUrl}/${initialPage}`;

  const menuContainer = document.getElementById("menu-container");
  const mainContainer = document.getElementById("main-container");

  if (menuContainer && !menuContainer.innerHTML.trim()) {
    await loadHTML(menuUrl, "menu-container");
  }
  if (mainContainer && !mainContainer.innerHTML.trim()) {
    await loadHTML(initialMainUrl, "main-container");
  }

  // Sau khi menu được tải, gắn sự kiện click cho các mục
  attachMenuEvents(mainBaseUrl);
});

// Hàm xử lý click menu
function attachMenuEvents(mainBaseUrl) {
  const menuItems = document.querySelectorAll(".menu-item");

  const setActiveMenu = (page) => {
    menuItems.forEach((item) => {
      item.classList.toggle("active", item.dataset.page === page);
    });
  };

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

      const link = item.querySelector("a[href]");

      // Bỏ class active khỏi tất cả menu items
      menuItems.forEach((i) => i.classList.remove("active"));

      // Thêm class active cho item được click
      item.classList.add("active");

      // Lấy tên trang từ data-page
      const page = item.dataset.page; // ví dụ: "articles", "contact", ...

      // Xây dựng đường dẫn file main tương ứng
      const mainFile = item.dataset.url || link?.href || buildMainUrl(page);

      if (link && window.location.href !== link.href) {
        window.history.pushState({ page }, "", link.href);
      }

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
    const mainFile = target.dataset.mainUrl || buildMainUrl(page);

    if (target.dataset.menuPage) {
      setActiveMenu(target.dataset.menuPage);
    }

    if (target.dataset.url && window.location.href !== target.dataset.url) {
      window.history.pushState({ page }, "", target.dataset.url);
    }

    await loadHTML(mainFile, "main-container");
  });
}




