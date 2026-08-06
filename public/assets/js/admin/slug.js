// ============================================
// MODULE: SLUG GENERATOR
// ============================================
(function($) {
    'use strict';

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

            // Hiệu ứng highlight khi cập nhật
            $slugElement.css({
                backgroundColor: settings.highlightColor,
                transition: 'all 0.3s ease'
            });

            setTimeout(function() {
                $slugElement.css('backgroundColor', '#f3f4f6');
            }, settings.highlightDuration);

            // Callback nếu có
            if (typeof settings.onUpdate === 'function') {
                settings.onUpdate(newSlug, oldSlug);
            }
        },

        // Khóa chỉnh sửa slug
        lockEditing: function($slugElement, message) {
            if (!$slugElement.length) return;

            const lockMessage = message || 'Slug được tạo tự động từ tiêu đề!';

            $slugElement.on('copy cut paste', function(e) {
                e.preventDefault();
                alert(lockMessage);
                return false;
            });

            $slugElement.on('keydown', function(e) {
                e.preventDefault();
                alert(lockMessage);
                return false;
            });

            $slugElement.attr('title', lockMessage);
        },

        // Bind vào form - HỖ TRỢ CẢ HAI FORM
        bindToForm: function($form, options) {
            const self = this;
            
            // Tìm title và slug trong form - hỗ trợ cả 2 loại id
            const $title = $form.find('#recruitment_title, #news_title');
            const $slug = $form.find('#slug');

            if (!$title.length || !$slug.length) {
                console.warn('Không tìm thấy title hoặc slug trong form');
                return;
            }

            const settings = $.extend({
                lockEditing: true,
                lockMessage: 'Slug được tạo tự động từ tiêu đề, không thể chỉnh sửa trực tiếp',
                updateOnInput: true,
                autoGenerateInitial: true
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
                }
            };
        }
    };

    // Expose global
    window.SlugGenerator = SlugGenerator;

})(jQuery);