// ==================== SLUG GENERATION ====================

// Hàm xóa dấu tiếng Việt
function removeVietnameseTones(str) {
    if (!str) {
        return '';
    }

    const accentsMap = {
        a: /[àáạảãâầấậẩẫăằắặẳẵ]/g,
        e: /[èéẹẻẽêềếệểễ]/g,
        i: /[ìíịỉĩ]/g,
        o: /[òóọỏõôồốộổỗơờớợởỡ]/g,
        u: /[ùúụủũưừứựửữ]/g,
        y: /[ỳýỵỷỹ]/g,
        d: /[đ]/g,
        A: /[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/g,
        E: /[ÈÉẸẺẼÊỀẾỆỂỄ]/g,
        I: /[ÌÍỊỈĨ]/g,
        O: /[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/g,
        U: /[ÙÚỤỦŨƯỪỨỰỬỮ]/g,
        Y: /[ỲÝỴỶỸ]/g,
        D: /[Đ]/g
    };

    let result = str;

    Object.keys(accentsMap).forEach((key) => {
        result = result.replace(accentsMap[key], key);
    });

    return result;
}

// Tạo slug từ tiêu đề
function generateSlugFromTitle(title) {
    if (!title || title.trim() === '') {
        return '';
    }

    let slug = removeVietnameseTones(title)
        .toLowerCase()
        .replace(/[^\w\s]/g, '')      // Xóa ký tự đặc biệt
        .replace(/\s+/g, '-')         // Thay khoảng trắng bằng dấu gạch ngang
        .replace(/-+/g, '-')          // Xóa dấu gạch ngang thừa
        .replace(/^-+|-+$/g, '')      // Xóa dấu gạch ngang đầu/cuối
        .substring(0, 100);           // Giới hạn độ dài

    return slug;
}

// Kiểm tra slug hợp lệ
function isValidSlug(slug) {
    if (!slug || slug.trim() === '') {
        return false;
    }
    return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug);
}

// ==================== SLUG AUTO-GENERATE (GIỐNG RECRUITMENT) ====================

// Cập nhật slug tự động khi người dùng nhập tiêu đề
function updateSlug($form) {
    const $title = $form.find('#news_title');
    const $slug = $form.find('#slug');
    const $slugOriginal = $form.find('#slug_original');

    if (!$title.length || !$slug.length) {
        return;
    }

    const title = $title.val();
    const newSlug = generateSlugFromTitle(title);
    const oldSlug = $slug.val();

    if (newSlug === oldSlug) {
        return;
    }

    $slug.val(newSlug);

    if ($slugOriginal.length) {
        $slugOriginal.val(newSlug);
    }

    // Hiệu ứng highlight khi cập nhật slug
    $slug.css({
        backgroundColor: '#fef3c7',
        transition: 'all 0.3s ease'
    });

    setTimeout(() => {
        $slug.css('backgroundColor', '#f3f4f6');
    }, 500);
}

// Hàm tạo lại slug thủ công (tương tự recruitment)
window.regenerateSlug = function() {
    const $form = $('#newsForm');

    if (!$form.length) {
        return;
    }

    const $title = $form.find('#news_title');
    const $slug = $form.find('#slug');
    const $slugOriginal = $form.find('#slug_original');

    if (!$title.length) {
        return;
    }

    const title = $title.val();

    if (!title || title.trim() === '') {
        showToast('Vui lòng nhập tiêu đề trước khi tạo slug!', 'error');
        scrollToErrorElement($title, 120);
        return;
    }

    const newSlug = generateSlugFromTitle(title);

    $slug.val(newSlug);

    if ($slugOriginal.length) {
        $slugOriginal.val(newSlug);
    }

    // Hiệu ứng highlight xanh khi tạo lại
    $slug.css({
        backgroundColor: '#d1fae5',
        borderColor: '#10b981'
    });

    setTimeout(() => {
        $slug.css({
            backgroundColor: '#f3f4f6',
            borderColor: '#e5e7eb'
        });
    }, 500);

    showToast('Đã tạo lại slug thành công!', 'success');
};

// ==================== BIND FORM HANDLERS (GIỐNG RECRUITMENT) ====================

function bindNewsFormHandlers($form) {
    if (!$form.length || $form.data('news-init')) {
        return;
    }

    $form.data('news-init', true);

    const $title = $form.find('#news_title');
    const $slug = $form.find('#slug');

    // Cập nhật slug khi nhập tiêu đề
    $title.on('input', function() {
        updateSlug($form);
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();
    });

    // Ngăn chỉnh sửa slug trực tiếp (vì có readonly trong HTML)
    $slug.on('copy cut paste', function(e) {
        e.preventDefault();
        showToast('Slug không thể chỉnh sửa trực tiếp!', 'error');
        return false;
    });

    $slug.on('keydown', function(e) {
        e.preventDefault();
        showToast('Slug được tạo tự động từ tiêu đề!', 'info');
        return false;
    });

    // Tạo slug ban đầu nếu có tiêu đề
    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    if (initialTitle && initialTitle.trim() !== '' && (!initialSlug || initialSlug.trim() === '')) {
        updateSlug($form);
    }

    // Tooltip cho slug
    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
}

// ==================== TOAST MESSAGE (GIỐNG RECRUITMENT) ====================

function showToast(message, type) {
    // Xóa toast cũ nếu có
    $('.toast').remove();

    const toast = $('<div>')
        .addClass(`toast toast-${type}`)
        .html(message.replace(/\n/g, '<br>'))
        .hide();

    $('body').append(toast);
    toast.fadeIn(300);

    setTimeout(() => {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 4000);
}

// ==================== SCROLL TO ERROR (GIỐNG RECRUITMENT) ====================

function scrollToErrorElement($element, offset = 120) {
    if (!$element || !$element.length) return;

    // Xóa highlight cũ nếu có
    $element.removeClass('error-highlight');

    // Force reflow để animation chạy lại
    void $element[0].offsetWidth;

    // Thêm class highlight
    $element.addClass('error-highlight');

    // Scroll đến element
    const elementPosition = $element.offset().top;
    const offsetPosition = elementPosition - offset;

    $('html, body').animate({
        scrollTop: offsetPosition
    }, 500, function() {
        // Focus vào element sau khi scroll xong
        if ($element.is(':visible') && !$element.is('input[readonly]')) {
            $element.trigger('focus');
        }
    });

    // Xóa highlight sau 2 giây
    setTimeout(() => {
        $element.removeClass('error-highlight');
    }, 2000);
}



// ==================== INITIALIZATION ====================

// Khởi tạo form
function initNewsForm(scope = document) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#newsForm');

    if (!$form.length) return;

    bindNewsFormHandlers($form);
}

// ==================== EXPOSE GLOBAL FUNCTIONS ====================

// Thêm vào window.newsForm để dùng trong HTML (giống recruitment)
window.newsForm = {
    init: initNewsForm,
    generateAITitle: function() {
        const $form = $('#newsForm');
        const $title = $form.find('#news_title');

        if (!$form.length || !$title.length) {
            return;
        }

        const aiTitles = [
            'Tin tức mới nhất về công nghệ 2026 - Cập nhật xu hướng',
            'Hướng dẫn chi tiết cách sử dụng phần mềm mới nhất',
            'Thông báo quan trọng: Thay đổi chính sách bảo mật',
            'Tổng hợp tin tức nổi bật tuần qua - Đừng bỏ lỡ',
            'Chia sẻ kinh nghiệm làm việc hiệu quả từ chuyên gia',
            'Cập nhật tính năng mới - Nâng cấp hệ thống'
        ];

        $title.val(aiTitles[Math.floor(Math.random() * aiTitles.length)]);
        updateSlug($form);
        $title.removeClass('error-field');
        $title.closest('.form-group').find('.field-error-msg').remove();
        showToast('Đã tạo gợi ý tiêu đề!', 'success');
    },
    generateAIContent: function() {
        showToast('Tính năng tạo nội dung bằng AI đang phát triển!', 'info');
    },
    generateAIImage: function() {
        showToast('Tính năng tạo ảnh bằng AI đang phát triển!', 'info');
    }
};

// ==================== AUTO INIT ON DOM READY ====================

$(document).ready(function() {
    // Khởi tạo form
    window.newsForm.init(document);

    // Các phần khác (Quill, Color Picker, etc.) giữ nguyên
    // ...
});

  
// ==================== QUILL.JS INTEGRATION ====================

// Khởi tạo Quill Editor
let quillEditor = null;
let quillInitialized = false;

function initQuillEditor() {
    if (quillInitialized) return;
    
    // Lấy textarea và container
    const textarea = document.getElementById('news_content');
    const editorContainer = document.querySelector('.editor-instructions');
    
    if (!textarea || !editorContainer) return;
    
    // Tạo container cho Quill
    const quillContainer = document.createElement('div');
    quillContainer.id = 'quill-editor-container';
    quillContainer.style.cssText = `
        border: 1px solid #e5e7eb;
        border-radius: 0 0 8px 8px;
        min-height: 400px;
        background: white;
    `;
    
    // Chèn container trước textarea
    textarea.parentNode.insertBefore(quillContainer, textarea);
    
    // Ẩn textarea gốc
    textarea.style.display = 'none';
    
    // Khởi tạo Quill
    quillEditor = new Quill('#quill-editor-container', {
        theme: 'snow',
        modules: {
            toolbar: false, // Tắt toolbar mặc định của Quill
            clipboard: {
                matchVisual: false
            }
        },
        placeholder: 'Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác...'
    });
    
    // Đặt nội dung ban đầu từ textarea
    if (textarea.value) {
        quillEditor.root.innerHTML = textarea.value;
    }
    
    // Cập nhật textarea khi Quill thay đổi
    quillEditor.on('text-change', function() {
        const content = quillEditor.root.innerHTML;
        textarea.value = content;
    });
    
    quillInitialized = true;
    
    // Thiết lập các sự kiện cho toolbar custom
    initQuillToolbar();
    
    // Thêm CSS cho Quill
    addQuillStyles();
}

// Thêm CSS cho Quill Editor
function addQuillStyles() {
    const style = document.createElement('style');
    style.textContent = `
        #quill-editor-container .ql-editor {
            min-height: 400px;
            padding: 16px;
            font-family: inherit;
            font-size: inherit;
        }
        
        #quill-editor-container .ql-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        #quill-editor-container .ql-editor iframe {
            max-width: 100%;
            border-radius: 8px;
        }
        
        #quill-editor-container .ql-editor a {
            color: #3b82f6;
            text-decoration: underline;
        }
        
        #quill-editor-container .ql-editor a:hover {
            color: #2563eb;
        }
        
        #quill-editor-container .ql-editor blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 16px;
            margin: 16px 0;
            color: #4b5563;
        }
        
        #quill-editor-container .ql-editor pre {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        
        #quill-editor-container .ql-editor ul,
        #quill-editor-container .ql-editor ol {
            padding-left: 24px;
        }
        
        #quill-editor-container .ql-editor h1 {
            font-size: 2em;
            font-weight: bold;
        }
        
        #quill-editor-container .ql-editor h2 {
            font-size: 1.5em;
            font-weight: bold;
        }
        
        #quill-editor-container .ql-editor h3 {
            font-size: 1.17em;
            font-weight: bold;
        }
        
        #quill-editor-container .ql-editor h4 {
            font-size: 1em;
            font-weight: bold;
        }
    `;
    document.head.appendChild(style);
}

// ==================== QUILL TOOLBAR HANDLERS ====================

function initQuillToolbar() {
    if (!quillEditor) return;
    
    // Font Family
    $('#fontFamily').off('change').on('change', function() {
        const font = $(this).val();
        if (font && font !== 'default') {
            quillEditor.format('font', font);
        }
    });
    
    // Heading
    $('#headingSelect').off('change').on('change', function() {
        const value = $(this).val();
        if (value) {
            if (value === 'p') {
                quillEditor.format('header', false);
            } else {
                const headerLevel = parseInt(value.replace('h', ''));
                quillEditor.format('header', headerLevel);
            }
        }
    });
    
    // Bold
    $('[onclick*="wrapText(\'bold\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('bold', !quillEditor.getFormat().bold);
        updateToolbarStateQuill();
    });
    
    // Italic
    $('[onclick*="wrapText(\'italic\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('italic', !quillEditor.getFormat().italic);
        updateToolbarStateQuill();
    });
    
    // Underline
    $('[onclick*="wrapText(\'underline\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('underline', !quillEditor.getFormat().underline);
        updateToolbarStateQuill();
    });
    
    // Unordered List
    $('[onclick*="wrapText(\'insertUnorderedList\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        const format = quillEditor.getFormat();
        quillEditor.format('list', format.list === 'bullet' ? false : 'bullet');
        updateToolbarStateQuill();
    });
    
    // Ordered List
    $('[onclick*="wrapText(\'insertOrderedList\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        const format = quillEditor.getFormat();
        quillEditor.format('list', format.list === 'ordered' ? false : 'ordered');
        updateToolbarStateQuill();
    });
    
    // Alignment
    $('[onclick*="wrapText(\'justifyLeft\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('align', 'left');
        updateToolbarStateQuill();
    });
    
    $('[onclick*="wrapText(\'justifyCenter\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('align', 'center');
        updateToolbarStateQuill();
    });
    
    $('[onclick*="wrapText(\'justifyRight\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('align', 'right');
        updateToolbarStateQuill();
    });
    
    $('[onclick*="wrapText(\'justifyFull\')"]').off('click').on('click', function(e) {
        e.preventDefault();
        quillEditor.format('align', 'justify');
        updateToolbarStateQuill();
    });
}

