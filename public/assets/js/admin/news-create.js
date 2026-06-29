// ==================== SLUG FUNCTIONS ====================

// Hàm loại bỏ dấu tiếng Việt
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

// Tạo slug từ tiêu đề, loại bỏ dấu tiếng Việt, chuyển thành chữ thường, thay khoảng trắng bằng dấu gạch ngang, và loại bỏ ký tự đặc biệt
function generateSlugFromTitle(title) {
    if (!title || title.trim() === '') {
        return '';
    }

    let slug = removeVietnameseTones(title)
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '')
        .substring(0, 100);

    return slug;
}

// Kiểm tra slug hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)
function isValidSlug(slug) {
    if (!slug || slug.trim() === '') {
        return false;
    }
    return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug);
}

// Cập nhật slug tự động khi người dùng nhập tiêu đề (song song)
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

    // Nếu slug không thay đổi thì không làm gì
    if (newSlug === oldSlug) {
        return;
    }

    // Cập nhật slug
    $slug.val(newSlug);

    // Cập nhật slug_original (hidden field)
    if ($slugOriginal.length) {
        $slugOriginal.val(newSlug);
    }

    // Hiệu ứng highlight khi cập nhật
    $slug.css({
        backgroundColor: '#fef3c7',
        transition: 'all 0.3s ease'
    });

    setTimeout(() => {
        $slug.css('backgroundColor', '#f3f4f6');
    }, 500);
}

// ==================== SHOW TOAST ====================

