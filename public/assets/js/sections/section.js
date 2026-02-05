// ========================================
// Global Configuration & Initialization
// ========================================

// Initialize AOS (Animate On Scroll)
document.addEventListener("DOMContentLoaded", () => {
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      easing: "ease-out-cubic",
      once: true,
      offset: 50,
    });
  }
});

// Helper function to refresh AOS after dynamic content load
function refreshAOS() {
  if (typeof AOS !== "undefined") {
    setTimeout(() => {
      AOS.refresh();
    }, 200);
  }
}

// ========================================
// Load HEADER
// ========================================
fetch("../../app/views/pages/include/header.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("header").innerHTML = data;

    // --- Mobile Menu Logic ---
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

    // --- Accordion Menu Logic ---
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

    // --- Search Box Logic ---
    const searchIcon = document.getElementById("search-icon");
    const searchBox = document.getElementById("search-box");

    if (searchIcon && searchBox) {
      searchIcon.addEventListener("click", () => {
        searchBox.classList.toggle("active");
      });

      // Close search box when clicking outside
      document.addEventListener("click", (e) => {
        if (!searchIcon.contains(e.target) && !searchBox.contains(e.target)) {
          searchBox.classList.remove("active");
        }
      });
    }
  });

// ========================================
// Load SECTION 1
// ========================================
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
    setTimeout(() => {
      initSection1Events();
      refreshAOS();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section1.php:", error);
  });

// Hàm khởi tạo events cho Section 1
function initSection1Events() {
  const section1 = document.getElementById("section1");
  const slides = document.querySelectorAll(".hero-slide1");
  const paginationBullets = document.querySelectorAll(
    ".swiper-pagination-bullet",
  );

  if (!section1 || !slides.length) {
    return;
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

  function getRandomBackground() {
    if (backgroundImages.length === 0) return "";
    const randomIndex = Math.floor(Math.random() * backgroundImages.length);
    return backgroundImages[randomIndex];
  }

  function changeSlideBackground() {
    slides.forEach((slide) => {
      const randomImage = getRandomBackground();
      if (randomImage) {
        slide.style.backgroundImage = `url('${randomImage}')`;
      }
    });
  }

  function updatePaginationActive(index) {
    paginationBullets.forEach((bullet, idx) => {
      if (idx === index) {
        bullet.classList.add("swiper-pagination-bullet-active");
      } else {
        bullet.classList.remove("swiper-pagination-bullet-active");
      }
    });
  }

  function updateSlideTransitionTime(delayTime = 2000, speed = 500) {
    if (heroSwiperInstance) {
      heroSwiperInstance.params.autoplay.delay = delayTime;
      heroSwiperInstance.params.speed = speed;

      if (heroSwiperInstance.autoplay.running) {
        heroSwiperInstance.autoplay.stop();
        heroSwiperInstance.autoplay.start();
      }
    }
  }

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
        },
        init: function () {
          updatePaginationActive(0);
        },
      },
    });

    return heroSwiperInstance;
  }

  function initRandomBackground() {
    changeSlideBackground();
    initSwiperWithCustomTime(1500, 800);
    setInterval(changeSlideBackground, 10000);
  }

  if (typeof Swiper !== "undefined") {
    initRandomBackground();
  } else {
    setTimeout(() => {
      if (typeof Swiper !== "undefined") {
        initRandomBackground();
      }
    }, 500);
  }

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
}

// ========================================
// Load SECTION 2
// ========================================
fetch("../../app/views/pages/introduce/section2.php")
  .then((res) => res.text())
  .then((data) => {
    const section2Container = document.getElementById("section2");
    if (section2Container) {
      section2Container.innerHTML = data;

      // --- Add Animations ---
      const s2Left = section2Container.querySelector(".section2-left");
      if (s2Left) s2Left.setAttribute("data-aos", "fade-right");

      const s2Items = section2Container.querySelectorAll(".section2-item li");
      s2Items.forEach((item, index) => {
        item.setAttribute("data-aos", "fade-up");
        item.setAttribute("data-aos-delay", index * 100);
      });

      const s2Right = section2Container.querySelector(".section2-right");
      if (s2Right) {
        const boxes = s2Right.querySelectorAll(".section2-box");
        boxes.forEach((box, index) => {
          box.setAttribute("data-aos", "fade-left");
          box.setAttribute("data-aos-delay", index * 200);
        });
      }

      refreshAOS();
    }
  });