// Cập nhật trạng thái toolbar
function updateToolbarStateQuill() {
    if (!quillEditor) return;
    
    const format = quillEditor.getFormat();
    
    // Bold
    const boldBtn = $('[onclick*="wrapText(\'bold\')"]');
    boldBtn.toggleClass('active', !!format.bold);
    
    // Italic
    const italicBtn = $('[onclick*="wrapText(\'italic\')"]');
    italicBtn.toggleClass('active', !!format.italic);
    
    // Underline
    const underlineBtn = $('[onclick*="wrapText(\'underline\')"]');
    underlineBtn.toggleClass('active', !!format.underline);
    
    // List
    const bulletBtn = $('[onclick*="wrapText(\'insertUnorderedList\')"]');
    bulletBtn.toggleClass('active', format.list === 'bullet');
    
    const orderedBtn = $('[onclick*="wrapText(\'insertOrderedList\')"]');
    orderedBtn.toggleClass('active', format.list === 'ordered');
    
    // Heading
    const headerVal = format.header;
    if (headerVal) {
        $('#headingSelect').val('h' + headerVal);
    } else {
        $('#headingSelect').val('p');
    }
    
    // Alignment
    $('[onclick*="justify"]').removeClass('active');
    if (format.align) {
        const alignMap = {
            'left': 'justifyLeft',
            'center': 'justifyCenter',
            'right': 'justifyRight',
            'justify': 'justifyFull'
        };
        if (alignMap[format.align]) {
            $('[onclick*="wrapText(\'' + alignMap[format.align] + '\')"]').addClass('active');
        }
    } else {
        $('[onclick*="wrapText(\'justifyLeft\')"]').addClass('active');
    }
}