// Hiển thị toast message với kiểu (success, error, info)
function showToast(message, type) {
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

// Scroll đến element bị lỗi với hiệu ứng highlight
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

// ==================== VALIDATION & REQUIRED FIELDS ====================

// Mapping field selectors cho các trường required
const requiredFieldsMap = [
    {
        selector: '#news_title',
        name: 'title',
        label: 'title',
        getMessage: function() { return 'Vui lòng nhập tiêu đề tin tức'; }
    },
    {
        selector: 'input[name="author"]',
        name: 'author',
        label: 'author',
        getMessage: function() { return 'Vui lòng nhập tên tác giả'; }
    },
    {
        selector: '#news_content',
        name: 'content',
        label: 'content',
        getMessage: function() { return 'Vui lòng nhập nội dung bài viết'; }
    }
];

// Lấy tất cả các field required trong form
function getRequiredFields($form) {
    const $requiredFields = [];

    // Tìm tất cả label có chứa span.required
    $form.find('label').each(function() {
        const $label = $(this);
        if ($label.find('.required').length) {
            const forAttr = $label.attr('for');
            let $field = null;

            if (forAttr) {
                $field = $form.find('#' + forAttr);
            } else {
                // Tìm input/textarea kế tiếp hoặc trong cùng container
                $field = $label.closest('.form-group').find('input, textarea, select').first();
            }

            if ($field && $field.length) {
                const fieldName = $field.attr('name') || $field.attr('id');
                $requiredFields.push({
                    $element: $field,
                    label: $label.clone().children().remove().end().text().trim(),
                    name: fieldName
                });
            }
        }
    });

    return $requiredFields;
}

// ==================== VALIDATE CLIENT FORM ====================

// Validate form client-side trước khi submit
function validateClientForm($form) {
    const requiredFields = getRequiredFields($form);
    const errors = [];
    let firstErrorElement = null;

    // Xóa thông báo lỗi cũ
    $('.field-error-msg').remove();
    $('.error-field').removeClass('error-field');

    // Kiểm tra từng field required
    requiredFields.forEach(field => {
        const $field = field.$element;
        let value = '';

        if ($field.is('select')) {
            value = $field.val() || '';
        } else if ($field.is('input[type="checkbox"]')) {
            value = $field.is(':checked') ? 'checked' : '';
        } else {
            value = $field.val() || '';
        }

        let isValid = true;
        let errorMessage = '';

        // Kiểm tra theo loại field
        if ($field.attr('type') === 'number') {
            if (!value || parseInt(value, 10) <= 0) {
                isValid = false;
                errorMessage = `Vui lòng nhập ${field.label}`;
            }
        } else {
            // Kiểm tra nội dung cho textarea
            if ($field.is('textarea')) {
                // Kiểm tra nếu là textarea và có Quill Editor
                const quillContent = $field.val();
                const strippedContent = quillContent ? quillContent.replace(/<[^>]*>/g, '').trim() : '';
                if (!strippedContent) {
                    isValid = false;
                    errorMessage = `Vui lòng nhập ${field.label}`;
                }
            } else if (!value || value.trim() === '') {
                isValid = false;
                errorMessage = `Vui lòng nhập ${field.label}`;
            }
        }

        if (!isValid) {
            errors.push({ msg: errorMessage, field: $field });
            $field.addClass('error-field');

            const $errorMsg = $('<div>')
                .addClass('field-error-msg')
                .html('⚠️ ' + errorMessage);

            // Tìm vị trí thích hợp để thêm thông báo
            const $parentGroup = $field.closest('.form-group');
            if ($parentGroup.length) {
                $parentGroup.find('.field-error-msg').remove();
                if ($field.is('input[type="file"]')) {
                    $field.parent().append($errorMsg);
                } else if ($field.is('textarea') && $field.closest('.editor-instructions').length) {
                    $field.closest('.form-group').append($errorMsg);
                } else {
                    $field.after($errorMsg);
                }
            } else {
                $field.after($errorMsg);
            }

            if (!firstErrorElement) {
                firstErrorElement = $field;
            }
        }
    });

    // Kiểm tra slug (không required nhưng cần validate nếu có)
    const $slug = $form.find('#slug');
    const slug = $slug.val();
    if (slug && !isValidSlug(slug)) {
        errors.push({ msg: 'Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)', field: $slug });
        $slug.addClass('error-field');
        if (!firstErrorElement) firstErrorElement = $slug;
    }

    // Hiển thị lỗi và scroll
    if (errors.length > 0) {
        const errorMessages = errors.map(e => e.msg);
        showToast('⚠️ Vui lòng kiểm tra lại:\n• ' + errorMessages.join('\n• '), 'error');

        if (firstErrorElement) {
            scrollToErrorElement(firstErrorElement, 120);
        }
        return false;
    }

    return true;
}

// ==================== DISPLAY SERVER ERRORS ====================

// Kiểm tra và hiển thị lỗi từ server (PHP session errors)
function displayServerErrors(errors, $form) {
    if (!errors || errors.length === 0) return false;

    // Xóa các thông báo lỗi cũ
    $('.field-error-msg').remove();
    $('.error-field').removeClass('error-field');

    const requiredFields = getRequiredFields($form);
    let firstErrorElement = null;
    let errorList = [];

    // Tạo map field name => field info
    const fieldMap = {};
    requiredFields.forEach(field => {
        if (field.name) {
            fieldMap[field.name] = field;
        }
        // Cũng map theo id
        if (field.$element.attr('id')) {
            fieldMap[field.$element.attr('id')] = field;
        }
    });

    // Thêm các field không có required nhưng vẫn có thể có lỗi
    const allFieldsMap = {
        'slug': { $element: $('#slug'), label: 'Slug' },
        'category_id': { $element: $('select[name="category_id"]'), label: 'Danh mục' },
        'author_id': { $element: $('select[name="author_id"]'), label: 'Tác giả' },
        'publish_date': { $element: $('input[name="publish_date"]'), label: 'Ngày đăng' },
        'status': { $element: $('select[name="status"]'), label: 'Trạng thái' },
        'featured_image': { $element: $('#imageInput'), label: 'Ảnh đại diện' }
    };

    Object.assign(fieldMap, allFieldsMap);

    // Xử lý từng lỗi
    errors.forEach(error => {
        errorList.push(error);

        let $element = null;
        let fieldLabel = '';

        // Tìm field dựa trên nội dung lỗi
        const errorLower = error.toLowerCase();

        if (errorLower.includes('tiêu đề') || errorLower.includes('title')) {
            $element = $('#news_title');
            fieldLabel = 'Tiêu đề tin tức';
        } else if (errorLower.includes('nội dung') || errorLower.includes('content')) {
            $element = $('#news_content');
            fieldLabel = 'Nội dung bài viết';
        } else if (errorLower.includes('tác giả') || errorLower.includes('author')) {
            $element = $('input[name="author"]');
            fieldLabel = 'Tên tác giả';
        } else if (errorLower.includes('slug')) {
            $element = $('#slug');
            fieldLabel = 'Slug';
        } else if (errorLower.includes('danh mục') || errorLower.includes('category')) {
            $element = $('select[name="category_id"]');
            fieldLabel = 'Danh mục';
        } else if (errorLower.includes('ngày đăng') || errorLower.includes('publish_date')) {
            $element = $('input[name="publish_date"]');
            fieldLabel = 'Ngày đăng';
        } else if (errorLower.includes('ảnh') || errorLower.includes('image') || errorLower.includes('featured')) {
            $element = $('#uploadBox');
            fieldLabel = 'Ảnh đại diện';
        } else {
            // Tìm theo field name trong map
            for (let key in fieldMap) {
                if (errorLower.includes(key.toLowerCase())) {
                    $element = fieldMap[key].$element;
                    fieldLabel = fieldMap[key].label;
                    break;
                }
            }
        }

        if ($element && $element.length) {
            $element.addClass('error-field');

            // Thêm thông báo lỗi bên dưới field
            const $errorMsg = $('<div>')
                .addClass('field-error-msg')
                .html('⚠️ ' + error);

            // Tìm vị trí thích hợp để thêm thông báo
            const $parentGroup = $element.closest('.form-group');
            if ($parentGroup.length) {
                $parentGroup.find('.field-error-msg').remove();
                if ($element.is('input[type="file"]')) {
                    $element.parent().append($errorMsg);
                } else if ($element.is('textarea') && $element.closest('.editor-instructions').length) {
                    $element.closest('.form-group').append($errorMsg);
                } else {
                    $element.after($errorMsg);
                }
            } else {
                $element.after($errorMsg);
            }

            // Lưu lại element lỗi đầu tiên
            if (!firstErrorElement) {
                firstErrorElement = $element;
            }
        }
    });

    // Hiển thị toast tổng hợp
    if (errorList.length > 0) {
        showToast('⚠️ Có ' + errorList.length + ' lỗi cần sửa:\n• ' + errorList.join('\n• '), 'error');
    }

    // Scroll đến lỗi đầu tiên
    if (firstErrorElement && firstErrorElement.length) {
        setTimeout(function() {
            scrollToErrorElement(firstErrorElement, 120);
        }, 200);
        return true;
    }

    return false;
}

// ==================== BIND FORM HANDLERS ====================

function bindNewsFormHandlers($form) {
    if (!$form.length || $form.data('news-init')) {
        return;
    }

    $form.data('news-init', true);

    const $title = $form.find('#news_title');
    const $slug = $form.find('#slug');

    // === SLUG TỰ ĐỘNG CẬP NHẬT SONG SONG VỚI TIÊU ĐỀ ===
    // Khi người dùng nhập tiêu đề, slug tự động cập nhật theo thời gian thực
    $title.on('input', function() {
        updateSlug($form); // Cập nhật slug song song
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();
    });

    // Ngăn chỉnh sửa slug trực tiếp (readonly)
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

    // Xóa lỗi khi người dùng nhập vào các field required
    $form.find('input, textarea, select').on('input change', function() {
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();

        // Nếu là textarea, cũng kiểm tra content từ Quill
        if ($(this).is('textarea') && $(this).attr('id') === 'news_content') {
            const content = $(this).val();
            const strippedContent = content ? content.replace(/<[^>]*>/g, '').trim() : '';
            if (strippedContent) {
                $(this).removeClass('error-field');
                $(this).closest('.form-group').find('.field-error-msg').remove();
            }
        }
    });

    // Xử lý submit form
    $form.on('submit', function(e) {
        // Cập nhật slug lần cuối trước khi submit
        updateSlug($form);

        // Đồng bộ nội dung từ Quill nếu có
        if (typeof syncEditorContent === 'function') {
            syncEditorContent();
        }

        // Validate client trước khi submit
        if (!validateClientForm($form)) {
            e.preventDefault();
        }
    });

    // Upload ảnh
    $form.find('#uploadBox').on('click', function() {
        $form.find('#imageInput').trigger('click');
    });

    $form.find('#imageInput').on('change', function() {
        previewImage(this, $form);
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();
        // Xóa lỗi của uploadBox nếu có
        $('#uploadBox').removeClass('error-field');
        $('#uploadBox').closest('.form-group').find('.field-error-msg').remove();
    });

    // Tạo slug ban đầu nếu có tiêu đề (khi load trang với dữ liệu cũ)
    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    if (initialTitle && initialTitle.trim() !== '' && (!initialSlug || initialSlug.trim() === '')) {
        updateSlug($form);
    }

    // Tooltip cho slug
    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
}

// ==================== PREVIEW IMAGE ====================

// Preview ảnh khi người dùng chọn file
function previewImage(input, $form) {
    const $preview = $form.find('#imagePreview');
    const $previewImg = $form.find('#previewImg');
    const $uploadBox = $form.find('#uploadBox');
    const $uploadText = $form.find('#uploadText');
    const $uploadInfo = $form.find('#uploadInfo');

    if (!input.files || !input.files[0]) {
        return;
    }

    // Kiểm tra kích thước ảnh (Max 5MB)
    if (input.files[0].size > 5 * 1024 * 1024) {
        showToast('Ảnh không được vượt quá 5MB!', 'error');
        $(input).val('');
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {
        $previewImg.attr('src', e.target.result);
        $preview.show();
        $uploadBox.css('opacity', '0.5');
        $uploadText.html('Đã chọn ảnh: ' + input.files[0].name);
        $uploadInfo.html('Click để đổi ảnh khác');
        // Xóa lỗi nếu có
        $uploadBox.removeClass('error-field');
        $uploadBox.closest('.form-group').find('.field-error-msg').remove();
    };

    reader.readAsDataURL(input.files[0]);
}

// Xóa ảnh đã chọn
window.removeImage = function() {
    const $form = $('#newsForm');
    const $preview = $form.find('#imagePreview');
    const $previewImg = $form.find('#previewImg');
    const $uploadBox = $form.find('#uploadBox');
    const $uploadText = $form.find('#uploadText');
    const $uploadInfo = $form.find('#uploadInfo');
    const $imageInput = $form.find('#imageInput');

    $previewImg.attr('src', '#');
    $preview.hide();
    $uploadBox.css('opacity', '1');
    $uploadText.html('Tải lên ảnh đại diện (JPG, PNG, WEBP)');
    $uploadInfo.html('Kích thước khuyến nghị: 1200x630px (Max 5MB)');
    $imageInput.val('');
    $imageInput.removeClass('error-field');
    $uploadBox.removeClass('error-field');
    $uploadBox.closest('.form-group').find('.field-error-msg').remove();
};

// ==================== INITIALIZATION ====================

/// Khởi tạo form
function initNewsForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#newsForm');

    if (!$form.length) return;

    bindNewsFormHandlers($form);

    // Hiển thị lỗi từ server (PHP session) nếu có
    if (serverErrors && serverErrors.length > 0) {
        displayServerErrors(serverErrors, $form);
    }
}

// ==================== EXPOSE GLOBAL FUNCTIONS ====================

// Thêm vào window.newsForm để dùng trong HTML
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
        // Cập nhật slug ngay sau khi tạo tiêu đề AI
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

// Hàm tạo lại slug (có thể gọi từ console hoặc thêm nút trong HTML)
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
        borderColor: '#10b981',
        transition: 'all 0.3s ease'
    });

    setTimeout(() => {
        $slug.css({
            backgroundColor: '#f3f4f6',
            borderColor: '#e5e7eb'
        });
    }, 500);

    showToast('Đã tạo lại slug thành công!', 'success');
};

