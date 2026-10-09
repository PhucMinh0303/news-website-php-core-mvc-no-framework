$(document).ready(function () {

    // Danh sách 3 ảnh (thay đúng đường dẫn thực tế của bạn)
    var images = [
        'public/assets/img/section1/slide/slide-01-4-png-20251117085601MjdQzhHBq.png',
        'public/assets/img/section1/slide/slide-02-2-jpg-20251117085606kn0MGhh9lp.jpg',
        'public/assets/img/section1/slide/slide-03-2-jpg-20251117085611Ry7YCiuXjs.jpg'
    ];

    var $slide    = $('.hero-slide1');
    var $bullets  = $('.swiper-pagination-bullet');
    var current   = 0;
    var total     = images.length;
    var interval  = 2000;   // 4 giây đổi ảnh 1 lần
    var fadeTime  = 1500;   // Phải khớp với transition opacity trong CSS
    var timer;

    /**
     * Chuyển ảnh với hiệu ứng fade giống PowerPoint:
     *  1. Đặt ảnh mới vào lớp ::before (qua background-image)
     *  2. Thêm class .fading  -> opacity ::before = 1 (ảnh mới hiện dần)
     *  3. Sau khi fade xong, gán ảnh mới vào background chính,
     *     xóa .fading và reset ::before về opacity 0.
     */
    function fadeTo(index) {
        if (index === current) return;

        var newImg = 'url("' + images[index] + '")';

        // Bước 1 + 2: đưa ảnh mới lên lớp ::before và fade in
        $slide.css('--bg-next', newImg); // tùy chọn, không bắt buộc
        $slide[0].style.setProperty('--bg-next', newImg);

        // Gán ảnh mới cho ::before thông qua inline style bằng cách dùng attr
        // Cách đơn giản: dùng 1 thẻ <style> động hoặc dùng CSS variable.
        // => Dùng CSS variable cho gọn:
        $slide.css('background-image', $slide.css('background-image')); // giữ nguyên
        $slide.addClass('fading');
        $slide[0].style.setProperty('--bg-next', newImg);

        // Đặt ảnh mới vào biến CSS --bg-next và bind vào ::before
        // (cần CSS: .hero-slide1::before { background-image: var(--bg-next); })

        // Sau khi fade xong -> chốt ảnh mới vào background chính
        setTimeout(function () {
            $slide.css('background-image', newImg);
            $slide.removeClass('fading');
        }, fadeTime);

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

    // Click pagination
    $bullets.on('click', function () {
        var idx = parseInt($(this).data('index'), 10);
        stopAuto();
        fadeTo(idx);
        startAuto();
    });

    // Hover dừng tự động
    $('.hero-swiper1')
        .on('mouseenter', stopAuto)
        .on('mouseleave', startAuto);

    // Khởi động
    startAuto();
});