// ==================== quill-editor.js ====================
// Quill Editor - Tất cả các hàm liên quan đến editor

// ==================== QUILL.JS INTEGRATION ====================

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

// ==================== INIT QUILL EDITOR ====================

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

    const toolbar = quillEditor.getModule('toolbar');
    if (!toolbar) return;

    const toolbarConfig = toolbar.options;
    if (toolbarConfig.handlers) {
        toolbarConfig.handlers.video = function() {
            openVideoModalForTextarea();
        };
        toolbarConfig.handlers.image = function() {
            insertImageToTextarea();
        };
    }
}

// ==================== QUILL TOOLBAR FUNCTIONS ====================

function updateToolbarStateQuill() {
    if (!quillEditor) return;

    const format = quillEditor.getFormat();

    $('#btnBold').toggleClass('active', !!format.bold);
    $('#btnItalic').toggleClass('active', !!format.italic);
    $('#btnUnderline').toggleClass('active', !!format.underline);
    $('[onclick*="insertUnorderedList"]').toggleClass('active', format.list === 'bullet');
    $('[onclick*="insertOrderedList"]').toggleClass('active', format.list === 'ordered');

    const headerVal = format.header;
    if (headerVal) {
        $('#headingSelect').val('h' + headerVal);
    } else {
        $('#headingSelect').val('p');
    }

    $('[onclick*="justify"]').removeClass('active');
    const alignValue = format.align;
    if (!alignValue || alignValue === 'left') {
        $('[onclick*="justifyLeft"]').addClass('active');
    } else if (alignValue === 'center') {
        $('[onclick*="justifyCenter"]').addClass('active');
    } else if (alignValue === 'right') {
        $('[onclick*="justifyRight"]').addClass('active');
    }

    if (format.font) {
        $('#fontFamily').val(format.font);
    } else {
        $('#fontFamily').val('sans-serif');
    }
}

function syncEditorContent() {
    if (quillEditor) {
        const textarea = document.getElementById('news_content');
        if (textarea) {
            textarea.value = quillEditor.root.innerHTML;
        }
    }
}

// ==================== WRAP TEXT FUNCTIONS ====================

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
    if (typeof showToast === 'function') {
        showToast('Đã xóa định dạng HTML!', 'success');
    }
};

// ==================== IMAGE UPLOAD ====================

window.insertImageToTextarea = function() {
    if (!quillEditor) return;

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                if (typeof showToast === 'function') {
                    showToast('Ảnh không được vượt quá 5MB!', 'error');
                }
                return;
            }

            const reader = new FileReader();
            reader.onload = function(ev) {
                const range = quillEditor.getSelection(true);
                quillEditor.insertEmbed(range.index, 'image', ev.target.result);
                setTimeout(function() {
                    quillEditor.setSelection(range.index + 1, 0);
                }, 10);
                if (typeof showToast === 'function') {
                    showToast('Đã chèn ảnh thành công!', 'success');
                }
            };
            reader.readAsDataURL(file);
        }
    };
    input.click();
};

// ==================== VIDEO MODAL ====================