// ==================== AUTO INIT ON DOM READY ====================

$(document).ready(function() {
    // Lấy errors từ PHP session (nếu có)
    const serverErrors = window.serverErrors || null;

    // Khởi tạo form
    window.newsForm.init(document, serverErrors);
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

// ==================== QUILL TOOLBAR HANDLERS (jQuery) ====================

function initQuillToolbar() {
    if (!quillEditor) return;

    // === FONT FAMILY - Thay đổi kiểu chữ ===
    $('#fontFamily').off('change').on('change', function() {
        const font = $(this).val();
        if (font && font !== 'default') {
            quillEditor.format('font', font);
        }
    });

    // === HEADING ===
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
        updateToolbarStateQuill();
    });

// === BOLD ===
    $('#btnBold').off('click').on('click', function(e) {
        e.preventDefault();
        const range = quillEditor.getSelection();
        if (range && range.length > 0) {
            // Nếu có text được chọn, áp dụng định dạng bold
            const isBold = quillEditor.getFormat(range.index, range.length).bold;
            quillEditor.formatText(range.index, range.length, 'bold', !isBold);
        } else {
            // Nếu không có text được chọn, toggle trạng thái bold
            const currentFormat = quillEditor.getFormat();
            quillEditor.format('bold', !currentFormat.bold);
        }
        updateToolbarStateQuill();
        syncEditorContent();
    });

    // === ITALIC ===
    $('#btnItalic').off('click').on('click', function(e) {
        e.preventDefault();
        const range = quillEditor.getSelection();
        if (range && range.length > 0) {
            // Nếu có text được chọn, áp dụng định dạng italic
            const isItalic = quillEditor.getFormat(range.index, range.length).italic;
            quillEditor.formatText(range.index, range.length, 'italic', !isItalic);
        } else {
            // Nếu không có text được chọn, toggle trạng thái italic
            const currentFormat = quillEditor.getFormat();
            quillEditor.format('italic', !currentFormat.italic);
        }
        updateToolbarStateQuill();
        syncEditorContent();
    });

    // === UNDERLINE ===
    $('#btnUnderline').off('click').on('click', function(e) {
        e.preventDefault();
        const range = quillEditor.getSelection();
        if (range && range.length > 0) {
            // Nếu có text được chọn, áp dụng định dạng underline
            const isUnderline = quillEditor.getFormat(range.index, range.length).underline;
            quillEditor.formatText(range.index, range.length, 'underline', !isUnderline);
        } else {
            // Nếu không có text được chọn, toggle trạng thái underline
            const currentFormat = quillEditor.getFormat();
            quillEditor.format('underline', !currentFormat.underline);
        }
        updateToolbarStateQuill();
        syncEditorContent();
    });

    // === UNORDERED LIST ===
    $('[onclick*="insertUnorderedList"]').off('click').on('click', function(e) {
        e.preventDefault();
        const format = quillEditor.getFormat();
        quillEditor.format('list', format.list === 'bullet' ? false : 'bullet');
        updateToolbarStateQuill();
    });

    // === ORDERED LIST ===
    $('[onclick*="insertOrderedList"]').off('click').on('click', function(e) {
        e.preventDefault();
        const format = quillEditor.getFormat();
        quillEditor.format('list', format.list === 'ordered' ? false : 'ordered');
        updateToolbarStateQuill();
    });

    // === ALIGNMENT - Căn lề văn bản (giống MS Word) ===
    // Note: Alignment is now handled in wrapText() function to avoid conflicts


    

    // === TEXT COLOR - Hiển thị bảng màu ===
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

    // Đóng dropdown khi click ra ngoài
    $(document).off('click.quillDropdown').on('click.quillDropdown', function(e) {
        if (!$(e.target).closest('.color-picker-wrapper').length) {
            $('#colorDropdown').hide();
            $('#highlightDropdown').hide();
        }
    });
}

