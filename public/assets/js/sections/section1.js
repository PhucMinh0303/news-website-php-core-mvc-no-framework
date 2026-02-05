// Load SECTION1

fetch(window.APP_CONFIG.BASE_URL + "pages/Introduce/section1")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section1.php: ${res.status}`);
    }
    return res.text();
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
  const ASSET = window.APP_CONFIG.ASSET_URL;
  if (document.querySelector(".hero-swiper1.swiper-initialized")) {
    return;
  }

  const backgroundImages = [
    ASSET + "img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png",
    ASSET + "img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg",
    ASSET + "img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg",
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
