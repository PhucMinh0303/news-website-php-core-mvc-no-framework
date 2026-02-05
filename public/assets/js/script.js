// Load HEADER
fetch("../../app/views/pages/include/header.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("header").innerHTML = data;
    const menuMobileIcon = document.querySelector(".icon_menu_mobile");
    const menuMobile = document.querySelector(".menu_mobile");
    const closeMenuMobile = document.querySelector(".close_menu_mobile");

    if (menuMobileIcon && menuMobile) {
      // Mở menu mobile
      menuMobileIcon.addEventListener("click", function () {
        menuMobile.style.visibility = "visible";
        menuMobile.style.left = "0";
        document.body.style.overflow = "hidden"; // Ngăn scroll body
      });

      // Đóng menu mobile khi click vào nút đóng
      if (closeMenuMobile) {
        closeMenuMobile.addEventListener("click", function () {
          menuMobile.style.visibility = "hidden";
          menuMobile.style.left = "-280px";
          document.body.style.overflow = ""; // Khôi phục scroll
        });
      }

      // Đóng menu mobile khi click ra ngoài
      document.addEventListener("click", function (event) {
        if (
          !menuMobile.contains(event.target) &&
          !menuMobileIcon.contains(event.target) &&
          menuMobile.style.left === "0px"
        ) {
          menuMobile.style.visibility = "hidden";
          menuMobile.style.left = "-280px";
          document.body.style.overflow = "";
        }
      });
    }
    const arrows = document.querySelectorAll(".arrown_menu_accordion");

    arrows.forEach(function (arrow) {
      arrow.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const parentLi = this.closest("li");
        const subMenu = parentLi.querySelector(".ul_ma_2");

        if (!subMenu) return;

        // active cho li cấp 1
        parentLi.classList.toggle("active");

        // hiển thị menu con
        subMenu.classList.toggle("active");

        // xoay icon
        this.classList.toggle("active");
      });
    });
  });

// Load SECTION1