// ==================== CẬP NHẬT TRẠNG THÁI TOOLBAR ====================

// Cập nhật trạng thái toolbar
function updateToolbarStateQuill() {
    if (!quillEditor) return;

    const format = quillEditor.getFormat();

    // Bold
    $('#btnBold').toggleClass('active', !!format.bold);

    // Italic
    $('#btnItalic').toggleClass('active', !!format.italic);

    // Underline
    $('#btnUnderline').toggleClass('active', !!format.underline);

    // List
    $('[onclick*="insertUnorderedList"]').toggleClass('active', format.list === 'bullet');
    $('[onclick*="insertOrderedList"]').toggleClass('active', format.list === 'ordered');

    // Heading
    const headerVal = format.header;
    if (headerVal) {
        $('#headingSelect').val('h' + headerVal);
    } else {
        $('#headingSelect').val('p');
    }

    // === ALIGNMENT - Cập nhật trạng thái căn lề (giống MS Word) ===
    // Xóa tất cả active class của các nút căn lề
    $('[onclick*="justify"]').removeClass('active');

    // Lấy giá trị align hiện tại
    const alignValue = format.align;

    // Map align value sang selector và set active
    // Nếu không có align (undefined hoặc null) hoặc align là 'left' => active nút căn trái
    if (!alignValue || alignValue === 'left') {
        $('[onclick*="justifyLeft"]').addClass('active');
    } else if (alignValue === 'center') {
        $('[onclick*="justifyCenter"]').addClass('active');
    } else if (alignValue === 'right') {
        $('[onclick*="justifyRight"]').addClass('active');
    }

    // Font family
    if (format.font) {
        $('#fontFamily').val(format.font);
    } else {
        $('#fontFamily').val('Arial');
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
        'justifyLeft': function() {
            const currentAlign = quillEditor.getFormat().align;
            // Nếu đang căn trái hoặc chưa có align -> xóa align (về mặc định)
            // Nếu đang căn giữa hoặc phải -> chuyển về căn trái
            if (!currentAlign || currentAlign === 'left') {
                quillEditor.format('align', false);
            } else {
                quillEditor.format('align', 'left');
            }
        },
        'justifyCenter': function() {
            const currentAlign = quillEditor.getFormat().align;
            quillEditor.format('align', currentAlign === 'center' ? false : 'center');
        },
        'justifyRight': function() {
            const currentAlign = quillEditor.getFormat().align;
            quillEditor.format('align', currentAlign === 'right' ? false : 'right');
        }
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

// ==================== VIDEO MODAL ====================

// Mở modal chèn video
window.openVideoModalForTextarea = function() {
    if (!quillEditor) {
        showToast('Vui lòng đợi editor tải xong!', 'error');
        return;
    }
    const modal = document.getElementById('videoModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        // Focus vào input YouTube sau khi modal hiển thị
        setTimeout(function() {
            const youtubeInput = document.getElementById('youtubeUrl');
            if (youtubeInput) {
                youtubeInput.focus();
            }
        }, 350);
    }
};

// Đóng modal video
window.closeVideoModal = function() {
    const modal = document.getElementById('videoModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        // Reset fields
        const youtubeInput = document.getElementById('youtubeUrl');
        const fileInput = document.getElementById('videoFileInput');
        const fileName = document.getElementById('videoFileName');

        if (youtubeInput) youtubeInput.value = '';
        if (fileInput) fileInput.value = '';
        if (fileName) {
            fileName.style.display = 'none';
            fileName.textContent = '';
        }
    }
};

// Xử lý upload video từ file - Sử dụng jQuery
window.handleVideoFileUpload = function(event) {
    const file = event.target.files && event.target.files[0];
    if (!file) return;

    // Kiểm tra kích thước video (max 50MB)
    if (file.size > 50 * 1024 * 1024) {
        showToast('Video không được vượt quá 50MB!', 'error');
        event.target.value = '';
        return;
    }

    // Kiểm tra định dạng video
    const validTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
    if (!validTypes.includes(file.type)) {
        showToast('Định dạng video không hỗ trợ! Vui lòng chọn MP4, WebM, OGG hoặc MOV.', 'error');
        event.target.value = '';
        return;
    }

    // Hiển thị tên file
    const fileName = document.getElementById('videoFileName');
    if (fileName) {
        fileName.textContent = '📹 ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        fileName.style.display = 'block';
    }

    // Chèn video vào editor bằng jQuery
    insertVideoFileWithJQuery(file);
};

// ==================== CHÈN VIDEO BẰNG JQUERY ====================

/**
 * Chèn video từ file vào Quill Editor bằng jQuery
 * Sử dụng FileReader để đọc file và chèn dưới dạng thẻ video HTML
 */
function insertVideoFileWithJQuery(file) {
    if (!quillEditor) {
        showToast('Vui lòng đợi editor tải xong!', 'error');
        return;
    }

    const reader = new FileReader();
    
    reader.onload = function(e) {
        const videoDataUrl = e.target.result;
        
        // Tạo thẻ video HTML với controls và style
        const videoHtml = `<video controls style="max-width: 100%; height: auto; border-radius: 8px; margin: 10px 0; display: block; background: #000;" src="${videoDataUrl}"></video>`;
        
        // Sử dụng jQuery để lấy vị trí con trỏ
        const $editor = $('#quill-editor-container .ql-editor');
        
        // Lấy vị trí con trỏ hiện tại trong Quill
        const range = quillEditor.getSelection(true);
        const index = range ? range.index : quillEditor.getLength();
        
        // Chèn video vào Quill bằng dangerouslyPasteHTML
        quillEditor.clipboard.dangerouslyPasteHTML(index, videoHtml);
        
        // Cập nhật textarea
        updateTextareaContent();
        
        // Đặt con trỏ sau video
        setTimeout(function() {
            quillEditor.setSelection(index + 1, 0);
        }, 10);
        
        // Đóng modal và thông báo
        closeVideoModal();
        showToast('Đã chèn video từ File thành công!', 'success');
        setTimeout(updateToolbarStateQuill, 10);
    };

    reader.onerror = function() {
        showToast('Lỗi đọc file video!', 'error');
    };

    reader.readAsDataURL(file);
}

/**
 * Cập nhật nội dung textarea từ Quill Editor
 */
function updateTextareaContent() {
    if (quillEditor) {
        const textarea = document.getElementById('news_content');
        if (textarea) {
            textarea.value = quillEditor.root.innerHTML;
        }
    }
}

/**
 * Chèn video từ YouTube vào Quill Editor
 */
window.insertYoutubeVideo = function() {
    if (!quillEditor) {
        showToast('Vui lòng đợi editor tải xong!', 'error');
        return;
    }

    const urlInput = document.getElementById('youtubeUrl');
    if (!urlInput) return;

    const url = urlInput.value.trim();

    if (!url) {
        showToast('Vui lòng nhập URL YouTube!', 'error');
        urlInput.focus();
        urlInput.style.borderColor = '#ef4444';
        setTimeout(function() {
            urlInput.style.borderColor = '#e2e8f0';
        }, 2000);
        return;
    }

    const videoId = getYoutubeId(url);
    if (!videoId) {
        showToast('Link YouTube không hợp lệ!', 'error');
        urlInput.focus();
        urlInput.style.borderColor = '#ef4444';
        setTimeout(function() {
            urlInput.style.borderColor = '#e2e8f0';
        }, 2000);
        return;
    }

    // Tạo embed URL cho YouTube
    const embedUrl = 'https://www.youtube.com/embed/' + videoId;
    
    // Tạo HTML iframe với responsive container
    const videoHtml = `
        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin: 10px 0;">
            <iframe 
                src="${embedUrl}" 
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 8px;"
                allowfullscreen
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            ></iframe>
        </div>
    `;

    // Lấy vị trí con trỏ hiện tại
    const range = quillEditor.getSelection(true);
    const index = range ? range.index : quillEditor.getLength();
    
    // Chèn video vào Quill
    quillEditor.clipboard.dangerouslyPasteHTML(index, videoHtml);
    
    // Cập nhật textarea
    updateTextareaContent();
    
    // Đặt con trỏ sau video
    setTimeout(function() {
        quillEditor.setSelection(index + 1, 0);
    }, 10);

    closeVideoModal();
    showToast('Đã chèn video YouTube thành công!', 'success');
    setTimeout(updateToolbarStateQuill, 10);
};

// Hàm lấy YouTube ID từ URL
window.getYoutubeId = function(url) {
    if (!url) return null;

    const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s?#]+)/,
        /youtube\.com\/embed\/([^&\s?#]+)/,
        /youtube\.com\/v\/([^&\s?#]+)/,
        /youtube\.com\/shorts\/([^&\s?#]+)/
    ];

    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) return match[1];
    }

    return null;
};

// ==================== VIDEO MODAL EVENTS (jQuery) ====================

// Đóng modal khi click bên ngoài
$(document).on('click', '#videoModal', function(e) {
    if (e.target === this) {
        closeVideoModal();
    }
});

// Cho phép Enter để chèn YouTube
$(document).on('keydown', '#youtubeUrl', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        insertYoutubeVideo();
    }
});

// Xóa lỗi border khi người dùng nhập vào
$(document).on('input', '#youtubeUrl', function() {
    this.style.borderColor = '#e2e8f0';
});

// Đóng modal bằng phím ESC
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('videoModal');
        if (modal && modal.classList.contains('show')) {
            closeVideoModal();
        }
    }
});