// ==================== CÁC HÀM HỖ TRỢ CHO QUILL ====================

// Ghi đè hàm wrapText để tương thích với Quill
window.wrapText = function(action) {
    if (!quillEditor) return;
    
    const actions = {
        'bold': function() { quillEditor.format('bold', !quillEditor.getFormat().bold); },
        'italic': function() { quillEditor.format('italic', !quillEditor.getFormat().italic); },
        'underline': function() { quillEditor.format('underline', !quillEditor.getFormat().underline); },
        'insertUnorderedList': function() {
            const format = quillEditor.getFormat();
            quillEditor.format('list', format.list === 'bullet' ? false : 'bullet');
        },
        'insertOrderedList': function() {
            const format = quillEditor.getFormat();
            quillEditor.format('list', format.list === 'ordered' ? false : 'ordered');
        },
        'justifyLeft': function() { quillEditor.format('align', 'left'); },
        'justifyCenter': function() { quillEditor.format('align', 'center'); },
        'justifyRight': function() { quillEditor.format('align', 'right'); },
        'justifyFull': function() { quillEditor.format('align', 'justify'); }
    };
    
    if (actions[action]) {
        actions[action]();
        setTimeout(updateToolbarStateQuill, 10);
    }
};

// Ghi đè hàm applyHeadingToTextarea
window.applyHeadingToTextarea = function(value) {
    if (!quillEditor) return;
    
    if (value === 'p') {
        quillEditor.format('header', false);
    } else if (value && value.startsWith('h')) {
        const level = parseInt(value.replace('h', ''));
        quillEditor.format('header', level);
    }
    setTimeout(updateToolbarStateQuill, 10);
};

