// ============================================
// MODULE: RECRUITMENT FORM
// ============================================
(function($) {
    'use strict';

    // Constants
    const aiTitles = [
        'Tuyển dụng Chuyên viên IT - Lương hấp dẫn - Môi trường chuyên nghiệp',
        'Cần tuyển gấp Trưởng phòng Kinh doanh - Thưởng Tết hấp dẫn',
        'Công ty TNHH ABC tuyển dụng Kế toán trưởng - Lương cạnh tranh',
        'Urgent! Tuyển Nhân viên Marketing - Làm việc tại Quận 1 - Lương 15-20tr',
        'Tuyển dụng Lập trình viên Fullstack - Làm việc remote',
        'Cần tìm Nhân viên Chăm sóc khách hàng - Tiếng Anh tốt'
    ];

    const aiDescription = '<p><strong>Mô tả công việc:</strong></p>\n<ul>\n<li>Thực hiện các công việc chuyên môn theo đúng quy trình của công ty</li>\n<li>Phối hợp với các phòng ban để đảm bảo tiến độ công việc</li>\n<li>Báo cáo kết quả công việc định kỳ cho cấp trên trực tiếp</li>\n<li>Tham gia các dự án theo sự phân công của quản lý</li>\n<li>Đề xuất các giải pháp cải thiện quy trình làm việc</li>\n</ul>';

    const aiRequirements = '<ul>\n<li>Tốt nghiệp Cao đẳng / Đại học chuyên ngành phù hợp</li>\n<li>Có ít nhất 1-2 năm kinh nghiệm trong lĩnh vực tương tự</li>\n<li>Thành thạo các công cụ văn phòng (Word, Excel, PowerPoint)</li>\n<li>Kỹ năng giao tiếp, làm việc nhóm tốt</li>\n<li>Chủ động, sáng tạo và có tinh thần trách nhiệm cao</li>\n</ul>';

    const aiBenefits = '<ul>\n<li>Lương cạnh tranh + thưởng hiệu quả công việc</li>\n<li>Đầy đủ BHXH, BHYT, BHTN theo quy định</li>\n<li>Môi trường làm việc năng động, thân thiện</li>\n<li>Cơ hội thăng tiến và đào tạo chuyên sâu</li>\n<li>Các hoạt động team building, du lịch hàng năm</li>\n</ul>';

    // Mapping field selectors cho các trường required
    const requiredFieldsMap = [
        {
            selector: '#recruitment_title',
            name: 'title',
            label: 'Tiêu đề tin tuyển dụng',
            getMessage: function() { return 'Vui lòng nhập tiêu đề tin tuyển dụng'; }
        },
        {
            selector: 'textarea[name="work_location"]',
            name: 'work_location',
            label: 'Địa điểm làm việc',
            getMessage: function() { return 'Vui lòng nhập địa điểm làm việc'; }
        },
        {
            selector: 'input[name="quantity"]',
            name: 'quantity',
            label: 'Số lượng cần tuyển',
            getMessage: function() { return 'Số lượng cần tuyển phải lớn hơn 0'; }
        },
        {
            selector: '#deadline',
            name: 'deadline',
            label: 'Hạn nộp hồ sơ',
            getMessage: function() { return 'Vui lòng chọn hạn nộp hồ sơ'; }
        },
        {
            selector: '#job_description',
            name: 'description',
            label: 'Mô tả công việc',
            getMessage: function() { return 'Vui lòng nhập mô tả công việc'; }
        },
        {
            selector: '#job_requirements',
            name: 'requirements',
            label: 'Yêu cầu ứng viên',
            getMessage: function() { return 'Vui lòng nhập yêu cầu ứng viên'; }
        }
    ];

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    // Hiển thị toast message
    function showToast(message, type) {
        $('.toast').remove();

        const toast = $('<div>')
            .addClass('toast toast-' + type)
            .html(message.replace(/\n/g, '<br>'))
            .hide();

        $('body').append(toast);
        toast.fadeIn(300);

        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Scroll đến element bị lỗi với hiệu ứng highlight
    function scrollToErrorElement($element, offset) {
        offset = offset || 120;
        if (!$element || !$element.length) return;

        $element.removeClass('error-highlight');
        void $element[0].offsetWidth;
        $element.addClass('error-highlight');

        var elementPosition = $element.offset().top;
        var offsetPosition = elementPosition - offset;

        $('html, body').animate({
            scrollTop: offsetPosition
        }, 500, function() {
            if ($element.is(':visible') && !$element.is('input[readonly]')) {
                $element.trigger('focus');
            }
        });

        setTimeout(function() {
            $element.removeClass('error-highlight');
        }, 2000);
    }

    // Lấy các field required
    function getRequiredFields($form) {
        var $requiredFields = [];

        $form.find('label').each(function() {
            var $label = $(this);
            if ($label.find('.required').length) {
                var forAttr = $label.attr('for');
                var $field = null;

                if (forAttr) {
                    $field = $form.find('#' + forAttr);
                } else {
                    $field = $label.closest('.form-group').find('input, textarea, select').first();
                }

                if ($field && $field.length) {
                    var fieldName = $field.attr('name') || $field.attr('id');
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

    // ============================================
    // VALIDATE DEADLINE
    // ============================================

    function initDeadlineValidation($form) {
        var $deadline = $form.find('#deadline');

        if (!$deadline.length || $deadline.data('deadline-bound')) {
            return;
        }

        $deadline.data('deadline-bound', true);

        function validateDeadline() {
            var deadlineValue = $deadline.val();
            var $warningMsg = $deadline.closest('.form-group').find('.deadline-warning');

            if (!deadlineValue) {
                if ($warningMsg.length) {
                    $warningMsg.remove();
                }
                $deadline.removeClass('error-field');
                return true;
            }

            var selectedDate = new Date(deadlineValue);
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                if (!$warningMsg.length) {
                    var $newWarning = $('<small>')
                        .addClass('deadline-warning')
                        .css({
                            'color': '#dc2626',
                            'font-size': '12px',
                            'margin-top': '5px',
                            'display': 'block'
                        })
                        .html('⚠️ Hạn nộp hồ sơ không được nhỏ hơn ngày hiện tại');
                    $deadline.after($newWarning);
                } else {
                    $warningMsg.show();
                }
                $deadline.addClass('error-field');
                return false;
            } else {
                if ($warningMsg.length) {
                    $warningMsg.remove();
                }
                $deadline.removeClass('error-field');
                return true;
            }
        }

        $deadline.on('change input', function() {
            validateDeadline();
            this.setCustomValidity('');
            $(this).closest('.form-group').find('.field-error-msg').remove();
            $(this).removeClass('error-field');
        });

        return validateDeadline;
    }

    // ============================================
    // XỬ LÝ MỨC LƯƠNG
    // ============================================

    function initSalaryField($form) {
        var $salaryInput = $form.find('#salary_range');
        var $salaryHidden = $form.find('#salary_value');
        var $salaryError = $form.find('#salary_error');

        if (!$salaryInput.length || $salaryInput.data('salary-bound')) {
            return;
        }

        $salaryInput.data('salary-bound', true);

        // Hàm xử lý khi người dùng nhập
        $salaryInput.on('input', function(e) {
            var rawValue = $(this).val();
            var isDeleting = false;

            // Kiểm tra nếu đang xóa
            if (e.originalEvent && e.originalEvent.inputType) {
                isDeleting = e.originalEvent.inputType === 'deleteContentBackward' ||
                    e.originalEvent.inputType === 'deleteContentForward';
            }

            // Tìm vị trí dấu "-" nếu có
            var dashIndex = rawValue.indexOf('-');
            var hasDash = dashIndex !== -1;

            // Tách phần trước và sau dấu "-"
            var beforeDash = '';
            var afterDash = '';

            if (hasDash) {
                beforeDash = rawValue.substring(0, dashIndex).trim();
                afterDash = rawValue.substring(dashIndex + 1).trim();
            } else {
                beforeDash = rawValue.trim();
            }

            // Xử lý phần trước dấu "-"
            var formattedBefore = '';
            var numberBefore = '';
            if (beforeDash) {
                numberBefore = beforeDash.replace(/[^0-9]/g, '');
                if (numberBefore) {
                    var num = parseInt(numberBefore, 10);
                    formattedBefore = num.toLocaleString('vi-VN');
                }
            }

            // Xử lý phần sau dấu "-"
            var formattedAfter = '';
            var numberAfter = '';
            if (hasDash && afterDash) {
                numberAfter = afterDash.replace(/[^0-9]/g, '');
                if (numberAfter) {
                    var num2 = parseInt(numberAfter, 10);
                    formattedAfter = num2.toLocaleString('vi-VN');
                }
            }

            // Xây dựng giá trị mới
            var newValue = '';
            if (formattedBefore) {
                newValue = formattedBefore;
            }

            if (hasDash) {
                if (!(isDeleting && !formattedAfter && afterDash === '')) {
                    newValue += ' - ';
                    if (formattedAfter) {
                        newValue += formattedAfter;
                    }
                }
            }

            // Nếu đang xóa và không còn nội dung
            if (isDeleting && !formattedBefore && !formattedAfter && beforeDash === '' && afterDash === '') {
                newValue = '';
            }

            $(this).val(newValue);

            // Cập nhật hidden input
            var finalNumberBefore = numberBefore || '';
            var finalNumberAfter = numberAfter || '';
            var finalValue = finalNumberBefore;
            if (hasDash && !(isDeleting && !finalNumberAfter)) {
                if (finalNumberAfter) {
                    finalValue += '-' + finalNumberAfter;
                } else if (!isDeleting) {
                    finalValue += '-';
                }
            }
            $salaryHidden.val(finalValue);

            // Ẩn lỗi khi đang nhập hợp lệ
            $salaryError.text('');
            $salaryError.hide();
            $(this).removeClass('error-field');
            $(this).closest('.form-group').find('.field-error-msg').remove();
        });

        // Xử lý khi blur
        $salaryInput.on('blur', function() {
            var value = $(this).val();

            if (!value || value.trim() === '') {
                $(this).val('');
                $salaryHidden.val('');
                return;
            }

            var parts = value.split('-').map(function(part) { return part.trim(); });
            var formattedParts = [];

            parts.forEach(function(part) {
                if (part) {
                    var numbers = part.replace(/[^0-9]/g, '');
                    if (numbers) {
                        var num = parseInt(numbers, 10);
                        formattedParts.push(num.toLocaleString('vi-VN'));
                    } else {
                        formattedParts.push(part);
                    }
                } else {
                    formattedParts.push('');
                }
            });

            var formattedValue = formattedParts.join(' - ');
            if (parts.length === 1) {
                formattedValue = formattedParts[0] || '';
            }

            $(this).val(formattedValue);

            // Cập nhật hidden input
            var numberParts = [];
            parts.forEach(function(part) {
                if (part) {
                    var numbers = part.replace(/[^0-9]/g, '');
                    numberParts.push(numbers || '');
                } else {
                    numberParts.push('');
                }
            });

            var finalValue = numberParts[0] || '';
            if (numberParts.length > 1 && numberParts[1]) {
                finalValue += '-' + numberParts[1];
            }
            $salaryHidden.val(finalValue);
        });

        // Validate mức lương
        function validateSalary() {
            var value = $salaryInput.val();
            if (!value || value.trim() === '') {
                $salaryError.text('Vui lòng nhập mức lương');
                $salaryError.show();
                $salaryInput.addClass('error-field');
                return false;
            }

            var numbers = value.replace(/[^0-9]/g, '');
            if (numbers === '') {
                $salaryError.text('Vui lòng nhập số tiền hợp lệ');
                $salaryError.show();
                $salaryInput.addClass('error-field');
                return false;
            }

            // Kiểm tra nếu có dấu "-"
            if (value.includes('-')) {
                var parts = value.split('-').map(function(part) { return part.trim(); });
                var hasBothNumbers = true;
                var hasAtLeastOneNumber = false;

                parts.forEach(function(part) {
                    if (part) {
                        var num = part.replace(/[^0-9]/g, '');
                        if (num !== '') {
                            hasAtLeastOneNumber = true;
                        }
                        if (num === '') {
                            hasBothNumbers = false;
                        }
                    }
                });

                if (!hasBothNumbers && parts.length > 1) {
                    $salaryError.text('Vui lòng nhập đầy đủ cả 2 mức lương');
                    $salaryError.show();
                    $salaryInput.addClass('error-field');
                    return false;
                }

                if (!hasAtLeastOneNumber) {
                    $salaryError.text('Vui lòng nhập số tiền hợp lệ');
                    $salaryError.show();
                    $salaryInput.addClass('error-field');
                    return false;
                }
            }

            $salaryError.text('');
            $salaryError.hide();
            $salaryInput.removeClass('error-field');
            return true;
        }

        // Validate khi submit form
        $form.on('submit', function(e) {
            if (!validateSalary()) {
                e.preventDefault();
            }
        });

        // Xử lý giá trị ban đầu
        var initialValue = $salaryInput.val();
        if (initialValue && initialValue.trim() !== '') {
            var parts = initialValue.split('-').map(function(part) { return part.trim(); });
            var formattedParts = [];

            parts.forEach(function(part) {
                if (part) {
                    var numbers = part.replace(/[^0-9]/g, '');
                    if (numbers) {
                        var num = parseInt(numbers, 10);
                        formattedParts.push(num.toLocaleString('vi-VN'));
                    } else {
                        formattedParts.push(part);
                    }
                } else {
                    formattedParts.push('');
                }
            });

            var formattedValue = formattedParts.join(' - ');
            if (parts.length === 1) {
                formattedValue = formattedParts[0] || '';
            }
            $salaryInput.val(formattedValue);

            var numberParts = [];
            parts.forEach(function(part) {
                if (part) {
                    var numbers = part.replace(/[^0-9]/g, '');
                    numberParts.push(numbers || '');
                } else {
                    numberParts.push('');
                }
            });

            var finalValue = numberParts[0] || '';
            if (numberParts.length > 1 && numberParts[1]) {
                finalValue += '-' + numberParts[1];
            }
            $salaryHidden.val(finalValue);
        }

        return validateSalary;
    }

    // ============================================
    // HIỂN THỊ LỖI TỪ SERVER
    // ============================================

    function displayServerErrors(errors, $form) {
        if (!errors || errors.length === 0) return false;

        $('.field-error-msg').remove();
        $('.error-field').removeClass('error-field');

        var requiredFields = getRequiredFields($form);
        var firstErrorElement = null;
        var errorList = [];

        var fieldMap = {};
        requiredFields.forEach(function(field) {
            if (field.name) {
                fieldMap[field.name] = field;
            }
            if (field.$element.attr('id')) {
                fieldMap[field.$element.attr('id')] = field;
            }
        });

        // Các field không có required
        var allFieldsMap = {
            'slug': { $element: $('#slug'), label: 'Slug' },
            'salary_range': { $element: $('#salary_range'), label: 'Mức lương' },
            'benefits': { $element: $('#job_benefits'), label: 'Quyền lợi' },
            'degree': { $element: $('select[name="degree"]'), label: 'Trình độ' },
            'work_type': { $element: $('select[name="work_type"]'), label: 'Hình thức làm việc' },
            'status': { $element: $('select[name="status"]'), label: 'Trạng thái' }
        };

        Object.assign(fieldMap, allFieldsMap);

        errors.forEach(function(error) {
            errorList.push(error);

            var $element = null;
            var fieldLabel = '';
            var errorLower = error.toLowerCase();

            if (errorLower.includes('tiêu đề') || errorLower.includes('title')) {
                $element = $('#recruitment_title');
                fieldLabel = 'Tiêu đề tin tuyển dụng';
            } else if (errorLower.includes('địa điểm') || errorLower.includes('work_location')) {
                $element = $('textarea[name="work_location"]');
                fieldLabel = 'Địa điểm làm việc';
            } else if (errorLower.includes('số lượng') || errorLower.includes('quantity')) {
                $element = $('input[name="quantity"]');
                fieldLabel = 'Số lượng cần tuyển';
            } else if (errorLower.includes('hạn nộp') || errorLower.includes('deadline')) {
                $element = $('#deadline');
                fieldLabel = 'Hạn nộp hồ sơ';
            } else if (errorLower.includes('mô tả') || errorLower.includes('description')) {
                $element = $('#job_description');
                fieldLabel = 'Mô tả công việc';
            } else if (errorLower.includes('yêu cầu') || errorLower.includes('requirements')) {
                $element = $('#job_requirements');
                fieldLabel = 'Yêu cầu ứng viên';
            } else if (errorLower.includes('lương') || errorLower.includes('salary')) {
                $element = $('#salary_range');
                fieldLabel = 'Mức lương';
            } else if (errorLower.includes('quyền lợi') || errorLower.includes('benefits')) {
                $element = $('#job_benefits');
                fieldLabel = 'Quyền lợi';
            } else if (errorLower.includes('ảnh') || errorLower.includes('image')) {
                $element = $('#uploadBox');
                fieldLabel = 'Ảnh đại diện';
            } else if (errorLower.includes('trình độ') || errorLower.includes('degree')) {
                $element = $('select[name="degree"]');
                fieldLabel = 'Trình độ yêu cầu';
            } else {
                for (var key in fieldMap) {
                    if (errorLower.includes(key.toLowerCase())) {
                        $element = fieldMap[key].$element;
                        fieldLabel = fieldMap[key].label;
                        break;
                    }
                }
            }

            if ($element && $element.length) {
                $element.addClass('error-field');

                var $errorMsg = $('<div>')
                    .addClass('field-error-msg')
                    .html('⚠️ ' + error);

                var $parentGroup = $element.closest('.form-group');
                if ($parentGroup.length) {
                    $parentGroup.find('.field-error-msg').remove();
                    if ($element.is('input[type="file"]')) {
                        $element.parent().append($errorMsg);
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

    // ============================================
    // VALIDATE FORM CLIENT-SIDE
    // ============================================

    function validateClientForm($form) {
        var requiredFields = getRequiredFields($form);
        var errors = [];
        var firstErrorElement = null;

        $('.field-error-msg').remove();
        $('.error-field').removeClass('error-field');

        requiredFields.forEach(function(field) {
            var $field = field.$element;
            var value = '';

            if ($field.is('select')) {
                value = $field.val() || '';
            } else if ($field.is('input[type="checkbox"]')) {
                value = $field.is(':checked') ? 'checked' : '';
            } else {
                value = $field.val() || '';
            }

            var isValid = true;
            var errorMessage = '';

            // Kiểm tra theo loại field
            if ($field.attr('name') === 'quantity') {
                var quantity = parseInt(value, 10);
                if (!value || quantity < 1) {
                    isValid = false;
                    errorMessage = 'Số lượng cần tuyển phải lớn hơn 0';
                }
            } else if ($field.attr('id') === 'deadline' || $field.attr('name') === 'deadline') {
                if (!value) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn hạn nộp hồ sơ';
                } else {
                    var selectedDate = new Date(value);
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        isValid = false;
                        errorMessage = 'Hạn nộp hồ sơ không được nhỏ hơn ngày hiện tại';
                    }
                }
            } else if ($field.attr('name') === 'salary_range' || $field.attr('id') === 'salary_range') {
                var salaryValue = $field.val();
                if (!salaryValue || salaryValue.trim() === '') {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập mức lương';
                } else {
                    var numberOnly = salaryValue.replace(/[^0-9]/g, '');
                    if (numberOnly === '') {
                        isValid = false;
                        errorMessage = 'Vui lòng nhập số tiền hợp lệ';
                    } else {
                        var number = parseInt(numberOnly, 10);
                        if (number <= 0) {
                            isValid = false;
                            errorMessage = 'Mức lương phải lớn hơn 0';
                        }
                    }
                }
            } else if ($field.attr('type') === 'number') {
                if (!value || parseInt(value, 10) <= 0) {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập ' + field.label;
                }
            } else {
                if (!value || value.trim() === '') {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập ' + field.label;
                }
            }

            if (!isValid) {
                errors.push({ msg: errorMessage, field: $field });
                $field.addClass('error-field');

                var $errorMsg = $('<div>')
                    .addClass('field-error-msg')
                    .html('⚠️ ' + errorMessage);
                $field.after($errorMsg);

                if (!firstErrorElement) {
                    firstErrorElement = $field;
                }
            }
        });

        // Kiểm tra slug
        var $slug = $form.find('#slug');
        var slug = $slug.val();
        if (slug && !SlugGenerator.isValid(slug)) {
            errors.push({ msg: 'Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)', field: $slug });
            $slug.addClass('error-field');
            if (!firstErrorElement) firstErrorElement = $slug;
        }

        // Kiểm tra ảnh bằng ImagePreview module
        var imageValidation = ImagePreview.validateImage($form, { required: true });
        if (!imageValidation.valid) {
            errors.push({ msg: imageValidation.message, field: $form.find('#uploadBox') });
            $form.find('#uploadBox').addClass('error-field');
            if (!firstErrorElement) firstErrorElement = $form.find('#uploadBox');
        }

        if (errors.length > 0) {
            var errorMessages = errors.map(function(e) { return e.msg; });
            showToast('⚠️ Vui lòng kiểm tra lại:\n• ' + errorMessages.join('\n• '), 'error');

            if (firstErrorElement) {
                scrollToErrorElement(firstErrorElement, 120);
            }
            return false;
        }

        return true;
    }

    // ============================================
    // BIND FORM HANDLERS
    // ============================================

    function bindFormHandlers($form) {
        if (!$form.length || $form.data('recruitment-init')) {
            return;
        }

        $form.data('recruitment-init', true);

        // ============================================
        // SLUG GENERATOR - SỬ DỤNG MODULE ĐÃ TÁCH
        // ============================================
        var slugBinding = SlugGenerator.bindToForm($form, {
            lockEditing: true,
            lockMessage: 'Slug được tạo tự động từ tiêu đề, không thể chỉnh sửa trực tiếp',
            updateOnInput: true,
            autoGenerateInitial: true,
            onUpdate: function(newSlug, oldSlug) {
                // Callback khi slug được cập nhật
                // Có thể thêm logic bổ sung nếu cần
            }
        });

        // ============================================
        // IMAGE PREVIEW - SỬ DỤNG MODULE ĐÃ TÁCH
        // ============================================
        var imagePreview = ImagePreview.init($form);

        // ============================================
        // CÁC HANDLER KHÁC
        // ============================================

        // Khởi tạo deadline validation
        initDeadlineValidation($form);

        // Khởi tạo salary field
        initSalaryField($form);

        // Xóa lỗi khi người dùng nhập vào các field
        $form.find('input, textarea, select').on('input change', function() {
            $(this).removeClass('error-field');
            $(this).closest('.form-group').find('.field-error-msg').remove();

            if ($(this).attr('id') === 'deadline') {
                $(this).closest('.form-group').find('.deadline-warning').remove();
            }
        });

        // Xử lý submit form
        $form.on('submit', function(e) {
            // Update slug trước khi submit
            if (slugBinding) {
                slugBinding.updateSlug();
            }

            if (!validateClientForm($form)) {
                e.preventDefault();
            }
        });
    }

    // ============================================
    // INIT FUNCTION
    // ============================================

    function initRecruitmentForm(scope, serverErrors) {
        scope = scope || document;
        var $scope = scope instanceof jQuery ? scope : $(scope);
        var $form = $scope.find('#recruitmentForm');

        if (!$form.length) return;

        bindFormHandlers($form);

        // Hiển thị lỗi từ server
        if (serverErrors && serverErrors.length > 0) {
            displayServerErrors(serverErrors, $form);
        }
    }

    // ============================================
    // EXPOSE GLOBAL FUNCTIONS
    // ============================================

    window.recruitmentForm = {
        init: initRecruitmentForm,

        generateAITitle: function() {
            var $form = $('#recruitmentForm');
            var $title = $form.find('#recruitment_title');

            if (!$form.length || !$title.length) {
                return;
            }

            $title.val(aiTitles[Math.floor(Math.random() * aiTitles.length)]);
            // Update slug sau khi thay đổi title
            SlugGenerator.autoUpdate($title, $form.find('#slug'));
            $title.removeClass('error-field');
            $title.closest('.form-group').find('.field-error-msg').remove();
            showToast('Đã tạo gợi ý tiêu đề!', 'success');
        },

        generateAIDescription: function() {
            var $form = $('#recruitmentForm');
            var $description = $form.find('#job_description');

            if (!$form.length || !$description.length) {
                return;
            }

            $description.val(aiDescription);
            $description.removeClass('error-field');
            $description.closest('.form-group').find('.field-error-msg').remove();
            showToast('Đã tạo gợi ý mô tả công việc!', 'success');
        },

        generateAIRequirements: function() {
            var $form = $('#recruitmentForm');
            var $requirements = $form.find('#job_requirements');

            if (!$form.length || !$requirements.length) {
                return;
            }

            $requirements.val(aiRequirements);
            $requirements.removeClass('error-field');
            $requirements.closest('.form-group').find('.field-error-msg').remove();
            showToast('Đã tạo gợi ý yêu cầu ứng viên!', 'success');
        },

        generateAIBenefits: function() {
            var $form = $('#recruitmentForm');
            var $benefits = $form.find('#job_benefits');

            if (!$form.length || !$benefits.length) {
                return;
            }

            $benefits.val(aiBenefits);
            $benefits.removeClass('error-field');
            $benefits.closest('.form-group').find('.field-error-msg').remove();
            showToast('Đã tạo gợi ý quyền lợi!', 'success');
        },

        generateAIImage: function() {
            showToast('Tính năng tạo ảnh bằng AI đang phát triển!', 'info');
        }
    };

    // ============================================
    // AUTO INIT ON DOM READY
    // ============================================

    $(function() {
        var serverErrors = window.serverErrors || null;
        window.recruitmentForm.init(document, serverErrors);
    });

})(jQuery);