// ==================== CHÈN VIDEO BẰNG JQUERY THUẦN (ALTERNATIVE) ====================

/**
 * Cách 2: Chèn video hoàn toàn bằng jQuery (không qua Quill API)
 * Phương pháp này trực tiếp thao tác DOM của Quill editor
 */
function insertVideoWithJQueryDirect(file) {
    if (!quillEditor) {
        showToast('Vui lòng đợi editor tải xong!', 'error');
        return;
    }

    const reader = new FileReader();
    
    reader.onload = function(e) {
        const videoDataUrl = e.target.result;
        
        // Tạo thẻ video HTML
        const videoHtml = `<video controls style="max-width: 100%; height: auto; border-radius: 8px; margin: 10px 0; display: block; background: #000;" src="${videoDataUrl}"></video>`;
        
        // Sử dụng jQuery để chèn vào cuối editor
        const $editor = $('#quill-editor-container .ql-editor');
        
        // Kiểm tra nếu có selection trong Quill
        const selection = quillEditor.getSelection();
        if (selection) {
            // Chèn tại vị trí con trỏ
            quillEditor.clipboard.dangerouslyPasteHTML(selection.index, videoHtml);
        } else {
            // Chèn vào cuối nội dung
            $editor.append(videoHtml);
        }
        
        // Cập nhật textarea
        const textarea = document.getElementById('news_content');
        if (textarea) {
            textarea.value = quillEditor.root.innerHTML;
        }
        
        // Đóng modal và thông báo
        closeVideoModal();
        showToast('Đã chèn video từ File thành công!', 'success');
        
        // Đặt con trỏ xuống dòng mới sau video
        setTimeout(function() {
            const length = quillEditor.getLength();
            quillEditor.setSelection(length, 0);
        }, 100);
    };

    reader.onerror = function() {
        showToast('Lỗi đọc file video!', 'error');
    };

    reader.readAsDataURL(file);
}