// Ghi đè hàm insertImageToTextarea
window.insertImageToTextarea = function() {
    if (!quillEditor) return;
    
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const range = quillEditor.getSelection();
                if (range) {
                    quillEditor.insertEmbed(range.index, 'image', ev.target.result);
                } else {
                    quillEditor.root.innerHTML += `<img src="${ev.target.result}" alt="image">`;
                }
            };
            reader.readAsDataURL(file);
        }
    };
    input.click();
};

// Ghi đè hàm openVideoModalForTextarea
window.openVideoModalForTextarea = function() {
    if (!quillEditor) return;
    $('#videoModal').show();
};

// Ghi đè hàm insertVideo
window.insertVideo = function() {
    if (!quillEditor) return;
    
    const fileInput = document.getElementById('videoFileInput');
    const youtubeUrl = document.getElementById('youtubeUrl');
    
    const currentType = $('.video-type-active').data('type');
    
    if (currentType === 'file' && fileInput.files[0]) {
        const file = fileInput.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const videoHtml = `<video controls style="max-width: 100%; border-radius: 8px;">
                <source src="${e.target.result}" type="${file.type}">
                Your browser does not support the video tag.
            </video>`;
            const range = quillEditor.getSelection();
            if (range) {
                quillEditor.insertEmbed(range.index, 'html', videoHtml);
            } else {
                quillEditor.root.innerHTML += videoHtml;
            }
            closeVideoModal();
        };
        reader.readAsDataURL(file);
    } else if (currentType === 'youtube' && youtubeUrl.value) {
        const url = youtubeUrl.value.trim();
        const videoId = getYoutubeId(url);
        if (videoId) {
            const embedHtml = `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" 
                frameborder="0" allowfullscreen style="border-radius: 8px;"></iframe>`;
            const range = quillEditor.getSelection();
            if (range) {
                quillEditor.insertEmbed(range.index, 'html', embedHtml);
            } else {
                quillEditor.root.innerHTML += embedHtml;
            }
            closeVideoModal();
        } else {
            showToast('Link YouTube không hợp lệ!', 'error');
        }
    } else {
        showToast('Vui lòng chọn video hoặc nhập link YouTube!', 'error');
    }
};