function openVideoModalForTextarea() {
    if (!quillEditor) {
        if (typeof showToast === 'function') {
            showToast('Vui lòng đợi editor tải xong!', 'error');
        }
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
        if (typeof showToast === 'function') {
            showToast('Vui lòng đợi editor tải xong!', 'error');
        }
        return;
    }

    const urlInput = document.getElementById('youtubeUrl');
    if (!urlInput) return;

    const url = urlInput.value.trim();

    if (!url) {
        if (typeof showToast === 'function') {
            showToast('Vui lòng nhập URL YouTube!', 'error');
        }
        urlInput.focus();
        urlInput.style.borderColor = '#ef4444';
        setTimeout(function() {
            urlInput.style.borderColor = '#e2e8f0';
        }, 2000);
        return;
    }

    const videoId = getYoutubeId(url);
    if (!videoId) {
        if (typeof showToast === 'function') {
            showToast('Link YouTube không hợp lệ!', 'error');
        }
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
    if (typeof showToast === 'function') {
        showToast('Đã chèn video YouTube thành công!', 'success');
    }
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
        if (typeof showToast === 'function') {
            showToast('Video không được vượt quá 50MB!', 'error');
        }
        event.target.value = '';
        return;
    }

    const validTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
    if (!validTypes.includes(file.type)) {
        if (typeof showToast === 'function') {
            showToast('Định dạng video không hỗ trợ! Vui lòng chọn MP4, WebM, OGG hoặc MOV.', 'error');
        }
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
        if (typeof showToast === 'function') {
            showToast('Vui lòng đợi editor tải xong!', 'error');
        }
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
        if (typeof showToast === 'function') {
            showToast('Đã chèn video từ File thành công!', 'success');
        }
        setTimeout(updateToolbarStateQuill, 10);
    };

    reader.onerror = function() {
        if (typeof showToast === 'function') {
            showToast('Lỗi đọc file video!', 'error');
        }
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

function initColorPicker() {
    const themeColorsHtml = themeColors.map(color => 
        `<div class="color-item" data-color="${color}" style="background-color: ${color};"></div>`
    ).join('');

    $('.theme-colors, .highlight-theme-colors').html(themeColorsHtml);

    const standardColorsHtml = standardColors.map(color => 
        `<div class="color-item" data-color="${color}" style="background-color: ${color};"></div>`
    ).join('');

    $('.standard-colors, .highlight-standard-colors').html(standardColorsHtml);

    $(document).off('click', '.color-item').on('click', '.color-item', function(e) {
        const color = $(this).data('color');
        const isHighlight = $(this).closest('#highlightDropdown').length > 0;

        applyColorToQuill(color, isHighlight);

        $('#colorDropdown').hide();
        $('#highlightDropdown').hide();
        setTimeout(updateToolbarStateQuill, 10);
    });

    $('#noColorOption').off('click').on('click', function() {
        applyColorToQuill(null, false);
        $('#colorDropdown').hide();
    });

    $('#noHighlightOption').off('click').on('click', function() {
        applyColorToQuill(null, true);
        $('#highlightDropdown').hide();
    });

    $('#moreColorsOption, #moreHighlightColorsOption').off('click').on('click', function() {
        const isHighlight = $(this).attr('id') === 'moreHighlightColorsOption';
        showMoreColorsModalForQuill(isHighlight);
        $('#colorDropdown').hide();
        $('#highlightDropdown').hide();
    });

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

    const indicatorId = isHighlight ? '#highlightIndicator' : '#colorIndicator';
    $(indicatorId).css('background-color', color || 'transparent');

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

// ==================== FONT FAMILY & HEADING HANDLERS ====================

$('#fontFamily').off('change').on('change', function() {
    if (!quillEditor) return;
    const font = $(this).val();
    if (font) {
        quillEditor.format('font', font);
    }
    setTimeout(updateToolbarStateQuill, 10);
});

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

// ==================== EXPOSE GLOBAL FUNCTIONS ====================

window.quillEditor = {
    init: initQuillEditor,
    getEditor: function() { return quillEditor; },
    isInitialized: function() { return quillInitialized; },
    syncContent: syncEditorContent,
    updateToolbar: updateToolbarStateQuill,
    insertImage: insertImageToTextarea,
    insertVideo: openVideoModalForTextarea,
    applyColor: applyColorToQuill
};


// ==================== news-create.js ====================
// File chính - đã loại bỏ các hàm Quill Editor và image preview

// ==================== TOAST NOTIFICATION ====================

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

// ==================== VALIDATION & REQUIRED FIELDS ====================

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
        selector: '#news_content',
        name: 'content',
        label: 'Bài viết',
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

    // Kiểm tra slug - sử dụng hàm từ slugUtils
    const $slug = $form.find('#slug');
    const slug = $slug.val();
    if (slug && typeof window.slugUtils !== 'undefined' && !window.slugUtils.isValidSlug(slug)) {
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

    // ===== GẮN CÁC HANDLER CHO SLUG TỪ FILE RIÊNG =====
    if (typeof window.slugUtils !== 'undefined' && window.slugUtils.bindSlugHandlers) {
        window.slugUtils.bindSlugHandlers($form);
    }

    // ===== GẮN CÁC HANDLER CHO IMAGE UPLOAD TỪ FILE RIÊNG =====
    if (typeof window.imageHandler !== 'undefined' && window.imageHandler.bindHandlers) {
        window.imageHandler.bindHandlers($form);
    }

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
        if (typeof window.slugUtils !== 'undefined' && window.slugUtils.updateSlug) {
            window.slugUtils.updateSlug($form);
        }

        // Đồng bộ nội dung từ Quill Editor
        if (typeof window.quillEditor !== 'undefined' && window.quillEditor.syncContent) {
            window.quillEditor.syncContent();
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
        if (typeof window.slugUtils !== 'undefined' && window.slugUtils.generateSlugFromTitle) {
            if (!initialSlug || initialSlug.trim() === '' || initialSlug !== window.slugUtils.generateSlugFromTitle(initialTitle)) {
                if (typeof window.slugUtils.updateSlug === 'function') {
                    window.slugUtils.updateSlug($form);
                }
            }
        }
    }
}

// ==================== INITIALIZATION ====================

function initNewsForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#newsForm');

    if (!$form.length) return;

    bindNewsFormHandlers($form);

    // Đảm bảo slug sync sau khi init
    if (typeof window.slugUtils !== 'undefined' && window.slugUtils.ensureSlugSync) {
        window.slugUtils.ensureSlugSync($form);
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
        if (typeof window.slugUtils !== 'undefined' && window.slugUtils.updateSlug) {
            window.slugUtils.updateSlug($form);
        }
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
    // Khởi tạo Quill Editor từ file quill-editor.js
    if (typeof window.quillEditor !== 'undefined' && window.quillEditor.init) {
        window.quillEditor.init();
    }

    // Khởi tạo Color Picker từ file quill-editor.js
    if (typeof initColorPicker === 'function') {
        initColorPicker();
    }

    // Khởi tạo form với server errors
    const serverErrors = window.serverErrors || null;
    window.newsForm.init(document, serverErrors);

    // Cập nhật toolbar state sau khi khởi tạo
    setTimeout(function() {
        if (typeof updateToolbarStateQuill === 'function') {
            updateToolbarStateQuill();
        }
    }, 100);
});