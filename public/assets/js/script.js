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

// section1.js (jQuery version)
$.get("introduce/section1.html", function (data) {
  // Chèn nội dung vào phần tử có id="section1"
  $("#section1").html(data);

  const backgroundImages = [
    "../assets/img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png",
    "../assets/img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg",
    "../assets/img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg",
  ];

  // Biến toàn cục để lưu Swiper instance
  let heroSwiperInstance = null;

  // Hàm chọn ngẫu nhiên một hình ảnh từ mảng
  function getRandomBackground() {
    if (backgroundImages.length === 0) return "";
    const randomIndex = Math.floor(Math.random() * backgroundImages.length);
    return backgroundImages[randomIndex];
  }

  // Hàm thay đổi background cho slide
  function changeSlideBackground() {
    $(".hero-slide1").each(function () {
      const randomImage = getRandomBackground();
      if (randomImage) {
        $(this).css("background-image", `url('${randomImage}')`);
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
        `Đã cập nhật thời gian chuyển slide: ${delayTime}ms delay, ${speed}ms speed`,
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
          console.log("Slide changed to: ", this.activeIndex);
        },
      },
    });

    return heroSwiperInstance;
  }

  // Hàm khởi tạo - chạy sau khi nội dung đã được chèn
  function initRandomBackground() {
    if ($(".hero-slide1").length > 0) {
      changeSlideBackground();

      // Khởi tạo Swiper với thời gian mặc định
      initSwiperWithCustomTime(1500, 800);

      // Thay đổi background tự động mỗi 10 giây
      setInterval(changeSlideBackground, 10000);

      console.log(
        "Slider đã được khởi tạo với thời gian chuyển slide 1.5 giây và background thay đổi tự động mỗi 10 giây",
      );
    }
  }

  // Gọi hàm khởi tạo ngay sau khi chèn HTML
  initRandomBackground();

  // API để điều khiển từ bên ngoài
  window.randomBackground = {
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
  };

  // Hàm tiện ích để thay đổi thời gian từ console
  window.changeSlideDelay = function (milliseconds) {
    if (window.randomBackground) {
      window.randomBackground.setSlideTime(milliseconds);
      alert(
        `Đã đổi thời gian chuyển slide thành ${milliseconds}ms (${milliseconds / 1000} giây)`,
      );
    }
  };
});
// Load SECTION2
fetch("introduce/section2.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("section2").innerHTML = data;
  });
// Load SECTION3
$.get("introduce/section3-2.php", function (data) {
  // Chèn nội dung vào phần tử có id="section3"
  $("#section3").html(data);

  // Lấy danh sách các mục và lớp nền
  var $items = $(".list_rh_2 > li");
  var $bgLayer = $(".bg_rh_2");

  // Kiểm tra sự tồn tại của các phần tử cần thiết
  if ($items.length === 0 || $bgLayer.length === 0) return;

  // Hàm kích hoạt một mục
  function activateItem(li) {
    // 1. Xóa class 'active' khỏi tất cả các mục
    $items.removeClass("active");

    // 2. Thêm class 'active' vào mục hiện tại
    $(li).addClass("active");

    // 3. Thay đổi ảnh nền
    var newBg = $(li).data("bg"); // hoặc .attr("data-bg")
    if (newBg) {
      $bgLayer.css("background-image", "url('" + newBg + "')");
    }
  }

  // Kích hoạt mục đầu tiên
  activateItem($items[0]);

  // Gắn sự kiện hover (mouseenter) cho từng mục
  $items.on("mouseenter", function () {
    activateItem(this);
  });
});
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

// script.js makes API request
fetch("/api/news")
  .then((res) => res.json())
  .then((data) => {
    // Update DOM with response data
    renderNews(data);
  });
