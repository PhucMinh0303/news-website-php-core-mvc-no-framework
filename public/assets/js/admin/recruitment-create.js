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

  const aiDescription = 'Thực hiện các công việc chuyên môn theo đúng quy trình của công ty\nPhối hợp với các phòng ban để đảm bảo tiến độ công việc\nBáo cáo kết quả công việc định kỳ cho cấp trên trực tiếp\nTham gia các dự án theo sự phân công của quản lý\nĐề xuất các giải pháp cải thiện quy trình làm việc\n';

  const aiRequirements = 'Tốt nghiệp Cao đẳng / Đại học chuyên ngành phù hợp\nCó ít nhất 1-2 năm kinh nghiệm trong lĩnh vực tương tự\nThành thạo các công cụ văn phòng (Word, Excel, PowerPoint)\nKỹ năng giao tiếp, làm việc nhóm tốt\nChủ động, sáng tạo và có tinh thần trách nhiệm cao\n';

  const aiBenefits = 'Lương cạnh tranh + thưởng hiệu quả công việc\nĐầy đủ BHXH, BHYT, BHTN theo quy định\nMôi trường làm việc năng động, thân thiện\nCơ hội thăng tiến và đào tạo chuyên sâu\nCác hoạt động team building, du lịch hàng năm\n';

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
  // Tạo slug từ tiêu đề, loại bỏ dấu tiếng Việt, chuyển thành chữ thường, thay khoảng trắng bằng dấu gạch ngang, và loại bỏ ký tự đặc biệt
  function generateSlugFromTitle(title) {
    if (!title || title.trim() === '') {
      return '';
    }

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

  // Validation cho deadline field
  function initDeadlineValidation($form) {
    const $deadline = $form.find('#deadline');
    
    if (!$deadline.length || $deadline.data('deadline-bound')) {
      return;
    }
    
    $deadline.data('deadline-bound', true);
    
    // Hàm kiểm tra deadline
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
      
      // Kiểm tra nếu ngày chọn nhỏ hơn ngày hiện tại
      if (selectedDate < today) {
        // Tạo hoặc cập nhật message warning
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
        // Xóa warning nếu có
        if ($warningMsg.length) {
          $warningMsg.remove();
        }
        $deadline.removeClass('error-field');
        return true;
      }
    }
    
    // Lắng nghe sự kiện change và input
    $deadline.on('change input', function() {
      validateDeadline();
      
      // Xóa custom validity của browser
      this.setCustomValidity('');
      
      // Xóa error message cũ nếu có
      $(this).closest('.form-group').find('.field-error-msg').remove();
      $(this).removeClass('error-field');
    });
    
    // Trả về hàm validate để sử dụng trong form submit
    return validateDeadline;
  }

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
      'salary': { $element: $('#salary_display'), label: 'Mức lương' },
      'benefits': { $element: $('#job_benefits'), label: 'Quyền lợi' },
      'degree': { $element: $('select[name="degree"]'), label: 'Trình độ' },
      'work_type': { $element: $('select[name="work_type"]'), label: 'Hình thức làm việc' },
      'status': { $element: $('select[name="status"]'), label: 'Trạng thái' }
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
      } else if (errorLower.includes('slug')) {
        $element = $('#slug');
        fieldLabel = 'Slug';
      } else if (errorLower.includes('lương') || errorLower.includes('salary')) {
        $element = $('#salary_display');
        fieldLabel = 'Mức lương';
      } else if (errorLower.includes('quyền lợi') || errorLower.includes('benefits')) {
        $element = $('#job_benefits');
        fieldLabel = 'Quyền lợi';
      } else if (errorLower.includes('ảnh') || errorLower.includes('image')) {
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
          // Xóa thông báo cũ trong group này
          $parentGroup.find('.field-error-msg').remove();
          // Thêm thông báo mới
          if ($element.is('input[type="file"]')) {
            $element.parent().append($errorMsg);
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
          // Kiểm tra ngày không được nhỏ hơn ngày hiện tại
          const selectedDate = new Date(value);
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          
          if (selectedDate < today) {
            isValid = false;
            errorMessage = 'Hạn nộp hồ sơ không được nhỏ hơn ngày hiện tại';
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
  
  // Preview ảnh khi người dùng chọn file, đồng thời validate kích thước ảnh không vượt quá 2MB
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
      // Xóa lỗi nếu có
      $uploadBox.removeClass('error-field');
      $uploadBox.closest('.form-group').find('.field-error-msg').remove();
    };

    reader.readAsDataURL(input.files[0]);
  }

  function initSalaryHandler($form) {

    const $salaryDisplay = $form.find('#salary_display');
    const $salaryValue = $form.find('#salary_value');
    const $salaryError = $form.find('#salary_error');

    if (
      !$salaryDisplay.length ||
      $salaryDisplay.data('recruitment-salary-bound')
    ) {
      return;
    }

    $salaryDisplay.data(
      'recruitment-salary-bound',
      true
    );

    $salaryDisplay.on('input', function () {

      const originalValue = $(this).val();
      const $this = $(this);

      // Báo lỗi nếu nhập chữ
      if (/[a-zA-Z]/.test(originalValue)) {

        $salaryError.text(
          'Chỉ nhập số, không nhập chữ'
        );

        return;

      } else {

        $salaryError.text('');

      }

      // Chỉ giữ lại số
      let numbers = originalValue.replace(/\D/g, '');

      if (!numbers) {

        $this.val('');
        $salaryValue.val('');

        return;
      }

      /*
        Thêm 000 phía sau
        1 -> 1000
        10 -> 10000
        100 -> 100000
        1000 -> 1000000
      */

      const actualSalary =
        parseInt(numbers, 10) * 1000;

      // Format kiểu VN
      const formattedSalary =
        actualSalary.toLocaleString('vi-VN');

      // Hiển thị
        $this.val(`${formattedSalary} VND`);

      // Hidden lưu số sạch
      $salaryValue.val(actualSalary);

    });
    // Chống lỗi click lại cuối input
    $salaryDisplay.on('focus', function () {

        let value = $(this).val();

        value = value.replace(/\s*VND$/i, '');

        $(this).val(value);

    });
    // Rời input tự thêm VND lại
    $salaryDisplay.on('blur', function () {

        let value = $(this).val();

        if (!value) {
            return;
        }

        if (!/VND$/i.test(value)) {
            $(this).val(value + ' VND');
        }

    });

  }

  function bindFormHandlers($form) {
    if (!$form.length || $form.data('recruitment-init')) {
      return;
    }

    $form.data('recruitment-init', true);

    const $title = $form.find('#recruitment_title');
    const $slug = $form.find('#slug');

    $title.on('input', function() {
      updateSlug($form);
      $(this).removeClass('error-field');
      $(this).closest('.form-group').find('.field-error-msg').remove();
    });

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

    // Xóa lỗi khi người dùng nhập vào các field required
    $form.find('input, textarea, select').on('input change', function() {
      $(this).removeClass('error-field');
      $(this).closest('.form-group').find('.field-error-msg').remove();
      
      // Xóa deadline warning riêng nếu có
      if ($(this).attr('id') === 'deadline') {
        $(this).closest('.form-group').find('.deadline-warning').remove();
      }
    });
    // Xử lý submit form
    $form.on('submit', function(e) {
      // Cập nhật slug lần cuối trước khi submit
      updateSlug($form);
      
      // Validate client trước khi submit
      if (!validateClientForm($form)) {
        e.preventDefault();
      }
    });

    $form.find('#uploadBox').on('click', function() {
      $form.find('#imageInput').trigger('click');
    });

    $form.find('#imageInput').on('change', function() {
      previewImage(this, $form);
      $(this).removeClass('error-field');
      $(this).closest('.form-group').find('.field-error-msg').remove();
    });

    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    if (initialTitle && initialTitle.trim() !== '' && (!initialSlug || initialSlug.trim() === '')) {
      updateSlug($form);
    }

    initSalaryHandler($form);
    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
  }

  function initRecruitmentForm(scope = document, serverErrors = null) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#recruitmentForm');

    if (!$form.length) return;
    
    bindFormHandlers($form);
    
    // Hiển thị lỗi từ server (PHP session)
    if (serverErrors && serverErrors.length > 0) {
      displayServerErrors(serverErrors, $form);
    }
  }

  window.regenerateSlug = function() {
    const $form = $('#recruitmentForm');

    if (!$form.length) {
      return;
    }

    const $title = $form.find('#recruitment_title');
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

  $(function() {
    // Lấy errors từ PHP session (đã được in ra HTML dưới dạng biến JS)
    const serverErrors = window.serverErrors || null;
    window.recruitmentForm.init(document, serverErrors);
  });
})(jQuery);