// Ghi đè hàm createLinkForTextarea
window.createLinkForTextarea = function() {
    if (!quillEditor) return;
    
    const url = prompt('Nhập URL link:', 'https://');
    if (url && url.trim()) {
        const range = quillEditor.getSelection();
        if (range && range.length > 0) {
            quillEditor.format('link', url.trim());
        } else {
            const text = prompt('Nhập text hiển thị:', 'Xem thêm');
            if (text && text.trim()) {
                const index = range ? range.index : quillEditor.getLength();
                quillEditor.insertText(index, text.trim());
                quillEditor.setSelection(index, text.length);
                quillEditor.format('link', url.trim());
            }
        }
    }
};

// Ghi đè hàm removeTextareaFormat
window.removeTextareaFormat = function() {
    if (!quillEditor) return;
    
    const range = quillEditor.getSelection();
    if (range) {
        quillEditor.removeFormat(range.index, range.length);
    } else {
        quillEditor.root.innerHTML = quillEditor.root.innerHTML.replace(/<[^>]*>/g, '');
    }
    showToast('Đã xóa định dạng HTML!', 'success');
};

// Ghi đè hàm syncEditorContent
window.syncEditorContent = function() {
    if (quillEditor) {
        const textarea = document.getElementById('news_content');
        if (textarea) {
            textarea.value = quillEditor.root.innerHTML;
        }
    }
};