fetch("/introduce/section1")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section1.php: ${res.status}`);
    }
    return res.text();
  })
  .then((html) => {
    document.getElementById("section1").innerHTML = html;
  })
  .then((data) => {
    const section1Container = document.getElementById("section1");
    if (!section1Container) {
      console.error("Section 1: Container element #section1 not found in DOM");
      return;
    }
    section1Container.innerHTML = data;

    // Khởi tạo section 1 events sau khi content tải xong
    // Delay để đảm bảo DOM fully rendered
    setTimeout(() => {
      initSection1Events();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section1.php:", error);
  });

// Hàm khởi tạo events cho Section 1
function initSection1Events() {
  // Chọn phần tử section1 được load từ fetch
  const section1 = document.getElementById("section1");
  const slides = document.querySelectorAll(".hero-slide1");
  const paginationBullets = document.querySelectorAll(
    ".swiper-pagination-bullet",
  );

  // Kiểm tra xem elements có tồn tại không
  if (!section1 || !slides.length) {
    console.warn(
      "Section 1 elements not found. Section1:",
      section1,
      "Slides:",
      slides.length,
    );
    return;
  }

  // Warning nếu không tìm thấy pagination bullets
  if (!paginationBullets.length) {
    console.warn(
      "Section 1: No pagination bullets found. Swiper pagination may not work",
    );
  }

  const backgroundImages = [
    "../assets/img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png",
    "../assets/img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg",
    "../assets/img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg",
  ];

  let heroSwiperInstance = null;
  let currentSlideIndex = 0;

  // Hàm chọn ngẫu nhiên một hình ảnh từ mảng
  function getRandomBackground() {
    if (backgroundImages.length === 0) return "";
    const randomIndex = Math.floor(Math.random() * backgroundImages.length);
    return backgroundImages[randomIndex];
  }

  // Hàm thay đổi background cho slide
  function changeSlideBackground() {
    slides.forEach((slide) => {
      const randomImage = getRandomBackground();
      if (randomImage) {
        slide.style.backgroundImage = `url('${randomImage}')`;
      }
    });
  }

  // Hàm cập nhật active state
  function updatePaginationActive(index) {
    paginationBullets.forEach((bullet, idx) => {
      if (idx === index) {
        bullet.classList.add("swiper-pagination-bullet-active");
      } else {
        bullet.classList.remove("swiper-pagination-bullet-active");
      }
    });
  }

  // Hàm cập nhật thời gian chuyển slide
  function updateSlideTransitionTime(delayTime = 2000, speed = 500) {
    if (heroSwiperInstance) {
      heroSwiperInstance.params.autoplay.delay = delayTime;
      heroSwiperInstance.params.speed = speed;

      if (heroSwiperInstance.autoplay.running) {
        heroSwiperInstance.autoplay.stop();
        heroSwiperInstance.autoplay.start();
      }

      console.log(
        `Section 1: Cập nhật thời gian chuyển slide: ${delayTime}ms delay, ${speed}ms speed`,
      );
    }
  }

  // Hàm khởi tạo Swiper với thời gian tùy chỉnh
  function initSwiperWithCustomTime(delayTime = 2000, speed = 500) {
    if (heroSwiperInstance) {
      updateSlideTransitionTime(delayTime, speed);
      return heroSwiperInstance;
    }

    heroSwiperInstance = new Swiper(".hero-swiper1", {
      loop: true,
      speed: speed,
      autoplay: {
        delay: delayTime,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      on: {
        slideChange: function () {
          currentSlideIndex = this.realIndex;
          updatePaginationActive(currentSlideIndex);
          console.log("Section 1: Slide changed to:", currentSlideIndex);
        },
        init: function () {
          updatePaginationActive(0);
          console.log(
            "Section 1: Swiper initialized with",
            this.slides.length,
            "slides",
          );
        },
        error: function (swiper, error) {
          console.error("Section 1: Swiper error:", error);
        },
      },
    });

    return heroSwiperInstance;
  }

  // Hàm khởi tạo
  function initRandomBackground() {
    // Thay đổi background ngay khi trang tải
    changeSlideBackground();

    // Khởi tạo Swiper với thời gian mặc định
    initSwiperWithCustomTime(1500, 800);

    // Thay đổi background tự động mỗi 10 giây
    setInterval(changeSlideBackground, 10000);

    console.log("Section 1: Initialized with random backgrounds and Swiper");
  }

  // Đợi Swiper library tải xong nếu chưa có
  if (typeof Swiper !== "undefined") {
    initRandomBackground();
  } else {
    console.warn("Section 1: Swiper library not loaded yet");
    // Thử lại sau 500ms
    setTimeout(() => {
      if (typeof Swiper !== "undefined") {
        initRandomBackground();
      } else {
        console.error("Section 1: Swiper library failed to load after retry");
      }
    }, 500);
  }

  // API để điều khiển từ bên ngoài
  window.section1Slider = {
    changeBackground: changeSlideBackground,
    getRandomBackground: getRandomBackground,
    backgroundImages: backgroundImages,
    setSlideTime: function (delayTime, speed = 500) {
      updateSlideTransitionTime(delayTime, speed);
    },
    initWithTime: function (delayTime = 2000, speed = 500) {
      initSwiperWithCustomTime(delayTime, speed);
    },
    stopAutoplay: function () {
      if (heroSwiperInstance) {
        heroSwiperInstance.autoplay.stop();
      }
    },
    startAutoplay: function () {
      if (heroSwiperInstance) {
        heroSwiperInstance.autoplay.start();
      }
    },
    goToSlide: function (index) {
      if (heroSwiperInstance) {
        heroSwiperInstance.slideTo(index);
      }
    },
    getCurrentIndex: function () {
      return currentSlideIndex;
    },
  };

  console.log(
    "Section 1 events initialized successfully. Slides count:",
    slides.length,
  );
}
// Load SECTION2
fetch("../../app/views/pages/introduce/section2.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("section2").innerHTML = data;
  });
// Load SECTION3
fetch("/introduce/section3-2")
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
// Load SECTION4
fetch("../../app/views/pages/introduce/section4.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section4.php: ${res.status}`);
    }
    return res.text();
  })
  .then((data) => {
    const section4Container = document.getElementById("section4");
    if (!section4Container) {
      console.error("Section 4: Container element #section4 not found in DOM");
      return;
    }
    section4Container.innerHTML = data;

    // Delay để đảm bảo DOM fully rendered
    setTimeout(() => {
      initSection4Carousel();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section4.php:", error);
  });

// Hàm khởi tạo carousel cho Section 4
function initSection4Carousel() {
  const section4 = document.getElementById("section4");
  const swiperWrapper = document.querySelector(".swiper-wrapper");
  const slides = document.querySelectorAll(".swiper-slide");

  // Validation
  if (!section4 || !swiperWrapper || !slides.length) {
    console.warn(
      "Section 4: Required elements not found. Section4:",
      section4,
      "Wrapper:",
      swiperWrapper,
      "Slides:",
      slides.length,
    );
    return;
  }

  const totalSlides = slides.length;
  const slideWidth =
    slides[0].offsetWidth + parseInt(getComputedStyle(slides[0]).marginRight);
  const visibleSlides = Math.floor(
    swiperWrapper.parentElement.offsetWidth / slideWidth,
  );
  const durationPerSlide = 2000; // 2 giây dừng
  const transitionDuration = 500; // 0.5 giây di chuyển
  let currentIndex = 0;
  let isTransitioning = false;

  // Clone các slide để tạo hiệu ứng infinite
  for (let i = 0; i < visibleSlides + 2; i++) {
    const clone = slides[i % totalSlides].cloneNode(true);
    swiperWrapper.appendChild(clone);
  }

  const allSlides = document.querySelectorAll(".swiper-slide");
  const totalWidth = slideWidth * allSlides.length;

  function moveToNext() {
    if (isTransitioning) return;
    isTransitioning = true;

    currentIndex++;
    const translateX = -currentIndex * slideWidth;

    swiperWrapper.style.transition = `transform ${transitionDuration}ms ease-in-out`;
    swiperWrapper.style.transform = `translateX(${translateX}px)`;

    setTimeout(() => {
      // Nếu đã trôi qua hết một vòng clone, reset về vị trí ban đầu
      if (currentIndex >= totalSlides) {
        currentIndex = 0;
        swiperWrapper.style.transition = "none";
        swiperWrapper.style.transform = `translateX(0px)`;
      }
      isTransitioning = false;
    }, transitionDuration);
  }

  // Bắt đầu animation sau khi load
  setTimeout(() => {
    // Di chuyển sau mỗi khoảng thời gian (2s dừng + 0.5s di chuyển)
    setInterval(moveToNext, durationPerSlide + transitionDuration);
  }, 1000); // Delay 1 giây trước khi bắt đầu

  console.log(
    "Section 4 carousel initialized successfully. Slides count:",
    totalSlides,
  );
}
// Load SECTION5
fetch("../../app/views/pages/introduce/section5.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section5.php: ${res.status}`);
    }
    return res.text();
  })
  .then((data) => {
    const section5Container = document.getElementById("section5");
    if (!section5Container) {
      console.error("Section 5: Container element #section5 not found in DOM");
      return;
    }
    section5Container.innerHTML = data;
    console.log("Section 5 loaded successfully");
  })
  .catch((error) => {
    console.error("Error loading section5.php:", error);
  });

