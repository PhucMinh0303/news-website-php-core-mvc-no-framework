(function($) {
  'use strict';

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

  function generateSlugFromTitle(title) {
    if (!title || title.trim() === '') {
      return '';
    }
    // Tạo slug từ tiêu đề, loại bỏ dấu tiếng Việt, chuyển thành chữ thường, thay khoảng trắng bằng dấu gạch ngang, và loại bỏ ký tự đặc biệt
    let slug = removeVietnameseTones(title)
      .toLowerCase()
      .replace(/[^\w\s]/g, '')
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
  // Cập nhật slug tự động khi người dùng nhập tiêu đề
  function updateSlug($form) {
    const $title = $form.find('#recruitment_title');
    const $slug = $form.find('#slug');

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

    // Hiệu ứng highlight khi cập nhật
    $slug.css({
      backgroundColor: '#fef3c7',
      transition: 'all 0.3s ease'
    });

    setTimeout(() => {
      $slug.css('backgroundColor', '#f3f4f6');
    }, 500);
  }
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

  // ============================================
  // HÀM LẤY CÁC FIELD REQUIRED
  // ============================================

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

  // ============================================
  // VALIDATE DEADLINE
  // ============================================

  function initDeadlineValidation($form) {
    const $deadline = $form.find('#deadline');
    
    if (!$deadline.length || $deadline.data('deadline-bound')) {
      return;
    }
    
    $deadline.data('deadline-bound', true);
    
    function validateDeadline() {
      const deadlineValue = $deadline.val();
      const $warningMsg = $deadline.closest('.form-group').find('.deadline-warning');
      
      if (!deadlineValue) {
        if ($warningMsg.length) {
          $warningMsg.remove();
        }
        $deadline.removeClass('error-field');
        return true;
      }
      
      const selectedDate = new Date(deadlineValue);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate < today) {
        if (!$warningMsg.length) {
          const $newWarning = $('<small>')
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
  // XỬ LÝ MỨC LƯƠNG - FORMAT VND
  // ============================================

  function formatSalaryWithVND(input) {
    if (!input) return '';
    
    // Loại bỏ tất cả ký tự không phải số
    let numberStr = input.replace(/[^0-9]/g, '');
    
    if (numberStr === '') {
      return '';
    }
    
    // Chuyển thành số và định dạng với dấu chấm
    let number = parseInt(numberStr, 10);
    let formatted = number.toLocaleString('vi-VN');
    
    // Thêm VND
    return formatted + ' VND';
  }

  function extractSalaryNumber(formattedValue) {
    if (!formattedValue) return '';
    // Loại bỏ 'VND' và dấu chấm, giữ lại số
    return formattedValue.replace(/ VND$/, '').replace(/\./g, '');
  }

  // Khởi tạo xử lý mức lương
  function initSalaryField($form) {
    const $salaryInput = $form.find('#salary_range');
    const $salaryHidden = $form.find('#salary_value');
    const $salaryError = $form.find('#salary_error');
    
    if (!$salaryInput.length || $salaryInput.data('salary-bound')) {
      return;
    }
    
    $salaryInput.data('salary-bound', true);
    
    // Lưu giá trị gốc khi focus
    let previousValue = '';
    
    $salaryInput.on('focus', function() {
      // Lưu giá trị hiện tại khi focus vào
      previousValue = $(this).val();
    });
    
    $salaryInput.on('input', function() {
      let rawValue = $(this).val();
      let cursorPosition = this.selectionStart;
      
      // Nếu người dùng nhập chữ "VND" hoặc các ký tự không phải số
      // thì chỉ giữ lại số
      let numberOnly = rawValue.replace(/[^0-9]/g, '');
      
      // Nếu không có số, hiển thị trống
      if (numberOnly === '') {
        $(this).val('');
        $salaryHidden.val('');
        $salaryError.text('');
        $salaryError.hide();
        return;
      }
      
      // Định dạng số với dấu chấm và VND
      let number = parseInt(numberOnly, 10);
      let formatted = number.toLocaleString('vi-VN') + ' VND';
      
      // Cập nhật giá trị
      $(this).val(formatted);
      $salaryHidden.val(number);
      
      // Tính toán lại vị trí cursor
      // Đặt cursor vào cuối text
      this.setSelectionRange(formatted.length, formatted.length);
      
      // Ẩn lỗi khi đang nhập hợp lệ
      $salaryError.text('');
      $salaryError.hide();
      $(this).removeClass('error-field');
      $(this).closest('.form-group').find('.field-error-msg').remove();
    });
    
    // Xử lý khi blur (rời khỏi input)
    $salaryInput.on('blur', function() {
      let value = $(this).val();
      
      // Nếu trống hoặc chỉ có khoảng trắng
      if (!value || value.trim() === '') {
        $(this).val('');
        $salaryHidden.val('');
        return;
      }
      
      // Kiểm tra xem đã có VND chưa
      if (!value.includes('VND')) {
        // Nếu chưa có VND, thử lấy số từ giá trị
        let numberOnly = value.replace(/[^0-9]/g, '');
        if (numberOnly !== '') {
          let number = parseInt(numberOnly, 10);
          let formatted = number.toLocaleString('vi-VN') + ' VND';
          $(this).val(formatted);
          $salaryHidden.val(number);
        }
      }
      
      // Nếu có VND nhưng không có số
      if (value.includes('VND')) {
        let numberOnly = value.replace(/[^0-9]/g, '');
        if (numberOnly === '') {
          $(this).val('');
          $salaryHidden.val('');
        }
      }
    });
    
    // Xử lý khi nhấn phím Enter hoặc Tab
    $salaryInput.on('keydown', function(e) {
      if (e.key === 'Enter' || e.key === 'Tab') {
        $(this).trigger('blur');
      }
    });
    
    // Validate mức lương
    function validateSalary() {
      const value = $salaryInput.val();
      if (!value || value.trim() === '') {
        $salaryError.text('Vui lòng nhập mức lương');
        $salaryError.show();
        $salaryInput.addClass('error-field');
        return false;
      }
      
      // Kiểm tra xem có chứa số không
      let numberOnly = value.replace(/[^0-9]/g, '');
      if (numberOnly === '') {
        $salaryError.text('Vui lòng nhập số tiền hợp lệ');
        $salaryError.show();
        $salaryInput.addClass('error-field');
        return false;
      }
      
      let number = parseInt(numberOnly, 10);
      if (number <= 0) {
        $salaryError.text('Mức lương phải lớn hơn 0');
        $salaryError.show();
        $salaryInput.addClass('error-field');
        return false;
      }
      
      $salaryError.text('');
      $salaryError.hide();
      $salaryInput.removeClass('error-field');
      return true;
    }
    
    // Validate khi submit form
    $form.on('submit', function(e) {
      // Đảm bảo format đúng trước khi submit
      let value = $salaryInput.val();
      if (value && value.trim() !== '') {
        let numberOnly = value.replace(/[^0-9]/g, '');
        if (numberOnly !== '') {
          let number = parseInt(numberOnly, 10);
          let formatted = number.toLocaleString('vi-VN') + ' VND';
          $salaryInput.val(formatted);
          $salaryHidden.val(number);
        }
      }
    });
    
    // Thêm hàm validate vào form để sử dụng trong validation chung
    $salaryInput.data('validate-function', validateSalary);
    
    // Xử lý giá trị ban đầu nếu có
    const initialValue = $salaryInput.val();
    if (initialValue && initialValue.trim() !== '') {
      // Nếu là số nguyên, format lại
      let numberOnly = initialValue.replace(/[^0-9]/g, '');
      if (numberOnly !== '') {
        let number = parseInt(numberOnly, 10);
        let formatted = number.toLocaleString('vi-VN') + ' VND';
        $salaryInput.val(formatted);
        $salaryHidden.val(number);
      }
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
    
    // Thêm các field không có required nhưng vẫn có thể có lỗi
    const allFieldsMap = {
      'slug': { $element: $('#slug'), label: 'Slug' },
      'salary_range': { $element: $('#salary_range'), label: 'Mức lương' },
      'benefits': { $element: $('#job_benefits'), label: 'Quyền lợi' },
      'degree': { $element: $('select[name="degree"]'), label: 'Trình độ' },
      'work_type': { $element: $('select[name="work_type"]'), label: 'Hình thức làm việc' },
      'status': { $element: $('select[name="status"]'), label: 'Trạng thái' }
    };
    
    Object.assign(fieldMap, allFieldsMap);
    
    errors.forEach(error => {
      errorList.push(error);
      
      let $element = null;
      let fieldLabel = '';
      
      const errorLower = error.toLowerCase();
      
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
    const requiredFields = getRequiredFields($form);
    const errors = [];
    let firstErrorElement = null;
    
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
      if ($field.attr('name') === 'quantity') {
        const quantity = parseInt(value, 10);
        if (!value || quantity < 1) {
          isValid = false;
          errorMessage = 'Số lượng cần tuyển phải lớn hơn 0';
        }
      } else if ($field.attr('id') === 'deadline' || $field.attr('name') === 'deadline') {
        if (!value) {
          isValid = false;
          errorMessage = 'Vui lòng chọn hạn nộp hồ sơ';
        } else {
          const selectedDate = new Date(value);
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          
          if (selectedDate < today) {
            isValid = false;
            errorMessage = 'Hạn nộp hồ sơ không được nhỏ hơn ngày hiện tại';
          }
        }
      } else if ($field.attr('name') === 'salary_range' || $field.attr('id') === 'salary_range') {
        // Kiểm tra mức lương
        const salaryValue = $field.val();
        if (!salaryValue || salaryValue.trim() === '') {
          isValid = false;
          errorMessage = 'Vui lòng nhập mức lương';
        } else {
          let numberOnly = salaryValue.replace(/[^0-9]/g, '');
          if (numberOnly === '') {
            isValid = false;
            errorMessage = 'Vui lòng nhập số tiền hợp lệ';
          } else {
            let number = parseInt(numberOnly, 10);
            if (number <= 0) {
              isValid = false;
              errorMessage = 'Mức lương phải lớn hơn 0';
            }
          }
        }
      } else if ($field.attr('type') === 'number') {
        if (!value || parseInt(value, 10) <= 0) {
          isValid = false;
          errorMessage = `Vui lòng nhập ${field.label}`;
        }
      } else {
        if (!value || value.trim() === '') {
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
        $field.after($errorMsg);
        
        if (!firstErrorElement) {
          firstErrorElement = $field;
        }
      }
    });
    
    // Kiểm tra slug
    const $slug = $form.find('#slug');
    const slug = $slug.val();
    if (slug && !isValidSlug(slug)) {
      errors.push({ msg: 'Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)', field: $slug });
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

  
  // ============================================
  // PREVIEW ẢNH
  // ============================================

  function previewImage(input, $form) {
    const $preview = $form.find('#imagePreview');
    const $previewImg = $form.find('#previewImg');
    const $uploadBox = $form.find('#uploadBox');
    const $uploadText = $form.find('#uploadText');
    const $uploadInfo = $form.find('#uploadInfo');

    if (!input.files || !input.files[0]) {
      return;
    }

    if (input.files[0].size > 2 * 1024 * 1024) {
      showToast('Ảnh không được vượt quá 2MB!', 'error');
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


  // ============================================
  // BIND FORM HANDLERS
  // ============================================

  function bindFormHandlers($form) {
    if (!$form.length || $form.data('recruitment-init')) {
      return;
    }

    $form.data('recruitment-init', true);

    const $title = $form.find('#recruitment_title');

    // Tự động tạo slug khi nhập title
    $title.on('input', function() {
      updateSlug($form);
      $(this).removeClass('error-field');
      $(this).closest('.form-group').find('.field-error-msg').remove();
    });

    // Khóa chỉnh sửa slug
    const $slug = $form.find('#slug');
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
      updateSlug($form);
      
      // Format salary trước khi submit
      const $salaryInput = $form.find('#salary_range');
      const $salaryHidden = $form.find('#salary_value');
      if ($salaryInput.length) {
        const value = $salaryInput.val();
        if (value && value.trim() !== '') {
          let numberOnly = value.replace(/[^0-9]/g, '');
          if (numberOnly !== '') {
            let number = parseInt(numberOnly, 10);
            let formatted = number.toLocaleString('vi-VN') + ' VND';
            $salaryInput.val(formatted);
            $salaryHidden.val(number);
          }
        }
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
    });

    // Tạo slug ban đầu nếu có title
    const initialTitle = $title.val();
    if (initialTitle && initialTitle.trim() !== '') {
      updateSlug($form);
    }

    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
  }

  // ============================================
  // INIT FUNCTION
  // ============================================

  function initRecruitmentForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#recruitmentForm');

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
      const $form = $('#recruitmentForm');
      const $title = $form.find('#recruitment_title');

      if (!$form.length || !$title.length) {
        return;
      }

      $title.val(aiTitles[Math.floor(Math.random() * aiTitles.length)]);
      updateSlug($form);
      $title.removeClass('error-field');
      $title.closest('.form-group').find('.field-error-msg').remove();
      showToast('Đã tạo gợi ý tiêu đề!', 'success');
    },

    generateAIDescription: function() {
      const $form = $('#recruitmentForm');
      const $description = $form.find('#job_description');

      if (!$form.length || !$description.length) {
        return;
      }

      $description.val(aiDescription);
      $description.removeClass('error-field');
      $description.closest('.form-group').find('.field-error-msg').remove();
      showToast('Đã tạo gợi ý mô tả công việc!', 'success');
    },

    generateAIRequirements: function() {
      const $form = $('#recruitmentForm');
      const $requirements = $form.find('#job_requirements');

      if (!$form.length || !$requirements.length) {
        return;
      }

      $requirements.val(aiRequirements);
      $requirements.removeClass('error-field');
      $requirements.closest('.form-group').find('.field-error-msg').remove();
      showToast('Đã tạo gợi ý yêu cầu ứng viên!', 'success');
    },

    generateAIBenefits: function() {
      const $form = $('#recruitmentForm');
      const $benefits = $form.find('#job_benefits');

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
    const serverErrors = window.serverErrors || null;
    window.recruitmentForm.init(document, serverErrors);
  });

})(jQuery);