// Ghi đè hàm generateAIContent
window.generateAIContent = function() {
    if (!quillEditor) return;
    
    // Hiển thị loading
    showToast('Đang tạo nội dung bằng AI...', 'info');
    
    // Mô phỏng gọi API AI
    setTimeout(function() {
        const content = `
            <h2>Tiêu đề bài viết</h2>
            <p>Đây là nội dung được tạo tự động bởi AI. Bạn có thể chỉnh sửa để phù hợp với nhu cầu của mình.</p>
            <p><strong>Lưu ý:</strong> Nội dung này chỉ mang tính tham khảo, vui lòng kiểm tra lại trước khi đăng.</p>
            <ul>
                <li>Điểm nổi bật 1</li>
                <li>Điểm nổi bật 2</li>
                <li>Điểm nổi bật 3</li>
            </ul>
            <p>Liên hệ để biết thêm chi tiết.</p>
        `;
        
        const range = quillEditor.getSelection();
        if (range) {
            quillEditor.insertText(range.index, content);
        } else {
            quillEditor.root.innerHTML += content;
        }
        
        showToast('Đã tạo nội dung bằng AI!', 'success');
    }, 1500);
};

// ==================== MÀU SẮC CHO QUILL ====================

// Ghi đè hàm applyColor cho Quill
function applyColorToQuill(color, isHighlight) {
    if (!quillEditor) return;
    
    if (isHighlight) {
        quillEditor.format('background', color);
        $('#highlightIndicator').css('background-color', color || '#ffffff');
    } else {
        quillEditor.format('color', color);
        $('#colorIndicator').css('background-color', color || '#000000');
    }
}