// Hàm chèn video từ file (có thể dùng 1 trong 2 cách)
// Cách 1: Sử dụng Quill API
// window.insertVideoFile = insertVideoFileWithJQuery;

// Cách 2: Sử dụng jQuery thuần
window.insertVideoFileDirect = insertVideoWithJQueryDirect;


// ==================== NHẬP LINK VIDEO ====================

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

// Hàm áp dụng màu cho Quill
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

// Khởi tạo bảng màu cho Quill
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
}

// Modal chọn màu nâng cao
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

// Đóng modal chọn màu
window.closeMoreColorsModal = function() {
    $('#moreColorsModal').remove();
};

// Áp dụng màu từ modal
window.applyMoreColorsColorForQuill = function(isHighlight) {
    const color = $('#colorPreviewBox').css('background-color');
    applyColorToQuill(color, isHighlight);
    closeMoreColorsModal();
};

// ==================== HÀM CHUYỂN ĐỔI MÀU SẮC ====================

function hslToRgb(h, s, l) {
    h = parseInt(h) / 360;
    s = parseInt(s) / 100;
    l = parseInt(l) / 100;

    let r, g, b;
    if (s === 0) {
        r = g = b = l;
    } else {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }
    return {
        r: Math.round(r * 255),
        g: Math.round(g * 255),
        b: Math.round(b * 255)
    };
}

function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    }).join('');
}

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function rgbToHsl(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
        h = s = 0;
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r:
                h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
                break;
            case g:
                h = ((b - r) / d + 2) / 6;
                break;
            case b:
                h = ((r - g) / d + 4) / 6;
                break;
        }
    }
    return {
        h: Math.round(h * 360),
        s: Math.round(s * 100),
        l: Math.round(l * 100)
    };
}

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