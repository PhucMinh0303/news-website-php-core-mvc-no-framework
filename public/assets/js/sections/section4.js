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