// ========================================
// Load SECTION 3
// ========================================
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

    // --- Add Animations ---
    // Note: The section itself might already have data-aos="fade" from PHP
    const s3Items = section3Container.querySelectorAll(".list_rh_2 > li");
    s3Items.forEach((item, index) => {
      item.setAttribute("data-aos", "fade-up");
      item.setAttribute("data-aos-delay", index * 100);
    });

    setTimeout(() => {
      initSection3Events();
      refreshAOS();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section3-2.php:", error);
  });

function initSection3Events() {
  const section3 = document.getElementById("section3");
  const items = document.querySelectorAll(".list_rh_2 > li");
  const bgLayer = document.querySelector(".bg_rh_2");

  if (!section3 || !items.length || !bgLayer) {
    return;
  }

  function activateItem(li) {
    items.forEach((item) => {
      item.classList.remove("active");
    });
    li.classList.add("active");
    const newBg = li.getAttribute("data-bg");
    if (newBg) {
      bgLayer.style.backgroundImage = `url('${newBg}')`;
    }
  }

  if (items.length > 0) {
    activateItem(items[0]);
  }

  items.forEach((li) => {
    li.addEventListener("mouseenter", function () {
      activateItem(this);
    });
    li.addEventListener(
      "touchstart",
      function (e) {
        activateItem(this);
      },
      { passive: true },
    );
    li.addEventListener("click", function (e) {
      const link = this.querySelector("a");
      if (link && e.target.closest("a")) {
        activateItem(this);
      }
    });
  });
}

// ========================================
// Load SECTION 4
// ========================================
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

    // --- Add Animations ---
    const s4Title = section4Container.querySelector(".partner-title");
    if (s4Title) s4Title.setAttribute("data-aos", "fade-down");

    const s4Slider = section4Container.querySelector(".logo-slider");
    if (s4Slider) s4Slider.setAttribute("data-aos", "fade-up");

    setTimeout(() => {
      initSection4Carousel();
      refreshAOS();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section4.php:", error);
  });

function initSection4Carousel() {
  const section4 = document.getElementById("section4");
  const swiperWrapper = document.querySelector(".swiper-wrapper");
  const slides = document.querySelectorAll(".swiper-slide");

  if (!section4 || !swiperWrapper || !slides.length) {
    return;
  }

  const totalSlides = slides.length;
  const slideWidth =
    slides[0].offsetWidth + parseInt(getComputedStyle(slides[0]).marginRight);
  const visibleSlides = Math.floor(
    swiperWrapper.parentElement.offsetWidth / slideWidth,
  );
  const durationPerSlide = 2000;
  const transitionDuration = 500;
  let currentIndex = 0;
  let isTransitioning = false;

  for (let i = 0; i < visibleSlides + 2; i++) {
    const clone = slides[i % totalSlides].cloneNode(true);
    swiperWrapper.appendChild(clone);
  }

  function moveToNext() {
    if (isTransitioning) return;
    isTransitioning = true;

    currentIndex++;
    const translateX = -currentIndex * slideWidth;

    swiperWrapper.style.transition = `transform ${transitionDuration}ms ease-in-out`;
    swiperWrapper.style.transform = `translateX(${translateX}px)`;

    setTimeout(() => {
      if (currentIndex >= totalSlides) {
        currentIndex = 0;
        swiperWrapper.style.transition = "none";
        swiperWrapper.style.transform = `translateX(0px)`;
      }
      isTransitioning = false;
    }, transitionDuration);
  }

  setTimeout(() => {
    setInterval(moveToNext, durationPerSlide + transitionDuration);
  }, 1000);
}

// ========================================
// Load SECTION 5
// ========================================
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

    // --- Add Animations ---
    const newsTitle = section5Container.querySelector(".news-title");
    if (newsTitle) newsTitle.setAttribute("data-aos", "fade-down");

    const newsCards = section5Container.querySelectorAll(".news-card");
    newsCards.forEach((card, index) => {
      card.setAttribute("data-aos", "zoom-in");
      card.setAttribute("data-aos-delay", index * 150);
    });

    const newsItems = section5Container.querySelectorAll(".news-item");
    newsItems.forEach((item, index) => {
      item.setAttribute("data-aos", "fade-up");
      item.setAttribute("data-aos-delay", index * 100);
    });

    refreshAOS();
  })
  .catch((error) => {
    console.error("Error loading section5.php:", error);
  });

// ========================================
// Load FOOTER
// ========================================
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

    // --- Add Animations ---
    // Animate the whole footer or parts of it
    footerContainer.setAttribute("data-aos", "fade-in");

    refreshAOS();
  })
  .catch((error) => {
    console.error("Error loading footer.php:", error);
  });

console.log("All scripts loaded successfully");
