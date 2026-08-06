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