// Ghi đè các sự kiện màu sắc
function initQuillColorPickers() {
    const themeColors = [
        '#000000', '#E74C3C', '#E67E22', '#F1C40F', '#2ECC71', '#3498DB', '#9B59B6', '#1ABC9C', '#E84393', '#7F8C8D',
        '#FFFFFF', '#C0392B', '#D35400', '#F39C12', '#27AE60', '#2980B9', '#8E44AD', '#16A085', '#E91E63', '#34495E'
    ];
    
    const standardColors = [
        '#000000', '#E74C3C', '#E67E22', '#F1C40F', '#2ECC71', '#3498DB', '#9B59B6', '#1ABC9C', '#E84393', '#7F8C8D',
        '#7F8C8D', '#BDC3C7', '#95A5A6', '#F5B041', '#58D68D', '#5DADE2', '#AF7AC5', '#76D7C4', '#F1948A', '#85929E'
    ];
    
    function renderColorGridsQuill() {
        // Text color grids
        const $themeGrid = $('#colorDropdown .theme-colors');
        const $standardGrid = $('#colorDropdown .standard-colors');
        
        $themeGrid.empty();
        $standardGrid.empty();
        
        themeColors.forEach(color => {
            $themeGrid.append(`<div class="color-item" style="background-color: ${color};" data-color="${color}"></div>`);
        });
        
        standardColors.forEach(color => {
            $standardGrid.append(`<div class="color-item" style="background-color: ${color};" data-color="${color}"></div>`);
        });
        
        // Highlight color grids
        const $highlightThemeGrid = $('#highlightDropdown .highlight-theme-colors');
        const $highlightStandardGrid = $('#highlightDropdown .highlight-standard-colors');
        
        $highlightThemeGrid.empty();
        $highlightStandardGrid.empty();
        
        themeColors.forEach(color => {
            $highlightThemeGrid.append(`<div class="color-item" style="background-color: ${color};" data-color="${color}"></div>`);
        });
        
        standardColors.forEach(color => {
            $highlightStandardGrid.append(`<div class="color-item" style="background-color: ${color};" data-color="${color}"></div>`);
        });
    }
    
    renderColorGridsQuill();
    
    // Toggle dropdown text color
    $('#textColorBtn').off('click').on('click', function(e) {
        e.stopPropagation();
        $('#colorDropdown').toggle();
        $('#highlightDropdown').hide();
    });
    
    // Toggle dropdown highlight color
    $('#highlightColorBtn').off('click').on('click', function(e) {
        e.stopPropagation();
        $('#highlightDropdown').toggle();
        $('#colorDropdown').hide();
    });
    
    // Apply text color
    $(document).off('click', '.color-item').on('click', '.color-item', function(e) {
        const color = $(this).data('color');
        const isHighlight = $(this).closest('#highlightDropdown').length > 0;
        
        applyColorToQuill(color, isHighlight);
        
        $('#colorDropdown').hide();
        $('#highlightDropdown').hide();
        setTimeout(updateToolbarStateQuill, 10);
    });
    
    // No color options
    $('#noColorOption').off('click').on('click', function() {
        applyColorToQuill(null, false);
        $('#colorDropdown').hide();
    });
    
    $('#noHighlightOption').off('click').on('click', function() {
        applyColorToQuill(null, true);
        $('#highlightDropdown').hide();
    });
    
    // More colors options
    $('#moreColorsOption, #moreHighlightColorsOption').off('click').on('click', function() {
        const isHighlight = $(this).attr('id') === 'moreHighlightColorsOption';
        showMoreColorsModalForQuill(isHighlight);
        $('#colorDropdown').hide();
        $('#highlightDropdown').hide();
    });
}

