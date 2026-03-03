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
  await loadHTML("menu/menu.html", "menu-container");
  await loadHTML("main/main.html", "main-container");

  // Sau khi menu được tải, gắn sự kiện click cho các mục
  attachMenuEvents();
});

// Hàm xử lý click menu
function attachMenuEvents() {
  const menuItems = document.querySelectorAll(".menu-item");
  const mainContainer = document.getElementById("main-container");

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
      let mainFile = "main/main.html"; // mặc định
      if (page && page !== "main") {
        mainFile = `main/${page}.html`;
      }

      // Tải nội dung mới vào main-container
      await loadHTML(mainFile, "main-container");
    });
  });
}