// Load FOOTER
fetch("../../app/views/pages/include/footer.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load footer.php: ${res.status}`);
    }
    return res.text();
  })
  .then((data) => {
    const footerContainer = document.getElementById("footer");
    if (!footerContainer) {
      console.error("Footer: Container element #footer not found in DOM");
      return;
    }
    footerContainer.innerHTML = data;
    console.log("Footer loaded successfully");
  })
  .catch((error) => {
    console.error("Error loading footer.php:", error);
  });
// ----

// ----
// Toggle search box của header (waits for header to load)
fetch("../../app/views/pages/include/header.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to reload header.php: ${res.status}`);
    }
    return res.text();
  })
  .then(() => {
    const searchIcon = document.getElementById("search-icon");
    const searchBox = document.getElementById("search-box");

    if (!searchIcon || !searchBox) {
      console.warn(
        "Search box: Required elements not found. Icon:",
        searchIcon,
        "Box:",
        searchBox,
      );
      return;
    }

    searchIcon.addEventListener("click", () => {
      searchBox.classList.toggle("active");
    });

    // Close search box when clicking outside
    document.addEventListener("click", (e) => {
      if (!searchIcon.contains(e.target) && !searchBox.contains(e.target)) {
        searchBox.classList.remove("active");
      }
    });

    console.log("Search box toggle initialized");
  })
  .catch((error) => {
    console.error("Error setting up search box:", error);
  });

// ========================================
// Tổng hợp Initialization Script
// ========================================
// Tất cả phần tử được load bằng fetch và initialize tương ứng:
// - Header: Menu mobile + Menu accordion
// - Section 1: Hero slider với Swiper + Pagination + Random background
// - Section 2: Tĩnh
// - Section 3: Service tabs với hover + background change
// - Section 4: Business partners carousel
// - Section 5: Tĩnh
// - Footer: Tĩnh
// - Search Box: Toggle functionality
// ========================================

console.log("All scripts loaded successfully");
