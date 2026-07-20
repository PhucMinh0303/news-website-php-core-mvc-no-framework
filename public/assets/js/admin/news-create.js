
    // ==================== SLUG FUNCTIONS ====================

    /**
     * Hàm loại bỏ dấu tiếng Việt
     * @param {string} str - Chuỗi cần loại bỏ dấu
     * @returns {string} Chuỗi đã loại bỏ dấu
     */
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

    /**
     * Tạo slug từ tiêu đề
     * @param {string} title - Tiêu đề cần tạo slug
     * @returns {string} Slug đã tạo
     */
    function generateSlugFromTitle(title) {
        if (!title || title.trim() === '') {
            return '';
        }
        
        // Tạo slug từ tiêu đề, loại bỏ dấu tiếng Việt, 
        // chuyển thành chữ thường, thay khoảng trắng bằng dấu gạch ngang, 
        // và loại bỏ ký tự đặc biệt
        let slug = removeVietnameseTones(title)
            .toLowerCase()
            .replace(/[^\w\s]/g, '')      // Xóa ký tự đặc biệt
            .replace(/\s+/g, '-')         // Thay khoảng trắng bằng -
            .replace(/-+/g, '-')          // Xóa - thừa
            .replace(/^-+|-+$/g, '')      // Xóa - ở đầu và cuối
            .substring(0, 100);           // Giới hạn độ dài

        return slug;
    }

    /**
     * Kiểm tra slug hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)
     * @param {string} slug - Slug cần kiểm tra
     * @returns {boolean} True nếu slug hợp lệ
     */
    function isValidSlug(slug) {
        if (!slug || slug.trim() === '') {
            return false;
        }
        return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug);
    }

    // Cập nhật slug tự động khi người dùng nhập tiêu đề  
    function updateSlug($form) {
        const $title = $form.find('#news_title');
        const $slug = $form.find('#slug');

        if (!$title.length || !$slug.length) {
            return;
        }

        const title = $title.val();
        const newSlug = generateSlugFromTitle(title);
        const oldSlug = $slug.val();

        // Không cập nhật nếu slug không thay đổi
        if (newSlug === oldSlug) {
            return;
        }

        // Cập nhật slug
        $slug.val(newSlug);

        // Hiệu ứng highlight khi cập nhật (THÊM VÀO)
        $slug.css({
            backgroundColor: '#fef3c7',
            transition: 'all 0.3s ease'
        });

        setTimeout(() => {
            $slug.css('backgroundColor', '#f3f4f6');
        }, 500);
    }

    // ===== THÊM HÀM NÀY: Kiểm tra và cập nhật slug khi cần =====
    function ensureSlugSync($form) {
        const $title = $form.find('#news_title');
        const $slug = $form.find('#slug');
        
        if (!$title.length || !$slug.length) return;
        
        const title = $title.val();
        const currentSlug = $slug.val();
        const autoSlug = generateSlugFromTitle(title);
        
        // Nếu slug trống hoặc khác với auto slug, cập nhật lại
        if (!currentSlug || currentSlug.trim() === '' || currentSlug !== autoSlug) {
            updateSlug($form);
            return true;
        }
        return false;
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

        $element.removeClass('error-highlight');
        void $element[0].offsetWidth;
        $element.addClass('error-highlight');

        const elementPosition = $element.offset().top;
        const offsetPosition = elementPosition - offset;

        $('html, body').animate({
            scrollTop: offsetPosition
        }, 500, function() {
            if ($element.is(':visible') && !$element.is('input[readonly]')) {
                $element.trigger('focus');
            }
        });

        setTimeout(() => {
            $element.removeClass('error-highlight');
        }, 2000);
    }

    // ==================== VALIDATION & REQUIRED FIELDS ====================

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

    function getRequiredFields($form) {
        const $requiredFields = [];

        $form.find('label').each(function() {
            const $label = $(this);
            if ($label.find('.required').length) {
                const forAttr = $label.attr('for');
                let $field = null;

                if (forAttr) {
                    $field = $form.find('#' + forAttr);
                } else {
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

    function validateClientForm($form) {
        const requiredFields = getRequiredFields($form);
        const errors = [];
        let firstErrorElement = null;

        $('.field-error-msg').remove();
        $('.error-field').removeClass('error-field');

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

            if ($field.attr('type') === 'number') {
                if (!value || parseInt(value, 10) <= 0) {
                    isValid = false;
                    errorMessage = `Vui lòng nhập ${field.label}`;
                }
            } else {
                if ($field.is('textarea')) {
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

        // Kiểm tra slug
        const $slug = $form.find('#slug');
        const slug = $slug.val();
        if (slug && !isValidSlug(slug)) {
            errors.push({ 
                msg: 'Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)', 
                field: $slug 
            });
            $slug.addClass('error-field');
            if (!firstErrorElement) firstErrorElement = $slug;
        }

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

    function displayServerErrors(errors, $form) {
        if (!errors || errors.length === 0) return false;

        $('.field-error-msg').remove();
        $('.error-field').removeClass('error-field');

        const requiredFields = getRequiredFields($form);
        let firstErrorElement = null;
        let errorList = [];

        const fieldMap = {};
        requiredFields.forEach(field => {
            if (field.name) {
                fieldMap[field.name] = field;
            }
            if (field.$element.attr('id')) {
                fieldMap[field.$element.attr('id')] = field;
            }
        });

        const allFieldsMap = {
            'slug': { $element: $('#slug'), label: 'Slug' },
            'category_id': { $element: $('select[name="category_id"]'), label: 'Danh mục' },
            'author_id': { $element: $('select[name="author_id"]'), label: 'Tác giả' },
            'publish_date': { $element: $('input[name="publish_date"]'), label: 'Ngày đăng' },
            'status': { $element: $('select[name="status"]'), label: 'Trạng thái' },
            'featured_image': { $element: $('#imageInput'), label: 'Ảnh đại diện' }
        };

        Object.assign(fieldMap, allFieldsMap);

        errors.forEach(error => {
            errorList.push(error);

            let $element = null;
            let fieldLabel = '';

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

                const $errorMsg = $('<div>')
                    .addClass('field-error-msg')
                    .html('⚠️ ' + error);

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

                if (!firstErrorElement) {
                    firstErrorElement = $element;
                }
            }
        });

        if (errorList.length > 0) {
            showToast('⚠️ Có ' + errorList.length + ' lỗi cần sửa:\n• ' + errorList.join('\n• '), 'error');
        }

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

    // Tự động tạo slug khi nhập title (ĐÃ CÓ SẴN)
    $title.on('input', function() {
        updateSlug($form);
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();
    });

    // ===== THÊM: Theo dõi thay đổi của slug (tùy chọn) =====
    $slug.on('input', function() {
        // Nếu người dùng cố tình sửa slug, hiển thị thông báo
        const currentSlug = $(this).val();
        const title = $title.val();
        const autoSlug = generateSlugFromTitle(title);
        
        if (currentSlug !== autoSlug && currentSlug.trim() !== '') {
            showToast('⚠️ Slug được tạo tự động từ tiêu đề! Thay đổi này sẽ bị ghi đè.', 'info');
        }
    });

    // Khóa chỉnh sửa slug trực tiếp
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

    // Xóa lỗi khi người dùng nhập vào các field
    $form.find('input, textarea, select').on('input change', function() {
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();

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

        if (typeof syncEditorContent === 'function') {
            syncEditorContent();
        }

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
        $('#uploadBox').removeClass('error-field');
        $('#uploadBox').closest('.form-group').find('.field-error-msg').remove();
    });

    // Tạo slug ban đầu nếu có title
    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    // ===== SỬA: Cập nhật slug nếu có title nhưng chưa có slug =====
    if (initialTitle && initialTitle.trim() !== '') {
        // Nếu slug trống hoặc slug không khớp với title
        if (!initialSlug || initialSlug.trim() === '' || initialSlug !== generateSlugFromTitle(initialTitle)) {
            updateSlug($form);
        }
    }

    // Thêm title cho slug
    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
}

    // ==================== PREVIEW IMAGE ====================

    function previewImage(input, $form) {
        const $preview = $form.find('#imagePreview');
        const $previewImg = $form.find('#previewImg');
        const $uploadBox = $form.find('#uploadBox');
        const $uploadText = $form.find('#uploadText');
        const $uploadInfo = $form.find('#uploadInfo');

        if (!input.files || !input.files[0]) {
            return;
        }

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
            $uploadBox.removeClass('error-field');
            $uploadBox.closest('.form-group').find('.field-error-msg').remove();
        };

        reader.readAsDataURL(input.files[0]);
    }

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

    function initNewsForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#newsForm');

    if (!$form.length) return;

    bindNewsFormHandlers($form);

    // ===== THÊM: Đảm bảo slug sync sau khi init =====
    if (typeof ensureSlugSync === 'function') {
        ensureSlugSync($form);
    }

    if (serverErrors && serverErrors.length > 0) {
        displayServerErrors(serverErrors, $form);
    }
}

    // ==================== EXPOSE GLOBAL FUNCTIONS ====================

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
        const serverErrors = window.serverErrors || null;
        window.newsForm.init(document, serverErrors);
    });

    // ==================== QUILL.JS INTEGRATION (SỬ DỤNG API CHÍNH THỨC) ====================

    let quillEditor = null;
    let quillInitialized = false;

    // Màu sắc cho color picker
    const themeColors = [
        '#000000', '#434343', '#666666', '#999999', '#b7b7b7', '#cccccc', '#d9d9d9', '#efefef', '#f3f3f3', '#ffffff',
        '#980000', '#ff0000', '#ff9900', '#ffff00', '#00ff00', '#00ffff', '#4a86e8', '#0000ff', '#9900ff', '#ff00ff',
        '#e6b8af', '#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d0e0e3', '#c9daf8', '#cfe2f3', '#d9d2e9', '#ead1dc',
        '#dd7e6b', '#ea9999', '#f9cb9c', '#ffe599', '#b6d7a8', '#a2c4c9', '#8eaad8', '#9fc5e8', '#b4a7d6', '#d5a6bd',
        '#cc4125', '#e06666', '#f6b26b', '#ffd966', '#93c47d', '#76a5af', '#6d9eeb', '#6fa8dc', '#8e7cc3', '#c27ba0',
        '#a61c00', '#cc0000', '#e69138', '#f1c232', '#6aa84f', '#45818e', '#3c78d8', '#3d85c6', '#674ea7', '#a64d79',
        '#85200c', '#990000', '#b45f06', '#bf9000', '#38761d', '#134f5c', '#1155cc', '#0b5394', '#351c75', '#741b47'
    ];

    const standardColors = [
        '#c0c0c0', '#808080', '#800000', '#ff0000', '#ff8000', '#ffff00', '#00ff00', '#00ffff', '#0000ff', '#8000ff',
        '#808000', '#008080', '#800080', '#000000', '#666666', '#999999', '#b3b3b3', '#cccccc', '#e6e6e6', '#ffffff'
    ];

    function initQuillEditor() {
        if (quillInitialized) return;

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

        textarea.parentNode.insertBefore(quillContainer, textarea);
        textarea.style.display = 'none';

        // Khởi tạo Quill với toolbar đầy đủ
        quillEditor = new Quill('#quill-editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': [] }],
                    [{ 'header': [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ],
                clipboard: {
                    matchVisual: false
                }
            },
            placeholder: 'Đây là nội dung bài viết. Có thể gõ trực tiếp hoặc dán nội dung từ nguồn khác...'
        });

        // Đặt nội dung ban đầu
        if (textarea.value) {
            quillEditor.root.innerHTML = textarea.value;
        }

        // Cập nhật textarea khi Quill thay đổi
        quillEditor.on('text-change', function() {
            textarea.value = quillEditor.root.innerHTML;
        });

        quillInitialized = true;

        // Thiết lập custom handlers cho video và image
        setupQuillCustomHandlers();
    }

    function setupQuillCustomHandlers() {
        if (!quillEditor) return;

        // Lấy toolbar của Quill
        const toolbar = quillEditor.getModule('toolbar');
        if (!toolbar) return;

        // Custom handler cho video
        const videoHandler = function() {
            openVideoModalForTextarea();
        };

        // Custom handler cho image
        const imageHandler = function() {
            insertImageToTextarea();
        };

        // Ghi đè handlers
        const toolbarConfig = toolbar.options;
        if (toolbarConfig.handlers) {
            toolbarConfig.handlers.video = videoHandler;
            toolbarConfig.handlers.image = imageHandler;
        }
    }

    // ==================== QUILL TOOLBAR FUNCTIONS ====================

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

        // Alignment
        $('[onclick*="justify"]').removeClass('active');

        const alignValue = format.align;
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
            $('#fontFamily').val('sans-serif');
        }
    }

    // ==================== CÁC HÀM WRAP TEXT (SỬ DỤNG API QUILL) ====================

    window.wrapText = function(action) {
        if (!quillEditor) return;

        switch(action) {
            case 'bold':
                quillEditor.format('bold', !quillEditor.getFormat().bold);
                break;
            case 'italic':
                quillEditor.format('italic', !quillEditor.getFormat().italic);
                break;
            case 'underline':
                quillEditor.format('underline', !quillEditor.getFormat().underline);
                break;
            case 'insertUnorderedList':
                const format = quillEditor.getFormat();
                quillEditor.format('list', format.list === 'bullet' ? false : 'bullet');
                break;
            case 'insertOrderedList':
                const format2 = quillEditor.getFormat();
                quillEditor.format('list', format2.list === 'ordered' ? false : 'ordered');
                break;
            case 'justifyLeft':
                const currentAlign = quillEditor.getFormat().align;
                if (!currentAlign || currentAlign === 'left') {
                    quillEditor.format('align', false);
                } else {
                    quillEditor.format('align', 'left');
                }
                break;
            case 'justifyCenter':
                const currentAlign2 = quillEditor.getFormat().align;
                quillEditor.format('align', currentAlign2 === 'center' ? false : 'center');
                break;
            case 'justifyRight':
                const currentAlign3 = quillEditor.getFormat().align;
                quillEditor.format('align', currentAlign3 === 'right' ? false : 'right');
                break;
        }
        setTimeout(updateToolbarStateQuill, 10);
    };

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

    window.removeTextareaFormat = function() {
        if (!quillEditor) return;

        const range = quillEditor.getSelection();
        if (range && range.length > 0) {
            quillEditor.removeFormat(range.index, range.length);
        } else {
            quillEditor.root.innerHTML = quillEditor.root.innerHTML.replace(/<[^>]*>/g, '');
        }
        showToast('Đã xóa định dạng HTML!', 'success');
    };

    window.syncEditorContent = function() {
        if (quillEditor) {
            const textarea = document.getElementById('news_content');
            if (textarea) {
                textarea.value = quillEditor.root.innerHTML;
            }
        }
    };

    // ==================== IMAGE UPLOAD VIA QUILL API ====================

    window.insertImageToTextarea = function() {
        if (!quillEditor) return;

        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                // Kiểm tra kích thước ảnh
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Ảnh không được vượt quá 5MB!', 'error');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    const range = quillEditor.getSelection(true);
                    quillEditor.insertEmbed(range.index, 'image', ev.target.result);
                    // Đặt con trỏ sau ảnh
                    setTimeout(function() {
                        quillEditor.setSelection(range.index + 1, 0);
                    }, 10);
                    showToast('Đã chèn ảnh thành công!', 'success');
                };
                reader.readAsDataURL(file);
            }
        };
        input.click();
    };

    // ==================== VIDEO MODAL ====================

    function openVideoModalForTextarea() {
        if (!quillEditor) {
            showToast('Vui lòng đợi editor tải xong!', 'error');
            return;
        }
        const modal = document.getElementById('videoModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(function() {
                const youtubeInput = document.getElementById('youtubeUrl');
                if (youtubeInput) {
                    youtubeInput.focus();
                }
            }, 350);
        }
    }

    window.closeVideoModal = function() {
        const modal = document.getElementById('videoModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
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

    // ==================== VIDEO HANDLERS ====================

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

        const embedUrl = 'https://www.youtube.com/embed/' + videoId;
        
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

        const range = quillEditor.getSelection(true);
        const index = range ? range.index : quillEditor.getLength();
        
        quillEditor.clipboard.dangerouslyPasteHTML(index, videoHtml);
        
        setTimeout(function() {
            quillEditor.setSelection(index + 1, 0);
        }, 10);

        closeVideoModal();
        showToast('Đã chèn video YouTube thành công!', 'success');
        setTimeout(updateToolbarStateQuill, 10);
    };

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

    window.handleVideoFileUpload = function(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        if (file.size > 50 * 1024 * 1024) {
            showToast('Video không được vượt quá 50MB!', 'error');
            event.target.value = '';
            return;
        }

        const validTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
        if (!validTypes.includes(file.type)) {
            showToast('Định dạng video không hỗ trợ! Vui lòng chọn MP4, WebM, OGG hoặc MOV.', 'error');
            event.target.value = '';
            return;
        }

        const fileName = document.getElementById('videoFileName');
        if (fileName) {
            fileName.textContent = '📹 ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            fileName.style.display = 'block';
        }

        insertVideoFileWithQuill(file);
    };

    function insertVideoFileWithQuill(file) {
        if (!quillEditor) {
            showToast('Vui lòng đợi editor tải xong!', 'error');
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            const videoDataUrl = e.target.result;
            
            const videoHtml = `<video controls style="max-width: 100%; height: auto; border-radius: 8px; margin: 10px 0; display: block; background: #000;" src="${videoDataUrl}"></video>`;
            
            const range = quillEditor.getSelection(true);
            const index = range ? range.index : quillEditor.getLength();
            
            quillEditor.clipboard.dangerouslyPasteHTML(index, videoHtml);
            
            setTimeout(function() {
                quillEditor.setSelection(index + 1, 0);
            }, 10);
            
            closeVideoModal();
            showToast('Đã chèn video từ File thành công!', 'success');
            setTimeout(updateToolbarStateQuill, 10);
        };

        reader.onerror = function() {
            showToast('Lỗi đọc file video!', 'error');
        };

        reader.readAsDataURL(file);
    }

    // ==================== VIDEO MODAL EVENTS ====================

    $(document).on('click', '#videoModal', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });

    $(document).on('keydown', '#youtubeUrl', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            insertYoutubeVideo();
        }
    });

    $(document).on('input', '#youtubeUrl', function() {
        this.style.borderColor = '#e2e8f0';
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('videoModal');
            if (modal && modal.classList.contains('show')) {
                closeVideoModal();
            }
        }
    });

    // ==================== COLOR PICKER ====================

    // Khởi tạo color picker
    function initColorPicker() {
        // Theme colors
        const themeColorsHtml = themeColors.map(color => 
            `<div class="color-item" data-color="${color}" style="background-color: ${color};"></div>`
        ).join('');

        $('.theme-colors, .highlight-theme-colors').html(themeColorsHtml);

        // Standard colors
        const standardColorsHtml = standardColors.map(color => 
            `<div class="color-item" data-color="${color}" style="background-color: ${color};"></div>`
        ).join('');

        $('.standard-colors, .highlight-standard-colors').html(standardColorsHtml);

        // Color picker events
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

        // Toggle dropdowns
        $('#textColorBtn').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#colorDropdown').toggle();
            $('#highlightDropdown').hide();
        });

        $('#highlightColorBtn').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#highlightDropdown').toggle();
            $('#colorDropdown').hide();
        });

        // Close dropdowns when clicking outside
        $(document).off('click.quillDropdown').on('click.quillDropdown', function(e) {
            if (!$(e.target).closest('.color-picker-wrapper').length) {
                $('#colorDropdown').hide();
                $('#highlightDropdown').hide();
            }
        });
    }

    function applyColorToQuill(color, isHighlight) {
        if (!quillEditor) return;

        const format = isHighlight ? 'background' : 'color';

        if (color === null) {
            quillEditor.format(format, false);
        } else {
            quillEditor.format(format, color);
        }

        // Update indicator
        const indicatorId = isHighlight ? '#highlightIndicator' : '#colorIndicator';
        $(indicatorId).css('background-color', color || 'transparent');

        // Update button active state
        if (!isHighlight) {
            $('#textColorBtn').toggleClass('active', !!color);
        } else {
            $('#highlightColorBtn').toggleClass('active', !!color);
        }
    }

    // ==================== MORE COLORS MODAL ====================

    function showMoreColorsModalForQuill(isHighlight) {
        const modalHtml = `
            <div class="more-colors-modal" id="moreColorsModal">
                <div class="more-colors-content">
                    <h3>Chọn màu sắc</h3>
                    <div class="color-preview">
                        <div class="color-preview-box" id="colorPreviewBox"></div>
                        <div class="color-values">
                            <input type="text" id="colorHexInput" placeholder="#000000" maxlength="7">
                        </div>
                    </div>
                    <div class="color-slider">
                        <label>Màu sắc</label>
                        <input type="color" id="colorPickerInput" value="#000000">
                    </div>
                    <div class="modal-buttons">
                        <button class="btn-secondary" onclick="closeMoreColorsModal()">Hủy</button>
                        <button class="btn-primary" onclick="applyMoreColorQuill(${isHighlight})">Chọn</button>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        const colorPicker = document.getElementById('colorPickerInput');
        const hexInput = document.getElementById('colorHexInput');
        const previewBox = document.getElementById('colorPreviewBox');

        colorPicker.addEventListener('input', function() {
            const color = this.value;
            hexInput.value = color;
            previewBox.style.backgroundColor = color;
        });

        hexInput.addEventListener('input', function() {
            const color = this.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                colorPicker.value = color;
                previewBox.style.backgroundColor = color;
            }
        });
    }

    function closeMoreColorsModal() {
        $('#moreColorsModal').remove();
    }

    function applyMoreColorQuill(isHighlight) {
        const color = $('#colorHexInput').val();
        if (color && /^#[0-9A-F]{6}$/i.test(color)) {
            applyColorToQuill(color, isHighlight);
        }
        closeMoreColorsModal();
        setTimeout(updateToolbarStateQuill, 10);
    }

    // ==================== FONT FAMILY HANDLER ====================

    $('#fontFamily').off('change').on('change', function() {
        if (!quillEditor) return;
        const font = $(this).val();
        if (font) {
            quillEditor.format('font', font);
        }
        setTimeout(updateToolbarStateQuill, 10);
    });

    // ==================== HEADING SELECT HANDLER ====================

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

    // ==================== INITIALIZE ON DOM READY ====================

    $(document).ready(function() {
        // Khởi tạo Quill Editor
        initQuillEditor();

        // Khởi tạo Color Picker
        initColorPicker();

        // Cập nhật toolbar state sau khi khởi tạo
        setTimeout(updateToolbarStateQuill, 100);
    });
