// asset-manegerment
fetch("../../app/views/pages/product-service/product-service-section.php")
  .then((res) => {
    if (!res.ok)
      throw new Error(`Failed to load asset-manegerment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("product-service-section");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// asset-manegerment
fetch("../../app/views/pages/product-service/asset-manegerment.php")
  .then((res) => {
    if (!res.ok)
      throw new Error(`Failed to load asset-manegerment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("asset-manegerment");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// portfolio-management
fetch("../../app/views/pages/product-service/portfolio-management.php")
  .then((res) => {
    if (!res.ok)
      throw new Error(`Failed to load portfolio-management: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("portfolio-management");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// business-management-consulting
fetch(
  "../../app/views/pages/product-service/business-management-consulting.php"
)
  .then((res) => {
    if (!res.ok)
      throw new Error(
        `Failed to load business-management-consulting: ${res.status}`
      );
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("Business-management-consulting");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// M&A-Project-Consulting
fetch("../../app/views/pages/product-service/m&a-project-consulting.php")
  .then((res) => {
    if (!res.ok)
      throw new Error(`Failed to load m&a-project-consulting: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("m&a-project-consulting");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// m&a-and-corporate-restructuring
fetch(
  "../../app/views/pages/product-service/m&a-and-corporate-restructuring.php"
)
  .then((res) => {
    if (!res.ok)
      throw new Error(
        `Failed to load M&A-and-corporate-restructuring: ${res.status}`
      );
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("M&A-and-corporate-restructuring");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// Contact
fetch("../../app/views/pages/Contact/contact.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Contact: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("Contact");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// Recruitment-section
fetch("../../app/views/pages/Recruitment/recruitment-section.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("recruitment-section");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// Recruitment
fetch("../../app/views/pages/Recruitment/recruitment.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("Recruitment");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));

// Recruitment-title
fetch("../../app/views/pages/Recruitment/recruitment-title2.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("Recruitment-title");
    if (mount) mount.innerHTML = data;

    // Popup functionality for recruitment-title2
    const overlay = document.getElementById("popupOverlay");

    // Bắt tất cả nút mở popup
    const triggers = document.querySelectorAll(".but_fcb_td");

    let activePopup = null;

    function openPopup(popup) {
      if (!popup) return;

      activePopup = popup;
      popup.classList.add("active");
      overlay.classList.add("active");
      document.body.style.overflow = "hidden";
    }

    function closePopup() {
      if (!activePopup) return;

      activePopup.classList.remove("active");
      overlay.classList.remove("active");
      document.body.style.overflow = "";
      activePopup = null;
    }

    // Click mở popup
    triggers.forEach((btn) => {
      btn.addEventListener("click", function () {
        const selector = this.getAttribute("data-src");
        if (!selector) return;

        const popup = document.querySelector(selector);
        openPopup(popup);
      });
    });

    // Đóng khi click overlay
    overlay.addEventListener("click", closePopup);

    // Đóng khi click nút close
    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("close-popup")) {
        closePopup();
      }
    });

    // Đóng bằng ESC
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closePopup();
      }
    });

    // (Tuỳ chọn) Demo submit form
    const form = document.getElementById("fcb_td");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        alert("Đã gửi đơn ứng tuyển!");
        setTimeout(closePopup, 100);
      });
    }
  })
  .catch((err) => console.error(err));
// News
fetch("../../app/views/pages/News/news2.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("News");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// News-section
fetch("../../app/views/pages/News/news-section.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("News-section");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// News-title
fetch("../../app/views/pages/News/news-title.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("News-title");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// Introduction
fetch("../../app/views/pages/introduce/introduction.php")
  .then((res) => {
    if (!res.ok) throw new Error(`Failed to load Recruitment: ${res.status}`);
    return res.text();
  })
  .then((data) => {
    const mount = document.getElementById("Introduction");
    if (mount) mount.innerHTML = data;
  })
  .catch((err) => console.error(err));
// ----
// Use event delegation so clicks work after the HTML is injected
document.addEventListener("click", function (e) {
  const title = e.target.closest(".title_list_spct_k2");
  if (!title) return;

  // Ensure the title is inside the expected list
  const list = title.closest(".list_spct_k2");
  if (!list) return;

  const item = title.closest("li");
  if (!item) return;

  const content = item.querySelector(".nd_list_spct_k2");

  // Close other items
  list.querySelectorAll("li").forEach((li) => {
    if (li !== item) {
      li.classList.remove("active");
      const c = li.querySelector(".nd_list_spct_k2");
      if (c) c.style.display = "none";
    }
  });

  const isActive = item.classList.contains("active");
  if (!isActive) {
    item.classList.add("active");
    if (content) content.style.display = "block";
  } else {
    item.classList.remove("active");
    if (content) content.style.display = "none";
  }
});
// Toggle box in recruitment-title
