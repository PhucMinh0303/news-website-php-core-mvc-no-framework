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
    // Load section content
    $("#section1").load("introduce/section1.php", function () {
        // Configuration
        const config = {
            backgroundImages: [
                "../assets/img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png",
                "../assets/img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg",
                "../assets/img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg",
            ],
            defaultDelayTime: 1500,
            defaultSpeed: 800,
            backgroundChangeInterval: 10000
        };

        let heroSwiperInstance = null;

        // Get random background image
        function getRandomBackground() {
            if (!config.backgroundImages.length) return "";
            const randomIndex = Math.floor(Math.random() * config.backgroundImages.length);
            return config.backgroundImages[randomIndex];
        }

        // Change slide background
        function changeSlideBackground() {
            $(".hero-slide1").each(function () {
                const randomImage = getRandomBackground();
                if (randomImage) {
                    $(this).css("background-image", "url('" + randomImage + "')");
                }
            });
        }

        // Update slide transition time
        function updateSlideTransitionTime(delayTime, speed) {
            if (heroSwiperInstance && heroSwiperInstance.params) {
                heroSwiperInstance.params.autoplay.delay = delayTime;
                heroSwiperInstance.params.speed = speed;

                if (heroSwiperInstance.autoplay && heroSwiperInstance.autoplay.running) {
                    heroSwiperInstance.autoplay.stop();
                    heroSwiperInstance.autoplay.start();
                }

                console.log("Updated slide time - Delay:", delayTime, "ms, Speed:", speed, "ms");
            }
        }

        // Initialize Swiper
        function initSwiperWithCustomTime(delayTime, speed) {
            if (heroSwiperInstance) {
                updateSlideTransitionTime(delayTime, speed);
                return heroSwiperInstance;
            }

            heroSwiperInstance = new Swiper(".hero-swiper1", {
                loop: true,
                speed: speed,
                effect: "fade",
                wrapperClass: 'swiper-wrapper1',
                slideClass: 'swiper-slide1',
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
                        $(".hero-slide1").removeClass("active");
                        $(".hero-slide1").eq(this.realIndex).addClass("active");
                    },
                },
            });

            return heroSwiperInstance;
        }

        // Initialize slider with random backgrounds
        function initRandomBackground() {
            if ($(".hero-slide1").length) {
                changeSlideBackground();
                initSwiperWithCustomTime(config.defaultDelayTime, config.defaultSpeed);

                setInterval(function () {
                    changeSlideBackground();
                }, config.backgroundChangeInterval);

                console.log("Slider initialized successfully");
            } else {
                console.warn("No .hero-slide1 elements found");
            }
        }

        // Public API
        window.randomBackground = {
            // Core functions
            changeBackground: changeSlideBackground,
            getRandomBackground: getRandomBackground,
            backgroundImages: config.backgroundImages,

            // Time control
            setSlideTime: function (delayTime, speed) {
                updateSlideTransitionTime(delayTime || config.defaultDelayTime, speed || config.defaultSpeed);
            },

            // Swiper control
            initWithTime: function (delayTime, speed) {
                initSwiperWithCustomTime(delayTime || config.defaultDelayTime, speed || config.defaultSpeed);
            },

            stopAutoplay: function () {
                if (heroSwiperInstance && heroSwiperInstance.autoplay) {
                    heroSwiperInstance.autoplay.stop();
                    console.log("Autoplay stopped");
                }
            },

            startAutoplay: function () {
                if (heroSwiperInstance && heroSwiperInstance.autoplay) {
                    heroSwiperInstance.autoplay.start();
                    console.log("Autoplay started");
                }
            },

            goToSlide: function (index) {
                if (heroSwiperInstance && typeof heroSwiperInstance.slideTo === 'function') {
                    heroSwiperInstance.slideTo(index);
                    console.log("Navigated to slide:", index);
                }
            },

            // Additional helper functions
            destroy: function () {
                if (heroSwiperInstance) {
                    heroSwiperInstance.destroy(true, true);
                    heroSwiperInstance = null;
                    console.log("Swiper destroyed");
                }
            },

            getCurrentIndex: function () {
                return heroSwiperInstance ? heroSwiperInstance.realIndex : -1;
            },

            updateImages: function (newImages) {
                if (Array.isArray(newImages) && newImages.length) {
                    config.backgroundImages = newImages;
                    changeSlideBackground();
                    console.log("Background images updated");
                }
            }
        };

        // Start initialization
        initRandomBackground();
    });
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
