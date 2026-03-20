// section3-2.js - gsap animation for section 3-2
fetch("introduce/section3-2.html")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("section3").innerHTML = data;

    // Khởi tạo section 3 events sau khi content tải xong
    // Delay để đảm bảo DOM fully rendered
    setTimeout(() => {
      initSection3WithGsap();
    }, 100);
  })
  .catch((error) => {
    console.error("Error loading section3-2.html:", error);
  });

function initSection3WithGsap() {
  const section3 = document.getElementById("section3");
  const items = section3 ? section3.querySelectorAll(".list_rh_2 > li") : [];
  const bgLayer = section3 ? section3.querySelector(".bg_rh_2") : null;

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

  // Utility: fade the background image using GSAP for smoother transitions
  function fadeBgTo(url) {
    if (!url) return;
    const currentOpacity = parseFloat(getComputedStyle(bgLayer).opacity) || 1;

    // If GSAP is available, animate fade-out / swap / fade-in
    if (window.gsap) {
      gsap.to(bgLayer, {
        opacity: 0,
        duration: 0.25,
        ease: "power1.out",
        onComplete() {
          bgLayer.style.backgroundImage = `url('${url}')`;
          gsap.to(bgLayer, { opacity: 1, duration: 0.25, ease: "power1.in" });
        },
      });
      return;
    }

    // Fallback: instant swap
    bgLayer.style.backgroundImage = `url('${url}')`;
  }

  let activeItem = null;

  function setActiveItem(li) {
    if (!li || li === activeItem) return;

    if (activeItem) {
      activeItem.classList.remove("active");
      if (window.gsap) {
        gsap.to(activeItem, { scale: 1, duration: 0.25, ease: "power1.out" });
      }
    }

    activeItem = li;
    activeItem.classList.add("active");

    if (window.gsap) {
      gsap.to(activeItem, { scale: 1.04, duration: 0.25, ease: "power1.out" });
    }

    const newBg = activeItem.getAttribute("data-bg");
    fadeBgTo(newBg);
  }

  // Initialize the first item as active
  setActiveItem(items[0]);

  items.forEach((li) => {
    li.addEventListener("mouseenter", () => setActiveItem(li));

    li.addEventListener("touchstart", () => setActiveItem(li), {
      passive: true,
    });

    li.addEventListener("click", (event) => {
      // Keep link navigation while still animating
      const link = li.querySelector("a");
      if (link && event.target.closest("a")) {
        setActiveItem(li);
      }
    });
  });
}
