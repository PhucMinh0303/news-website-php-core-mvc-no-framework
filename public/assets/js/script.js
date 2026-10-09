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
$(document).ready(function () {

    // Danh sách 3 ảnh
    var images = [
        'public/assets/img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png',
        'public/assets/img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg',
        'public/assets/img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg'
    ];

    var $slide    = $('.hero-slide1');
    var $bullets  = $('.swiper-pagination-bullet');
    var current   = 0;
    var total     = images.length;
    var interval  = 4000;   // 4 giây đổi ảnh
    var wipeTime  = 1200;   // Thời gian wipe (khớp CSS)
    var timer;

    /**
     * Chuyển ảnh với hiệu ứng WIPE (không đụng đến hero-content)
     */
    function fadeTo(index) {
        if (index === current) return;

        var newImg = 'url("' + images[index] + '")';

        // Gán ảnh mới vào biến CSS --bg-next (dùng cho ::before)
        $slide[0].style.setProperty('--bg-next', newImg);

        // Kích hoạt wipe
        $slide.addClass('wiping');

        // Sau khi wipe xong -> chốt ảnh mới vào background chính
        setTimeout(function () {
            $slide.css('background-image', newImg);
            $slide.removeClass('wiping');
            $slide[0].style.setProperty('--bg-next', 'none');
        }, wipeTime);

        // Cập nhật pagination
        $bullets.removeClass('active');
        $bullets.eq(index).addClass('active');

        current = index;
    }

    function nextSlide() {
        fadeTo((current + 1) % total);
    }

    function startAuto() {
        timer = setInterval(nextSlide, interval);
    }
    function stopAuto() {
        clearInterval(timer);
    }

    // Click pagination: số 1 -> ảnh 1, số 2 -> ảnh 2, số 3 -> ảnh 3
    $bullets.on('click', function () {
        var idx = parseInt($(this).data('index'), 10);
        if (isNaN(idx)) return;
        stopAuto();
        fadeTo(idx);
        startAuto();
    });

    // Hover dừng tự động
    $('.hero-swiper1')
        .on('mouseenter', stopAuto)
        .on('mouseleave', startAuto);

    // Khởi động auto slide
    startAuto();

    // KHÔNG gọi playZoomIn() ở đây nữa.
    // Animation zoom in sẽ do CSS tự chạy 1 lần khi trang load/reload.
});

// Load SECTION2
fetch("introduce/section2.php")
    .then((res) => res.text())
    .then((data) => {
        document.getElementById("section2").innerHTML = data;
    });
// SECTION3-2: hover vào <li> để hiển thị mô tả (.des_nd_rh_2) và đổi nền
$(function () {
    var $section3 = $(".rh_2");
    var $items = $section3.find(".list_rh_2 > li");
    var $bgLayer = $section3.find(".bg_rh_2");

    if ($items.length === 0 || $bgLayer.length === 0) return;

    function activateItem(li) {
        var $li = $(li);
        if ($li.hasClass("active")) return;

        $items.removeClass("active");
        $li.addClass("active");

        var newBg = $li.data("bg");
        if (newBg) {
            $bgLayer.css("background-image", "url('" + newBg + "')");
        }
    }

    activateItem($items[0]);

    $items
        .off(".section3")
        .on("mouseenter.section3 focusin.section3", function () {
            activateItem(this);
        });
});// Load SECTION4
$(document).ready(function () {
    const $slider = $(".section4 .logo-slider");
    const $wrapper = $slider.find(".swiper-wrapper");
    const $slides = $wrapper.find(".swiper-slide");

    let slideWidth = $slides.outerWidth(true);
    let speed = 2000; // thời gian chuyển
    let autoplayDelay = 0; // chạy liên tục

    // clone slide để loop vô hạn
    $wrapper.append($slides.clone());

    function startSlider() {
        $wrapper.animate(
            {left: -slideWidth},
            speed,
            "linear",
            function () {
                $wrapper.css("left", 0);
                $wrapper.append($wrapper.children().first());
            }
        );
    }

    let sliderInterval = setInterval(startSlider, autoplayDelay);

    // pause khi hover
    $slider.hover(
        function () {
            clearInterval(sliderInterval);
        },
        function () {
            sliderInterval = setInterval(startSlider, autoplayDelay);
        }
    );

    // responsive resize
    $(window).on("resize", function () {
        slideWidth = $slides.outerWidth(true);
    });
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
    // Footer animation initialization (if any)
    $(function () {
  const $footerImg = $('.footer-img');
  const $img = $footerImg.find('img');

  if (!$footerImg.length || !$img.length) return;

  const BAR_COUNT = 12;
  const LOOP_INTERVAL = 2000; // 2 giây chạy lại 1 lần

  // Tạo container + các bar
  const $barsWrap = $('<div class="random-bars"></div>');
  for (let i = 0; i < BAR_COUNT; i++) {
    $barsWrap.append('<div class="bar"></div>');
  }
  $footerImg.append($barsWrap);

  const $bars = $barsWrap.children('.bar');

  const rand = (min, max) => Math.random() * (max - min) + min;

  // Hàm chạy 1 vòng animation
  function playRandomBars() {
    $bars.each(function () {
      const $bar = $(this);

      // Reset về trạng thái phủ kín ảnh, không transition
      $bar.css({
        transition: 'none',
        transform: 'translateY(0)',
        opacity: 1
      });

      // Force reflow để reset có hiệu lực trước khi set transition mới
      void this.offsetWidth;

      const delay = rand(0, 400);
      const duration = rand(500, 900);
      const direction = Math.random() > 0.5 ? 1 : -1;

      setTimeout(function () {
        $bar.css({
          transition: `transform ${duration}ms cubic-bezier(.77,0,.18,1), opacity ${duration}ms ease`,
          transform: `translateY(${direction * 100}%)`,
          opacity: 0
        });
      }, delay);
    });
  }

  // Chạy lần đầu + lặp mỗi 2 giây
  let loopTimer = null;

  function startLoop() {
    playRandomBars();
    loopTimer = setInterval(playRandomBars, LOOP_INTERVAL);
  }

  function stopLoop() {
    if (loopTimer) {
      clearInterval(loopTimer);
      loopTimer = null;
    }
  }

  // Chỉ chạy khi footer-img vào viewport (tối ưu hiệu năng)
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            startLoop();
          } else {
            stopLoop();
          }
        });
      },
      { threshold: 0.3 }
    );
    observer.observe($footerImg[0]);
  } else {
    startLoop();
  }
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

$(document).ready(function () {
    $('input[name="filechon"]').on("change", function () {
        const fileName = this.value;

        if (fileName && !/\.pdf$/i.test(fileName)) {
            alert("Vui lòng chọn file PDF.");
            $(this).val("");
        }
    });
});
