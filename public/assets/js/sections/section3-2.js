// Load SECTION3
fetch("../../app/views/pages/introduce/section3-2.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section3-2.php: ${res.status}`);
    }
    return res.text();
  })
  .then((data) => {
    const section3Container = document.getElementById("section3");
    if (!section3Container) {
      console.error("Section 3: Container element #section3 not found in DOM");
      return;
    }
    section3Container.innerHTML = data;

    // Khởi tạo section 3 events sau khi content tải xong
    // Delay để đảm bảo DOM fully rendered
    setTimeout(() => {
      initSection3Events();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section3-2.php:", error);
  });

// Hàm khởi tạo events cho Section 3
function initSection3Events() {
  // Chọn phần tử section3 được load từ fetch
  const section3 = document.getElementById("section3");
  const items = document.querySelectorAll(".list_rh_2 > li");
  const bgLayer = document.querySelector(".bg_rh_2");

  // Kiểm tra xem elements có tồn tại không
  if (!section3 || !items.length || !bgLayer) {
    console.warn(
      "Section 3 elements not found. Section3:",
      section3,
      "Items:",
      items.length,
      "BgLayer:",
      bgLayer,
    );
    return;
  }

  function activateItem(li) {
    // 1. Xóa class active của tất cả các item khác
    items.forEach((item) => {
      item.classList.remove("active");
    });

    // 2. Thêm class active cho item hiện tại
    li.classList.add("active");

    // 3. Thay đổi Background Image của container chính
    const newBg = li.getAttribute("data-bg");
    if (newBg) {
      bgLayer.style.backgroundImage = `url('${newBg}')`;
      console.log("Section 3: Background changed to:", newBg);
    }
  }

  // Khởi tạo item đầu tiên là active
  if (items.length > 0) {
    activateItem(items[0]);
  }

  // Lắng nghe sự kiện hover (mouseenter để active)
  items.forEach((li) => {
    li.addEventListener("mouseenter", function () {
      activateItem(this);
    });

    // Touch support cho mobile - activate on touch
    li.addEventListener(
      "touchstart",
      function (e) {
        // Không cần preventDefault vì không phải link
        activateItem(this);
      },
      { passive: true },
    );

    // Click support cho mobile devices
    li.addEventListener("click", function (e) {
      // Nếu là link, cho phép navigation
      const link = this.querySelector("a");
      if (link && e.target.closest("a")) {
        activateItem(this);
        // Link sẽ auto navigate
      }
    });
  });

  console.log(
    "Section 3 events initialized successfully. Items count:",
    items.length,
  );
}
