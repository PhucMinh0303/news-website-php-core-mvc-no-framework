// ==================== toast.js ====================
// Toast Notification

/**
 * Hiển thị toast message với kiểu (success, error, info, warning)
 * @param {string} message - Nội dung thông báo
 * @param {string} type - Loại thông báo: 'success', 'error', 'info', 'warning'
 */
function showToast(message, type) {
    // Xóa toast cũ nếu có
    $('.toast').remove();

    // Xác định icon và màu sắc cho từng loại
    const configs = {
        success: {
            icon: '✅',
            bgColor: '#10b981',
            textColor: '#ffffff',
            borderColor: '#059669'
        },
        error: {
            icon: '❌',
            bgColor: '#ef4444',
            textColor: '#ffffff',
            borderColor: '#dc2626'
        },
        warning: {
            icon: '⚠️',
            bgColor: '#f59e0b',
            textColor: '#ffffff',
            borderColor: '#d97706'
        },
        info: {
            icon: 'ℹ️',
            bgColor: '#3b82f6',
            textColor: '#ffffff',
            borderColor: '#2563eb'
        }
    };

    const config = configs[type] || configs.info;

    // Tạo toast với style đẹp hơn
    const toast = $('<div>')
        .addClass('toast')
        .css({
            'position': 'fixed',
            'top': '80px',
            'right': '20px',
            'z-index': '99999',
            'background': config.bgColor,
            'color': config.textColor,
            'padding': '16px 24px',
            'border-radius': '12px',
            'box-shadow': '0 10px 40px rgba(0,0,0,0.2)',
            'font-size': '15px',
            'font-weight': '500',
            'max-width': '450px',
            'min-width': '280px',
            'border-left': '5px solid ' + config.borderColor,
            'display': 'flex',
            'align-items': 'flex-start',
            'gap': '12px',
            'opacity': '0',
            'transform': 'translateX(50px)',
            'transition': 'all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)',
            'font-family': "'Segoe UI', system-ui, -apple-system, sans-serif",
            'line-height': '1.5'
        })
        .html(`
            <div style="font-size: 22px; flex-shrink: 0; margin-top: 2px;">${config.icon}</div>
            <div style="flex: 1; word-break: break-word;">${message.replace(/\n/g, '<br>')}</div>
            <button onclick="$(this).closest('.toast').fadeOut(300, function(){ $(this).remove(); })" 
                    style="background: transparent; border: none; color: ${config.textColor}; font-size: 18px; cursor: pointer; padding: 0 4px; opacity: 0.7; flex-shrink: 0; line-height: 1;">
                ✕
            </button>
        `)
        .hide();

    $('body').append(toast);

    // Hiệu ứng xuất hiện
    setTimeout(() => {
        toast.css({
            'opacity': '1',
            'transform': 'translateX(0)'
        });
    }, 50);

    // Tự động ẩn sau 4 giây
    setTimeout(() => {
        toast.css({
            'opacity': '0',
            'transform': 'translateX(50px)'
        });
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 4000);

    // Click vào toast để đóng nhanh
    toast.on('click', function(e) {
        if (!$(e.target).closest('button').length) {
            toast.css({
                'opacity': '0',
                'transform': 'translateX(50px)'
            });
            setTimeout(() => {
                toast.remove();
            }, 400);
        }
    });
}

// ===== CÁC HÀM TOAST TIỆN ÍCH =====
function showSuccessToast(message) {
    showToast(message, 'success');
}

function showErrorToast(message) {
    showToast(message, 'error');
}

function showWarningToast(message) {
    showToast(message, 'warning');
}

function showInfoToast(message) {
    showToast(message, 'info');
}

// ==================== EXPOSE ====================
window.toast = {
    show: showToast,
    success: showSuccessToast,
    error: showErrorToast,
    warning: showWarningToast,
    info: showInfoToast
};
// ==================== validation.js ====================
// Form Validation

const requiredFieldsMap = [
    {
        selector: '#news_title',
        name: 'title',
        label: 'Tiêu đề tin tức',
        getMessage: function() { return 'Vui lòng nhập tiêu đề tin tức'; }
    },
    {
        selector: 'input[name="author"]',
        name: 'author',
        label: 'Tác giả',
        getMessage: function() { return 'Vui lòng nhập tên tác giả'; }
    },
    {
        selector: 'input[name="publish_date"]',
        name: 'publish_date',
        label: 'Ngày đăng',
        getMessage: function() { return 'Vui lòng nhập thêm ngày đăng'; }
    },
    {
        selector: '#news_content',
        name: 'content',
        label: 'Bài viết',
        getMessage: function() { return 'Vui lòng nhập nội dung bài viết'; }
    }
];

function getRequiredFields($form) {
    return requiredFieldsMap.map(field => {
        const $element = $form.find(field.selector).first();
        return $element.length ? {
            $element: $element,
            label: field.label,
            name: field.name,
            getMessage: field.getMessage
        } : null;
    }).filter(Boolean);
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
        } else if ($field.is('[contenteditable="true"]')) {
            value = $field.html() || '';
        } else {
            value = $field.val() || '';
        }

        let isValid = true;
        let errorMessage = '';

        if ($field.attr('type') === 'number') {
            if (!value || parseInt(value, 10) <= 0) {
                isValid = false;
                errorMessage = field.getMessage();
            }
        } else {
            if ($field.is('[contenteditable="true"]')) {
                const content = $field.html() || '';
                const plainText = $('<div>').html(content).text().replace(/\u00a0/g, ' ').trim();
                const containsMedia = /<(img|video|iframe)\b/i.test(content);
                if (!plainText && !containsMedia) {
                    isValid = false;
                    errorMessage = field.getMessage();
                }
            } else if ($field.is('textarea')) {
                const content = $field.val();
                const strippedContent = content ? content.replace(/<[^>]*>/g, '').trim() : '';
                if (!strippedContent) {
                    isValid = false;
                    errorMessage = field.getMessage();
                }
            } else if (!value || value.trim() === '') {
                isValid = false;
                errorMessage = field.getMessage();
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
                    $field.after($errorMsg);
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

    // Kiểm tra slug - sử dụng module SlugGenerator dùng chung
    const $slug = $form.find('#slug');
    const slug = $slug.val();
    if (slug && typeof window.SlugGenerator !== 'undefined' && !window.SlugGenerator.isValid(slug)) {
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

// ==================== EXPOSE ====================
window.validation = {
    getRequiredFields: getRequiredFields,
    scrollToError: scrollToErrorElement,
    validateClient: validateClientForm,
    displayServerErrors: displayServerErrors
};
// ==================== news-form.js ====================
// News Form - Handlers và initialization

// ==================== BIND FORM HANDLERS ====================

function bindNewsFormHandlers($form) {
    if (!$form.length || $form.data('news-init')) {
        return;
    }

    $form.data('news-init', true);

    // ===== GẮN CÁC HANDLER CHO SLUG =====
    if (typeof window.SlugGenerator !== 'undefined' && !$form.data('slug-init')) {
        window.SlugGenerator.bindToForm($form);
        $form.data('slug-init', true);
    }

    // ===== KHỞI TẠO IMAGE PREVIEW DÙNG CHUNG =====
    if (typeof window.ImagePreview !== 'undefined' && !$form.data('image-preview-init')) {
        const imagePreview = window.ImagePreview.init($form);
        if (imagePreview) {
            $form.data('image-preview-api', imagePreview);
            $form.data('image-preview-init', true);
        }
    }

    initTextareaToolbar($form);

    // Xóa lỗi khi người dùng nhập vào các field
    $form.find('input, textarea, select, [contenteditable="true"]').on('input change', function() {
        $(this).removeClass('error-field');
        $(this).closest('.form-group').find('.field-error-msg').remove();

        if ($(this).is('[contenteditable="true"]') && $(this).attr('id') === 'news_content') {
            const content = $(this).html() || '';
            const strippedContent = $('<div>').html(content).text().replace(/\u00a0/g, ' ').trim();
            if (strippedContent || /<(img|video|iframe)\b/i.test(content)) {
                $(this).removeClass('error-field');
                $(this).closest('.form-group').find('.field-error-msg').remove();
            }
        }
    });

    // Xử lý submit form
    $form.on('submit', function(e) {
        window.syncEditorContent?.();
        // Cập nhật slug lần cuối trước khi submit
        if (typeof window.SlugGenerator !== 'undefined') {
            window.SlugGenerator.autoUpdate($form.find('#news_title'), $form.find('#slug'));
        }

        const storeUrl = $form.data('store-url');
        const slug = $form.find('#slug').val()?.trim();
        if (storeUrl && slug && !$form.data('edit-mode')) {
            $form.attr('action', `${storeUrl}/${encodeURIComponent(slug)}`);
        }

        if (!validateClientForm($form)) {
            e.preventDefault();
        }
    });

    // Tạo slug ban đầu nếu có title
    const $title = $form.find('#news_title');
    const $slug = $form.find('#slug');
    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    if (initialTitle && initialTitle.trim() !== '') {
        if (typeof window.SlugGenerator !== 'undefined') {
            if (!initialSlug || initialSlug.trim() === '' || initialSlug !== window.SlugGenerator.generateFromTitle(initialTitle)) {
                if (typeof window.SlugGenerator.autoUpdate === 'function') {
                    window.SlugGenerator.autoUpdate($title, $slug);
                }
            }
        }
    }
}

// ==================== INITIALIZATION ====================

function initNewsForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.filter('#newsForm').add($scope.find('#newsForm')).first();

    if (!$form.length) return;

    bindNewsFormHandlers($form);

    if (serverErrors && serverErrors.length > 0) {
        displayServerErrors(serverErrors, $form);
    }
}

// ==================== WYSIWYG HTML EDITOR ====================

let savedNewsRange = null;

function getNewsEditor() {
    return document.getElementById('news_content');
}

function newsEditorContainsNode(editor, node) {
    return editor === node || editor.contains(node);
}

function syncNewsEditorContent() {
    const editor = getNewsEditor();
    const source = document.getElementById('news_content_source');
    if (editor && source) source.value = editor.innerHTML;
}

function rememberNewsSelection() {
    const editor = getNewsEditor();
    const selection = window.getSelection();
    if (!editor || !selection || selection.rangeCount === 0) return;

    const range = selection.getRangeAt(0);
    if (newsEditorContainsNode(editor, range.commonAncestorContainer)) {
        savedNewsRange = range.cloneRange();
    }
}

function restoreNewsSelection() {
    const editor = getNewsEditor();
    if (!editor) return false;
    editor.focus();

    const selection = window.getSelection();
    if (savedNewsRange && newsEditorContainsNode(editor, savedNewsRange.commonAncestorContainer)) {
        selection.removeAllRanges();
        selection.addRange(savedNewsRange);
    }
    return true;
}

function runNewsEditorCommand(command, value = null) {
    if (!restoreNewsSelection()) return;
    document.execCommand(command, false, value);
    syncNewsEditorContent();
    rememberNewsSelection();
}

function wrapText(command) {
    const commands = {
        bold: 'bold',
        italic: 'italic',
        underline: 'underline',
        insertUnorderedList: 'insertUnorderedList',
        insertOrderedList: 'insertOrderedList',
        justifyLeft: 'justifyLeft',
        justifyCenter: 'justifyCenter',
        justifyRight: 'justifyRight'
    };
    if (commands[command]) runNewsEditorCommand(commands[command]);
}

function applyHeadingToTextarea(tagName) {
    const tags = { h1: 'h1', h2: 'h2', h3: 'h3', h4: 'h4', p: 'p' };
    if (tags[tagName]) runNewsEditorCommand('formatBlock', `<${tags[tagName]}>`);
    $('#headingSelect').val('');
}

function applyNewsInlineStyle(styleName, value) {
    if (!restoreNewsSelection()) return;
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
        showToast('Hãy chọn phần chữ cần định dạng trước.', 'info');
        return;
    }

    const range = selection.getRangeAt(0);
    const span = document.createElement('span');
    span.style[styleName] = value;
    try {
        range.surroundContents(span);
    } catch (error) {
        const contents = range.extractContents();
        span.appendChild(contents);
        range.insertNode(span);
    }

    selection.removeAllRanges();
    const nextRange = document.createRange();
    nextRange.selectNodeContents(span);
    selection.addRange(nextRange);
    savedNewsRange = nextRange.cloneRange();
    syncNewsEditorContent();
}

function buildNewsColorPalette($container, colors, styleName) {
    $container.empty();
    colors.forEach(color => {
        $('<button>', {
            type: 'button',
            title: color,
            'aria-label': color,
            class: 'news-color-swatch'
        }).css({
            width: '22px', height: '22px', padding: 0, margin: '3px',
            border: '1px solid #999', backgroundColor: color, cursor: 'pointer'
        }).appendTo($container).on('mousedown', function(event) {
            event.preventDefault();
            rememberNewsSelection();
        }).on('click', function() {
            applyNewsInlineStyle(styleName, color);
            $(this).closest('.color-dropdown').hide();
        });
    });
}

function insertNewsHtml(html) {
    if (!restoreNewsSelection()) return;
    document.execCommand('insertHTML', false, html);
    syncNewsEditorContent();
    rememberNewsSelection();
}

function initTextareaToolbar($form) {
    const $editor = $form.find('#news_content');
    if (!$editor.length || $form.data('textarea-toolbar-init')) return;
    $form.data('textarea-toolbar-init', true);

    const editor = $editor[0];
    const source = $form.find('#news_content_source')[0];
    editor.innerHTML = source.value;
    editor.addEventListener('input', syncNewsEditorContent);
    editor.addEventListener('keyup', rememberNewsSelection);
    editor.addEventListener('mouseup', rememberNewsSelection);
    editor.addEventListener('focus', rememberNewsSelection);
    if (!window.newsEditorSelectionListenerInitialized) {
        document.addEventListener('selectionchange', rememberNewsSelection);
        window.newsEditorSelectionListenerInitialized = true;
    }
    $form.on('submit', syncNewsEditorContent);

    $form.on('mousedown', '.rich-editor-toolbar button', function(event) {
        event.preventDefault();
        rememberNewsSelection();
    });

    const themeColors = ['#ffffff', '#000000', '#e7e6e6', '#44546a', '#4472c4', '#ed7d31', '#a5a5a5', '#ffc000', '#5b9bd5', '#70ad47'];
    const standardColors = ['#c00000', '#ff0000', '#ffc000', '#ffff00', '#92d050', '#00b050', '#00b0f0', '#0070c0', '#002060', '#7030a0'];
    buildNewsColorPalette($form.find('.theme-colors'), themeColors, 'color');
    buildNewsColorPalette($form.find('.standard-colors'), standardColors, 'color');
    buildNewsColorPalette($form.find('.highlight-theme-colors'), themeColors, 'backgroundColor');
    buildNewsColorPalette($form.find('.highlight-standard-colors'), standardColors, 'backgroundColor');

    $form.find('#fontFamily').on('change', function() {
        const fontFamily = $(this).val();
        const safeFonts = ['Arial', 'Times New Roman', 'Verdana', 'Georgia', 'Courier New', 'Roboto'];
        if (safeFonts.includes(fontFamily)) runNewsEditorCommand('fontName', fontFamily);
    });

    $form.find('#textColorBtn').on('click', function() {
        $form.find('#colorDropdown').toggle();
        $form.find('#highlightDropdown').hide();
    });
    $form.find('#highlightColorBtn').on('click', function() {
        $form.find('#highlightDropdown').toggle();
        $form.find('#colorDropdown').hide();
    });
    $form.find('#noColorOption').on('mousedown', event => event.preventDefault()).on('click', function() {
        applyNewsInlineStyle('color', 'inherit');
        $form.find('#colorDropdown').hide();
    });
    $form.find('#noHighlightOption').on('mousedown', event => event.preventDefault()).on('click', function() {
        applyNewsInlineStyle('backgroundColor', 'transparent');
        $form.find('#highlightDropdown').hide();
    });

    function chooseCustomColor(styleName, dropdownSelector) {
        const $picker = $('<input>', { type: 'color', value: '#000000' }).css({
            position: 'fixed', left: '-10000px', top: '-10000px'
        }).appendTo(document.body);
        $picker.one('change', function() {
            applyNewsInlineStyle(styleName, this.value);
            $(dropdownSelector).hide();
            $picker.remove();
        }).trigger('click');
        $picker.on('blur', function() { setTimeout(() => $picker.remove(), 300); });
    }
    $form.find('#moreColorsOption').on('mousedown', event => event.preventDefault()).on('click', function() {
        chooseCustomColor('color', '#colorDropdown');
    });
    $form.find('#moreHighlightColorsOption').on('mousedown', event => event.preventDefault()).on('click', function() {
        chooseCustomColor('backgroundColor', '#highlightDropdown');
    });

    $('#newsContentImagePicker').remove();
    const imagePicker = $('<input>', {
        type: 'file', accept: 'image/jpeg,image/png,image/webp',
        id: 'newsContentImagePicker',
        'aria-label': 'Chọn ảnh chèn vào nội dung'
    }).css({ position: 'fixed', left: '-10000px', top: '-10000px' }).appendTo(document.body);
    imagePicker.on('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;
        if (!/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 5 * 1024 * 1024) {
            showToast('Chỉ hỗ trợ ảnh JPG, PNG, WEBP tối đa 5MB.', 'warning');
            this.value = '';
            return;
        }
        const uploadUrl = $form.data('upload-image-url');
        const formData = new FormData();
        formData.append('image', file);
        const altText = file.name.replace(/\.[^.]+$/, '').replace(/[&<>"']/g, '');
        fetch(uploadUrl, { method: 'POST', body: formData, credentials: 'same-origin' })
            .then(response => response.json())
            .then(result => {
                if (!result.success) throw new Error(result.message || 'Upload thất bại');
                const img = document.createElement('img');
                img.src = result.url;
                img.alt = altText;
                img.style.cssText = 'max-width: 100%; height: auto;';
                insertNewsHtml(img.outerHTML);
            })
            .catch(error => showToast(error.message || 'Không thể tải ảnh lên.', 'error'));
        this.value = '';
    });
    window.insertImageToTextarea = function() { rememberNewsSelection(); imagePicker.trigger('click'); };

    window.handleVideoFileUpload = function(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('video/') || file.size > 15 * 1280 * 720) {
            showToast('Chọn video hợp lệ có dung lượng tối đa 15MB.', 'warning');
            event.target.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function() {
            insertNewsHtml(`<video width="100%" controls><source src="${reader.result}" type="${file.type}"></video>`);
            if (typeof window.closeVideoModal === 'function') window.closeVideoModal();
        };
        reader.onerror = function() { showToast('Không thể đọc tệp video đã chọn.', 'error'); };
        reader.readAsDataURL(file);
    };

    window.insertYoutubeVideo = function() {
        const url = ($('#youtubeUrl').val() || '').trim();
        const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/);
        if (!match) {
            showToast('URL YouTube không hợp lệ.', 'warning');
            return;
        }
        insertNewsHtml(`<div class="video-wrapper"><iframe width="100%" height="315" src="https://www.youtube.com/embed/${match[1]}" frameborder="0" allowfullscreen></iframe></div>`);
        if (typeof window.closeVideoModal === 'function') window.closeVideoModal();
    };

    window.createLinkForTextarea = function() {
        rememberNewsSelection();
        if (!window.getSelection()?.toString()) {
            showToast('Hãy chọn phần chữ cần chèn liên kết trước.', 'info');
            return;
        }
        const url = window.prompt('Nhập địa chỉ liên kết:');
        if (url) runNewsEditorCommand('createLink', url);
    };

    window.removeTextareaFormat = function() {
        if (!restoreNewsSelection()) return;
        document.execCommand('removeFormat', false, null);
        document.execCommand('unlink', false, null);
        syncNewsEditorContent();
    };

    window.syncEditorContent = syncNewsEditorContent;
}

// ==================== AI FUNCTIONS ====================

function generateAITitle() {
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
    if (typeof window.SlugGenerator !== 'undefined') {
        window.SlugGenerator.autoUpdate($form.find('#news_title'), $form.find('#slug'));
    }
    $title.removeClass('error-field');
    $title.closest('.form-group').find('.field-error-msg').remove();
    showToast('Đã tạo gợi ý tiêu đề!', 'success');
}

function generateAIContent() {
    showToast('Tính năng tạo nội dung bằng AI đang phát triển!', 'info');
}

function generateAIImage() {
    showToast('Tính năng tạo ảnh bằng AI đang phát triển!', 'info');
}

function removeImage() {
    const imagePreview = $('#newsForm').data('image-preview-api');
    if (imagePreview) {
        imagePreview.clear();
    }
}

// ==================== EXPOSE ====================
window.newsForm = {
    init: initNewsForm,
    generateAITitle: generateAITitle,
    generateAIContent: generateAIContent,
    generateAIImage: generateAIImage,
    removeImage: removeImage
};
// ==================== news-create.js ====================
// Main Entry Point - Tổng hợp tất cả các module

// ==================== AUTO INIT ON DOM READY ====================

$(document).ready(function() {
    // Khởi tạo form với server errors
    const serverErrors = window.serverErrors || null;
    if (typeof window.newsForm !== 'undefined' && window.newsForm.init) {
        window.newsForm.init(document, serverErrors);

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType !== Node.ELEMENT_NODE) return;
                    const $node = $(node);
                    if ($node.is('#newsForm') || $node.find('#newsForm').length) {
                        window.newsForm.init(node);
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }
});
