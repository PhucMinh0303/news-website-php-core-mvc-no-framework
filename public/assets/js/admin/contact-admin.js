// Active tabs

$(document).on('click', '.message-item', function() {
    const msgId = $(this).data('message-id');
    const tab = $(this).data('tab') || 'inbox';
    
    // Remove active class từ tất cả items
    $('.message-item').removeClass('active');
    $(this).addClass('active');
    
    // Load detail
    loadMessageDetail(msgId, tab);
    activeMessageId = msgId;
});
$(document).ready(function() {
    // Xử lý click vào tab
    $('#tabsContainer .tab').on('click', function() {
        // Lấy data-tab của tab được click
        const tab = $(this).data('tab');
        
        // Remove active class từ tất cả tab
        $('#tabsContainer .tab').removeClass('active');
        $('#tabsContainer .tab').removeAttr('aria-selected');
        $('#tabsContainer .tab').attr('aria-selected', 'false');
        
        // Thêm active class cho tab được click
        $(this).addClass('active');
        $(this).attr('aria-selected', 'true');
        
        // Gọi hàm load dữ liệu theo tab (nếu có)
        loadMessagesByTab(tab);
    });
});

// Hàm load dữ liệu theo tab
function loadMessagesByTab(tab) {
    // Cập nhật activeMessageId nếu cần
    activeMessageId = null;
    
    // Xóa active class khỏi tất cả message items
    $('.message-item').removeClass('active');
    
    // Ẩn tất cả message items
    $('.message-item').hide();
    
    // Hiển thị message items theo tab
    // Giả sử mỗi message item có data-tab attribute
    if (tab === 'inbox') {
        $('.message-item[data-tab="inbox"]').show();
    } else if (tab === 'archive') {
        $('.message-item[data-tab="archive"]').show();
    } else if (tab === 'deleted') {
        $('.message-item[data-tab="deleted"]').show();
    }
}

// Hoặc bạn có thể tích hợp với hàm loadMessageDetail có sẵn
$(document).on('click', '#tabsContainer .tab', function() {
    const tab = $(this).data('tab');
    
    // Cập nhật active tab
    $('#tabsContainer .tab').removeClass('active');
    $('#tabsContainer .tab').attr('aria-selected', 'false');
    $(this).addClass('active');
    $(this).attr('aria-selected', 'true');
    
    // Reset active message
    activeMessageId = null;
    $('.message-item').removeClass('active');
    
    // Ẩn/Hiện danh sách message theo tab
    $('.message-item').hide();
    $(`.message-item[data-tab="${tab}"]`).show();
    
    // Nếu cần load detail cho message đầu tiên của tab
    const firstMessage = $(`.message-item[data-tab="${tab}"]`).first();
    if (firstMessage.length) {
        const msgId = firstMessage.data('message-id');
        firstMessage.addClass('active');
        loadMessageDetail(msgId, tab);
        activeMessageId = msgId;
    }
});