function showMoreColorsModalForQuill(isHighlight) {
    const modalHtml = `
        <div class="more-colors-modal" id="moreColorsModal">
            <div class="more-colors-content">
                <h3>${isHighlight ? 'Màu nền' : 'Màu chữ'}</h3>
                <div class="color-preview">
                    <div class="color-preview-box" id="colorPreviewBox" style="background-color: #000000;"></div>
                    <div class="color-values">
                        <input type="text" id="colorHex" placeholder="#000000" value="#000000">
                        <input type="text" id="colorRgb" placeholder="rgb(0, 0, 0)" readonly>
                    </div>
                </div>
                <div class="color-slider">
                    <label>Hue</label>
                    <input type="range" id="hueSlider" min="0" max="360" value="0">
                </div>
                <div class="color-slider">
                    <label>Saturation</label>
                    <input type="range" id="satSlider" min="0" max="100" value="100">
                </div>
                <div class="color-slider">
                    <label>Lightness</label>
                    <input type="range" id="lightSlider" min="0" max="100" value="50">
                </div>
                <div class="modal-buttons">
                    <button class="btn-secondary" onclick="closeMoreColorsModal()">Hủy</button>
                    <button class="btn-primary" onclick="applyMoreColorsColorForQuill(${isHighlight})">OK</button>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modalHtml);
    
    function updateColor() {
        const hue = $('#hueSlider').val();
        const sat = $('#satSlider').val();
        const light = $('#lightSlider').val();
        const color = `hsl(${hue}, ${sat}%, ${light}%)`;
        $('#colorPreviewBox').css('background-color', color);
        
        const rgb = hslToRgb(hue, sat, light);
        const hex = rgbToHex(rgb.r, rgb.g, rgb.b);
        $('#colorHex').val(hex);
        $('#colorRgb').val(`rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`);
    }
    
    $('#hueSlider, #satSlider, #lightSlider').on('input', updateColor);
    
    $('#colorHex').on('input', function() {
        let hex = $(this).val();
        if (hex && /^#[0-9A-F]{6}$/i.test(hex)) {
            const rgb = hexToRgb(hex);
            if (rgb) {
                const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
                $('#hueSlider').val(hsl.h);
                $('#satSlider').val(hsl.s);
                $('#lightSlider').val(hsl.l);
                $('#colorPreviewBox').css('background-color', hex);
            }
        }
    });
    
    updateColor();
}

window.applyMoreColorsColorForQuill = function(isHighlight) {
    const color = $('#colorPreviewBox').css('background-color');
    applyColorToQuill(color, isHighlight);
    closeMoreColorsModal();
};

// ==================== VIDEO MODAL CHO QUILL ====================

// Ghi đè các hàm video modal
window.selectVideoType = function(type) {
    $('.video-type-active').removeClass('video-type-active');
    
    if (type === 'file') {
        $('#btnFileType').addClass('video-type-active').css('background', '#3b82f6').css('color', 'white');
        $('#btnYoutubeType').css('background', '#e5e7eb').css('color', 'inherit');
        $('#videoFileUpload').show();
        $('#videoYoutubeInput').hide();
    } else {
        $('#btnYoutubeType').addClass('video-type-active').css('background', '#3b82f6').css('color', 'white');
        $('#btnFileType').css('background', '#e5e7eb').css('color', 'inherit');
        $('#videoFileUpload').hide();
        $('#videoYoutubeInput').show();
    }
};

window.closeVideoModal = function() {
    $('#videoModal').hide();
    $('#videoFileInput').val('');
    $('#youtubeUrl').val('');
    $('#videoFileUpload').hide();
    $('#videoYoutubeInput').hide();
    $('.video-type-active').removeClass('video-type-active');
};

// ==================== KHỞI TẠO ====================

// Khởi tạo Quill khi DOM ready
$(document).ready(function() {
    // Chờ Quill.js load xong
    if (typeof Quill !== 'undefined') {
        initQuillEditor();
        initQuillColorPickers();
        
        // Cập nhật trạng thái toolbar ban đầu
        setTimeout(updateToolbarStateQuill, 100);
    } else {
        // Nếu Quill chưa load, thử lại sau
        let attempts = 0;
        const maxAttempts = 10;
        const checkQuill = setInterval(function() {
            if (typeof Quill !== 'undefined') {
                clearInterval(checkQuill);
                initQuillEditor();
                initQuillColorPickers();
                setTimeout(updateToolbarStateQuill, 100);
            } else if (attempts >= maxAttempts) {
                clearInterval(checkQuill);
                console.warn('Quill.js không được tải, sử dụng textarea thông thường.');
            }
            attempts++;
        }, 500);
    }
});

// Cập nhật trạng thái khi focus vào editor
$(document).on('focus', '#quill-editor-container .ql-editor', function() {
    setTimeout(updateToolbarStateQuill, 10);
});
