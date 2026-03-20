// section1.js
fetch("introduce/section1.html")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("section1").innerHTML = data;
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
      const slides = document.querySelectorAll(".hero-slide1");

      slides.forEach((slide) => {
        // Lấy hình ảnh ngẫu nhiên khác nhau cho mỗi slide
        const randomImage = getRandomBackground();

        if (randomImage) {
          // Thay đổi background-image
          slide.style.backgroundImage = `url('${randomImage}')`;
        }
      });
    }

    // Hàm cập nhật thời gian chuyển slide
    function updateSlideTransitionTime(delayTime = 2000, speed = 500) {
      if (heroSwiperInstance) {
        // Cập nhật thời gian autoplay
        heroSwiperInstance.params.autoplay.delay = delayTime;
        heroSwiperInstance.params.speed = speed;

        // Nếu autoplay đang chạy, cần khởi động lại
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
      // Kiểm tra xem Swiper đã được khởi tạo chưa
      if (heroSwiperInstance) {
        updateSlideTransitionTime(delayTime, speed);
        return heroSwiperInstance;
      }

      // Nếu chưa, khởi tạo mới
      heroSwiperInstance = new Swiper(".hero-swiper1", {
        loop: true,
        speed: speed, // Thời gian chuyển động giữa các slide (ms)
        autoplay: {
          delay: delayTime, // Thời gian delay giữa các slide (ms)
          disableOnInteraction: false,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        on: {
          // Có thể thêm sự kiện khi chuyển slide nếu muốn
          slideChange: function () {
            console.log("Slide changed to: ", this.activeIndex);
            // Có thể thay đổi background khi chuyển slide
            // changeSlideBackground(); // Tắt để tránh xung đột với setInterval
          },
        },
      });

      return heroSwiperInstance;
    }

    // Hàm khởi tạo - chạy khi DOM đã tải xong
    function initRandomBackground() {
      // Kiểm tra xem có phần tử slider không
      const sliderExists = document.querySelector(".hero-slide1");

      if (sliderExists) {
        // Thay đổi background ngay khi trang tải
        changeSlideBackground();

        // Khởi tạo Swiper với thời gian mặc định (5 giây)
        initSwiperWithCustomTime(1500, 800);

        // Thay đổi background tự động mỗi 10 giây (không phụ thuộc vào slide change)
        setInterval(changeSlideBackground, 10000);

        console.log(
          "Slider đã được khởi tạo với thời gian chuyển slide 5 giây và background thay đổi tự động mỗi 10 giây",
        );
      }
    }

    // Chờ DOM tải xong
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", initRandomBackground);
    } else {
      initRandomBackground();
    }

    // API để điều khiển từ bên ngoài
    window.randomBackground = {
      // Thay đổi background
      changeBackground: changeSlideBackground,

      // Lấy hình ảnh ngẫu nhiên
      getRandomBackground: getRandomBackground,

      // Danh sách hình ảnh
      backgroundImages: backgroundImages,

      // Cập nhật thời gian chuyển slide
      setSlideTime: function (delayTime, speed = 500) {
        updateSlideTransitionTime(delayTime, speed);
      },

      // Khởi tạo lại với thời gian mới
      initWithTime: function (delayTime = 2000, speed = 500) {
        initSwiperWithCustomTime(delayTime, speed);
      },

      // Dừng autoplay
      stopAutoplay: function () {
        if (heroSwiperInstance) {
          heroSwiperInstance.autoplay.stop();
        }
      },

      // Bắt đầu autoplay
      startAutoplay: function () {
        if (heroSwiperInstance) {
          heroSwiperInstance.autoplay.start();
        }
      },

      // Chuyển đến slide cụ thể
      goToSlide: function (index) {
        if (heroSwiperInstance) {
          heroSwiperInstance.slideTo(index);
        }
      },
    };

    // Hàm tiện ích để thay đổi thời gian từ console
    function changeSlideDelay(milliseconds) {
      if (window.randomBackground) {
        window.randomBackground.setSlideTime(milliseconds);
        alert(
          `Đã đổi thời gian chuyển slide thành ${milliseconds}ms (${
            milliseconds / 1000
          } giây)`,
        );
      }
    }
  });
