// ============================================
// MODULE: SLUG GENERATOR & IMAGE PREVIEW
// ============================================
(function($) {
    'use strict';

    // ============================================
    // SLUG GENERATOR MODULE
    // ============================================
    const SlugGenerator = {
        // Hàm loại bỏ dấu tiếng Việt
        removeVietnameseTones: function(str) {
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

            Object.keys(accentsMap).forEach(function(key) {
                result = result.replace(accentsMap[key], key);
            });

            return result;
        },

        // Tạo slug từ tiêu đề
        generateFromTitle: function(title) {
            if (!title || title.trim() === '') {
                return '';
            }
            
            let slug = this.removeVietnameseTones(title)
                .toLowerCase()
                .replace(/[^\w\s]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '')
                .substring(0, 100);

            return slug;
        },

        // Kiểm tra slug hợp lệ
        isValid: function(slug) {
            if (!slug || slug.trim() === '') {
                return false;
            }
            return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug);
        },

        // Cập nhật slug tự động
        autoUpdate: function($titleElement, $slugElement, options) {
            if (!$titleElement.length || !$slugElement.length) {
                return;
            }

            const self = this;
            const settings = $.extend({
                highlightColor: '#fef3c7',
                highlightDuration: 500,
                onUpdate: null
            }, options);

            const title = $titleElement.val();
            const newSlug = self.generateFromTitle(title);
            const oldSlug = $slugElement.val();

            if (newSlug === oldSlug) {
                return;
            }

            $slugElement.val(newSlug);

            // Cập nhật hidden field slug_original nếu tồn tại
            const $slugOriginal = $slugElement.closest('form').find('#slug_original, #slugOriginal');
            if ($slugOriginal.length) {
                $slugOriginal.val(newSlug);
            }

            // Hiệu ứng highlight khi cập nhật
            $slugElement.css({
                backgroundColor: settings.highlightColor,
                transition: 'all 0.3s ease'
            });

            setTimeout(function() {
                $slugElement.css('backgroundColor', '');
            }, settings.highlightDuration);

            // Callback nếu có
            if (typeof settings.onUpdate === 'function') {
                settings.onUpdate(newSlug, oldSlug);
            }
        },

        // Khóa chỉnh sửa slug
        lockEditing: function($slugElement, message) {
            if (!$slugElement.length) return;

            const lockMessage = message || 'Slug được tạo tự động từ tiêu đề, không thể chỉnh sửa trực tiếp';

            // Ngăn chặn copy, cut, paste
            $slugElement.on('copy cut paste', function(e) {
                e.preventDefault();
                return false;
            });

            // Ngăn chặn nhập liệu
            $slugElement.on('keydown', function(e) {
                // Cho phép các phím: Tab, Home, End, Arrow keys
                const allowedKeys = [9, 35, 36, 37, 38, 39, 40];
                if (allowedKeys.indexOf(e.which) !== -1) {
                    return;
                }
                e.preventDefault();
                return false;
            });

            // Ngăn chặn click chuột phải
            $slugElement.on('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Thêm tooltip
            $slugElement.attr('title', lockMessage);
            $slugElement.attr('readonly', true);

            // Style để hiển thị readonly
            $slugElement.css({
                cursor: 'not-allowed',
                backgroundColor: '#f3f4f6'
            });
        },

        // Bind vào form - HỖ TRỢ CẢ HAI FORM
        bindToForm: function($form, options) {
            const self = this;
            const $title = $form.find('#recruitment_title, #news_title');
            const $slug = $form.find('#slug');

            if (!$title.length || !$slug.length) {
                console.warn('Không tìm thấy title hoặc slug trong form');
                return null;
            }

            // Xác định loại form dựa trên ID title
            const isRecruitment = $form.find('#recruitment_title').length > 0;
            const isNews = $form.find('#news_title').length > 0;

            const settings = $.extend({
                lockEditing: true,
                lockMessage: 'Slug được tạo tự động từ tiêu đề, không thể chỉnh sửa trực tiếp',
                updateOnInput: true,
                autoGenerateInitial: true,
                onUpdate: null
            }, options);

            // Tự động cập nhật khi nhập title
            if (settings.updateOnInput) {
                $title.on('input', function() {
                    self.autoUpdate($title, $slug, {
                        onUpdate: settings.onUpdate
                    });
                });
            }

            // Khóa chỉnh sửa slug
            if (settings.lockEditing) {
                self.lockEditing($slug, settings.lockMessage);
            }

            // Tạo slug ban đầu nếu có title
            if (settings.autoGenerateInitial) {
                const initialTitle = $title.val();
                if (initialTitle && initialTitle.trim() !== '') {
                    self.autoUpdate($title, $slug);
                }
            }

            // Return methods để có thể tương tác sau này
            return {
                generateSlug: function() {
                    return self.generateFromTitle($title.val());
                },
                updateSlug: function() {
                    self.autoUpdate($title, $slug);
                },
                getTitle: function() {
                    return $title;
                },
                getSlug: function() {
                    return $slug;
                },
                isRecruitment: isRecruitment,
                isNews: isNews
            };
        }
    };

    // ============================================
    // IMAGE PREVIEW MODULE
    // ============================================
    const ImagePreview = {
        // Cấu hình mặc định
        defaults: {
            maxSize: 15 * 1024 * 1024, // 2MB
            previewSelector: '#imagePreview',
            previewImgSelector: '#previewImg',
            uploadBoxSelector: '#uploadBox',
            uploadTextSelector: '#uploadText',
            uploadInfoSelector: '#uploadInfo',
            inputSelector: '#imageInput',
            allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
        },

        // Lưu trữ instances
        instances: new Map(),

        /**
         * Khởi tạo preview ảnh cho form
         * @param {jQuery|string} $form - Form hoặc selector
         * @param {Object} options - Cấu hình tùy chỉnh
         * @returns {Object} API của instance
         */
        init: function($form, options) {
            $form = $form instanceof jQuery ? $form : $($form);
            if (!$form.length) return null;

            // Merge options với defaults
            const config = $.extend({}, this.defaults, options);

            // Tìm các element trong form
            const elements = {
                $preview: $form.find(config.previewSelector),
                $previewImg: $form.find(config.previewImgSelector),
                $uploadBox: $form.find(config.uploadBoxSelector),
                $uploadText: $form.find(config.uploadTextSelector),
                $uploadInfo: $form.find(config.uploadInfoSelector),
                $input: $form.find(config.inputSelector)
            };

            // Kiểm tra các element cần thiết
            if (!elements.$input.length) {
                console.warn('ImagePreview: Không tìm thấy input file');
                return null;
            }

            // Tạo instance ID
            const instanceId = 'image-preview-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5);
            
            // Lưu instance
            const instance = {
                id: instanceId,
                form: $form,
                config: config,
                elements: elements,
                currentFile: null,
                isPreviewShown: false
            };

            this.instances.set(instanceId, instance);

            // Bind events
            this._bindEvents(instance);

            return this._createAPI(instance);
        },

        /**
         * Bind các sự kiện cho instance
         * @private
         */
        _bindEvents: function(instance) {
            const { elements, config } = instance;
            const { $uploadBox, $input, $previewImg, $preview } = elements;

            // Click vào upload box để mở file dialog
            $uploadBox.on('click', function(e) {
                // Không trigger nếu click vào preview image
                if ($(e.target).closest($previewImg).length) {
                    return;
                }
                $input.trigger('click');
            });

            // Click vào preview image để mở file dialog
            $previewImg.on('click', function(e) {
                e.stopPropagation();
                $input.trigger('click');
            });

            // Xử lý khi chọn file
            $input.on('change', function() {
                ImagePreview._handleFileSelect(instance, this);
            });

            // Xử lý drag and drop
            $uploadBox.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('drag-over');
            });

            $uploadBox.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
            });

            $uploadBox.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');

                const files = e.originalEvent.dataTransfer.files;
                if (files && files.length > 0) {
                    $input[0].files = files;
                    $input.trigger('change');
                }
            });
        },

        /**
         * Xử lý khi chọn file
         * @private
         */
        _handleFileSelect: function(instance, inputElement) {
            const { elements, config } = instance;
            const { $input, $preview, $previewImg, $uploadBox, $uploadText, $uploadInfo } = elements;

            if (!inputElement.files || !inputElement.files[0]) {
                this._clearPreview(instance);
                return;
            }

            const file = inputElement.files[0];

            // Kiểm tra loại file
            if (!config.allowedTypes.includes(file.type)) {
                this._showError(instance, 'Định dạng ảnh không hỗ trợ. Vui lòng chọn JPG, PNG, GIF hoặc WEBP');
                this._clearFileInput(instance);
                return;
            }

            // Kiểm tra kích thước
            if (file.size > config.maxSize) {
                const maxSizeMB = (config.maxSize / (1024 * 1024)).toFixed(1);
                this._showError(instance, `Ảnh không được vượt quá ${maxSizeMB}MB!`);
                this._clearFileInput(instance);
                return;
            }

            // Lưu file hiện tại
            instance.currentFile = file;

            // Đọc và hiển thị ảnh
            const reader = new FileReader();
            reader.onload = function(e) {
                $previewImg.attr('src', e.target.result);
                $preview.show();
                $uploadBox.css('opacity', '0.5');
                $uploadText.html('Đã chọn ảnh: ' + file.name);
                $uploadInfo.html('Click để đổi ảnh khác');
                instance.isPreviewShown = true;
                
                // Xóa lỗi
                $uploadBox.removeClass('error-field');
                $uploadBox.closest('.form-group').find('.field-error-msg').remove();

                // Trigger event
                $input.trigger('imageLoaded', [file, e.target.result]);
            };

            reader.onerror = function() {
                ImagePreview._showError(instance, 'Không thể đọc file. Vui lòng thử lại');
                ImagePreview._clearFileInput(instance);
            };

            reader.readAsDataURL(file);
        },

        /**
         * Xóa preview
         * @private
         */
        _clearPreview: function(instance) {
            const { elements } = instance;
            const { $preview, $previewImg, $uploadBox, $uploadText, $uploadInfo } = elements;

            $previewImg.attr('src', '');
            $preview.hide();
            $uploadBox.css('opacity', '1');
            $uploadText.html('Click hoặc kéo thả ảnh vào đây');
            $uploadInfo.html('PNG, JPG, GIF, WEBP (Tối đa 2MB)');
            instance.isPreviewShown = false;
            instance.currentFile = null;
        },

        /**
         * Xóa input file
         * @private
         */
        _clearFileInput: function(instance) {
            const { $input } = instance.elements;
            $input.val('');
            this._clearPreview(instance);
        },

        /**
         * Hiển thị lỗi
         * @private
         */
        _showError: function(instance, message) {
            const { $uploadBox } = instance.elements;
            
            // Tìm hoặc tạo error message
            let $errorMsg = $uploadBox.closest('.form-group').find('.field-error-msg');
            if (!$errorMsg.length) {
                $errorMsg = $('<div>').addClass('field-error-msg');
                $uploadBox.after($errorMsg);
            }
            $errorMsg.html('⚠️ ' + message);
            $uploadBox.addClass('error-field');

            // Hiển thị toast nếu có
            if (window.showToast) {
                window.showToast('⚠️ ' + message, 'error');
            }
        },

        /**
         * Tạo API cho instance
         * @private
         */
        _createAPI: function(instance) {
            const self = this;
            return {
                /**
                 * Lấy dữ liệu ảnh hiện tại
                 * @returns {Object|null} { file: File, dataURL: string } hoặc null
                 */
                getImageData: function() {
                    if (!instance.isPreviewShown || !instance.currentFile) {
                        return null;
                    }
                    return {
                        file: instance.currentFile,
                        dataURL: instance.elements.$previewImg.attr('src')
                    };
                },

                /**
                 * Lấy file hiện tại
                 * @returns {File|null}
                 */
                getFile: function() {
                    return instance.currentFile || null;
                },

                /**
                 * Kiểm tra có ảnh không
                 * @returns {boolean}
                 */
                hasImage: function() {
                    return instance.isPreviewShown && !!instance.currentFile;
                },

                /**
                 * Xóa ảnh đã chọn
                 */
                clear: function() {
                    self._clearFileInput(instance);
                },

                /**
                 * Cập nhật ảnh từ URL
                 * @param {string} url - URL của ảnh
                 */
                setImageFromUrl: function(url) {
                    const { $preview, $previewImg, $uploadBox, $uploadText, $uploadInfo } = instance.elements;
                    $previewImg.attr('src', url);
                    $preview.show();
                    $uploadBox.css('opacity', '0.5');
                    $uploadText.html('Ảnh đã được tải lên');
                    $uploadInfo.html('Click để đổi ảnh khác');
                    instance.isPreviewShown = true;
                    instance.currentFile = null; // Không có file object khi set từ URL
                },

                /**
                 * Validate ảnh
                 * @param {Object} options - Tùy chọn validate
                 * @returns {Object} { valid: boolean, message: string }
                 */
                validate: function(options) {
                    options = options || {};
                    const minWidth = options.minWidth || 0;
                    const minHeight = options.minHeight || 0;
                    const maxSize = options.maxSize || instance.config.maxSize;

                    if (!instance.isPreviewShown) {
                        return { valid: false, message: 'Vui lòng chọn ảnh' };
                    }

                    if (instance.currentFile && instance.currentFile.size > maxSize) {
                        const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
                        return { valid: false, message: `Ảnh không được vượt quá ${maxSizeMB}MB` };
                    }

                    // Kiểm tra kích thước ảnh nếu có image element
                    if (minWidth > 0 || minHeight > 0) {
                        const $img = instance.elements.$previewImg;
                        if ($img.length && $img[0].naturalWidth) {
                            const width = $img[0].naturalWidth;
                            const height = $img[0].naturalHeight;
                            
                            if (minWidth > 0 && width < minWidth) {
                                return { valid: false, message: `Ảnh phải có chiều rộng tối thiểu ${minWidth}px` };
                            }
                            if (minHeight > 0 && height < minHeight) {
                                return { valid: false, message: `Ảnh phải có chiều cao tối thiểu ${minHeight}px` };
                            }
                        }
                    }

                    return { valid: true, message: '' };
                },

                /**
                 * Destroy instance
                 */
                destroy: function() {
                    const { $input, $uploadBox, $previewImg } = instance.elements;
                    $input.off('change');
                    $uploadBox.off('click dragover dragleave drop');
                    $previewImg.off('click');
                    self._clearPreview(instance);
                    self.instances.delete(instance.id);
                }
            };
        },

        /**
         * Tạo preview ảnh từ input (hàm static cho compatibility)
         * @param {HTMLInputElement} input - Input element
         * @param {jQuery} $form - Form chứa input
         * @param {Function} callback - Callback sau khi preview
         */
        previewFromInput: function(input, $form, callback) {
            if (!input || !input.files || !input.files[0]) {
                return;
            }

            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024;

            if (file.size > maxSize) {
                if (window.showToast) {
                    window.showToast('Ảnh không được vượt quá 2MB!', 'error');
                }
                $(input).val('');
                return;
            }

            const $preview = $form.find('#imagePreview');
            const $previewImg = $form.find('#previewImg');
            const $uploadBox = $form.find('#uploadBox');
            const $uploadText = $form.find('#uploadText');
            const $uploadInfo = $form.find('#uploadInfo');

            const reader = new FileReader();
            reader.onload = function(e) {
                $previewImg.attr('src', e.target.result);
                $preview.show();
                $uploadBox.css('opacity', '0.5');
                $uploadText.html('Đã chọn ảnh: ' + file.name);
                $uploadInfo.html('Click để đổi ảnh khác');
                $uploadBox.removeClass('error-field');
                $uploadBox.closest('.form-group').find('.field-error-msg').remove();

                if (typeof callback === 'function') {
                    callback(file, e.target.result);
                }
            };
            reader.readAsDataURL(file);
        },

        /**
         * Clear preview (hàm static cho compatibility)
         * @param {jQuery} $form - Form chứa preview
         */
        clearPreview: function($form) {
            $form = $form instanceof jQuery ? $form : $($form);
            if (!$form.length) return;

            const $preview = $form.find('#imagePreview');
            const $previewImg = $form.find('#previewImg');
            const $uploadBox = $form.find('#uploadBox');
            const $uploadText = $form.find('#uploadText');
            const $uploadInfo = $form.find('#uploadInfo');
            const $input = $form.find('#imageInput');

            $previewImg.attr('src', '');
            $preview.hide();
            $uploadBox.css('opacity', '1');
            $uploadText.html('Click hoặc kéo thả ảnh vào đây');
            $uploadInfo.html('PNG, JPG, GIF, WEBP (Tối đa 2MB)');
            $input.val('');
        },

        /**
         * Validate ảnh (hàm static cho compatibility)
         * @param {jQuery} $form - Form chứa ảnh
         * @param {Object} options - Tùy chọn validate
         * @returns {Object} { valid: boolean, message: string }
         */
        validateImage: function($form, options) {
            $form = $form instanceof jQuery ? $form : $($form);
            options = options || {};

            const $preview = $form.find('#imagePreview');
            const $previewImg = $form.find('#previewImg');
            const isVisible = $preview.is(':visible') && $previewImg.attr('src');

            if (!options.required && !isVisible) {
                return { valid: true, message: '' };
            }

            if (options.required && !isVisible) {
                return { valid: false, message: 'Vui lòng chọn ảnh đại diện' };
            }

            if (isVisible) {
                const $uploadBox = $form.find('#uploadBox');
                const hasError = $uploadBox.hasClass('error-field');
                if (hasError) {
                    const $errorMsg = $uploadBox.closest('.form-group').find('.field-error-msg');
                    return { 
                        valid: false, 
                        message: $errorMsg.length ? $errorMsg.text().replace('⚠️ ', '') : 'Ảnh không hợp lệ' 
                    };
                }
            }

            return { valid: true, message: '' };
        }
    };

    // ============================================
    // EXPOSE GLOBAL
    // ============================================

    window.SlugGenerator = SlugGenerator;
    window.ImagePreview = ImagePreview;

    // ============================================
    // AUTO INIT ON DOM READY
    // ============================================

    $(function() {
        // Tự động init SlugGenerator cho các form
        $('form').each(function() {
            const $form = $(this);
            const hasTitle = $form.find('#recruitment_title, #news_title').length > 0;
            const hasSlug = $form.find('#slug').length > 0;
            
            if (hasTitle && hasSlug) {
                // Kiểm tra nếu chưa được init
                if (!$form.data('slug-init')) {
                    $form.data('slug-init', true);
                    SlugGenerator.bindToForm($form);
                }
            }
        });

        // Tự động init ImagePreview cho các form có uploadBox và imageInput
        $('form').each(function() {
            const $form = $(this);
            if ($form.find('#uploadBox').length && $form.find('#imageInput').length) {
                // Kiểm tra nếu chưa được init
                if (!$form.data('image-preview-init')) {
                    $form.data('image-preview-init', true);
                    const imagePreview = ImagePreview.init($form);
                    if (imagePreview) {
                        $form.data('image-preview-api', imagePreview);
                    }
                }
            }
        });

        // ============================================
        // HỖ TRỢ CHO create-news.php (VIDEO MODAL)
        // ============================================
        
        // Định nghĩa các hàm global cho video modal nếu chưa tồn tại
        if (typeof window.openVideoModalForTextarea !== 'function') {
            window.openVideoModalForTextarea = function() {
                const modal = document.getElementById('videoModal');
                if (modal) {
                    modal.style.display = 'flex';
                    modal.classList.add('show');
                    // Focus vào input
                    const input = document.getElementById('youtubeUrl');
                    if (input) setTimeout(function() { input.focus(); }, 100);
                }
            };
        }

        if (typeof window.closeVideoModal !== 'function') {
            window.closeVideoModal = function() {
                const modal = document.getElementById('videoModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    // Reset input
                    const input = document.getElementById('youtubeUrl');
                    if (input) input.value = '';
                    // Reset file input
                    const fileInput = document.getElementById('videoFileInput');
                    if (fileInput) fileInput.value = '';
                    const fileName = document.getElementById('videoFileName');
                    if (fileName) {
                        fileName.style.display = 'none';
                        fileName.textContent = '';
                    }
                }
            };
        }

        if (typeof window.insertYoutubeVideo !== 'function') {
            window.insertYoutubeVideo = function() {
                const urlInput = document.getElementById('youtubeUrl');
                const url = urlInput ? urlInput.value.trim() : '';
                
                if (!url) {
                    alert('Vui lòng nhập URL YouTube');
                    return;
                }

                // Lấy video ID từ URL
                let videoId = '';
                const patterns = [
                    /(?:youtube\.com\/watch\?v=)([^&]+)/,
                    /(?:youtu\.be\/)([^?]+)/,
                    /(?:youtube\.com\/embed\/)([^?]+)/
                ];

                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) {
                        videoId = match[1];
                        break;
                    }
                }

                if (!videoId) {
                    alert('URL YouTube không hợp lệ. Vui lòng nhập đúng định dạng.');
                    return;
                }

                // Tạo iframe embed
                const embedCode = `<div class="video-wrapper"><iframe width="100%" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>`;
                
                // Chèn vào textarea
                const textarea = document.getElementById('news_content');
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    const before = text.substring(0, start);
                    const after = text.substring(end);
                    textarea.value = before + embedCode + after;
                    textarea.selectionStart = textarea.selectionEnd = start + embedCode.length;
                    textarea.focus();
                }

                closeVideoModal();
            };
        }

        if (typeof window.handleVideoFileUpload !== 'function') {
            window.handleVideoFileUpload = function(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Kiểm tra loại file
                if (!file.type.startsWith('video/')) {
                    alert('Vui lòng chọn file video');
                    event.target.value = '';
                    return;
                }

                // Kiểm tra kích thước (tối đa 50MB)
                if (file.size > 50 * 1024 * 1024) {
                    alert('Video không được vượt quá 50MB');
                    event.target.value = '';
                    return;
                }

                // Hiển thị tên file
                const fileName = document.getElementById('videoFileName');
                if (fileName) {
                    fileName.textContent = '📹 ' + file.name;
                    fileName.style.display = 'block';
                }

                // Tạo URL để preview
                const url = URL.createObjectURL(file);
                const embedCode = `<div class="video-wrapper"><video width="100%" controls><source src="${url}" type="${file.type}">Trình duyệt của bạn không hỗ trợ video.</video></div>`;
                
                // Chèn vào textarea
                const textarea = document.getElementById('news_content');
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    const before = text.substring(0, start);
                    const after = text.substring(end);
                    textarea.value = before + embedCode + after;
                    textarea.selectionStart = textarea.selectionEnd = start + embedCode.length;
                    textarea.focus();
                }

                closeVideoModal();
            };
        }

        // Đóng modal khi click bên ngoài
        $(document).on('click', '#videoModal', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });

        // Đóng modal với phím ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVideoModal();
            }
        });
    });

})(jQuery);