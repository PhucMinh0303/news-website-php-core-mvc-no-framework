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

  const aiDescription = '<p><strong>Mô tả công việc:</strong></p>\n<ul>\n    <li>Thực hiện các công việc chuyên môn theo đúng quy trình của công ty</li>\n    <li>Phối hợp với các phòng ban để đảm bảo tiến độ công việc</li>\n    <li>Báo cáo kết quả công việc định kỳ cho cấp trên trực tiếp</li>\n    <li>Tham gia các dự án theo sự phân công của quản lý</li>\n    <li>Đề xuất các giải pháp cải thiện quy trình làm việc</li>\n</ul>';

  const aiRequirements = '<p><strong>Yêu cầu ứng viên:</strong></p>\n<ul>\n    <li>Tốt nghiệp Cao đẳng / Đại học chuyên ngành phù hợp</li>\n    <li>Có ít nhất 1-2 năm kinh nghiệm trong lĩnh vực tương tự</li>\n    <li>Thành thạo các công cụ văn phòng (Word, Excel, PowerPoint)</li>\n    <li>Kỹ năng giao tiếp, làm việc nhóm tốt</li>\n    <li>Chủ động, sáng tạo và có tinh thần trách nhiệm cao</li>\n</ul>';

  const aiBenefits = '<p><strong>Quyền lợi được hưởng:</strong></p>\n<ul>\n    <li>Lương cạnh tranh + thưởng hiệu quả công việc</li>\n    <li>Đầy đủ BHXH, BHYT, BHTN theo quy định</li>\n    <li>Môi trường làm việc năng động, thân thiện</li>\n    <li>Cơ hội thăng tiến và đào tạo chuyên sâu</li>\n    <li>Các hoạt động team building, du lịch hàng năm</li>\n</ul>';

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

    let slug = removeVietnameseTones(title)
      .toLowerCase()
      .replace(/[^\w\s]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 100);

    return slug;
  }

  function isValidSlug(slug) {
    if (!slug || slug.trim() === '') {
      return false;
    }

    return /^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug);
  }

  function showToast(message, type) {
    $('.toast').remove();

    const toast = $('<div>')
      .addClass(`toast toast-${type}`)
      .html(message)
      .hide();

    $('body').append(toast);
    toast.fadeIn(300);

    setTimeout(() => {
      toast.fadeOut(300, function() {
        $(this).remove();
      });
    }, 3000);
  }

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

    if (newSlug === oldSlug) {
      return;
    }

    $slug.val(newSlug);

    if ($slugOriginal.length) {
      $slugOriginal.val(newSlug);
    }

    $slug.css({
      backgroundColor: '#fef3c7',
      transition: 'all 0.3s ease'
    });

    setTimeout(() => {
      $slug.css('backgroundColor', '#f3f4f6');
    }, 500);
  }

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
    };

    reader.readAsDataURL(input.files[0]);
  }

  function initSalaryHandler($form) {
    const $salaryDisplay = $form.find('#salary_display');
    const $salaryValue = $form.find('#salary_value');
    const $salaryError = $form.find('#salary_error');

    if (!$salaryDisplay.length || $salaryDisplay.data('recruitment-salary-bound')) {
      return;
    }

    $salaryDisplay.data('recruitment-salary-bound', true);

    $salaryDisplay.on('input', function() {
      const originalValue = $(this).val();
      const $this = $(this);

      if (/[a-zA-Z]/.test(originalValue)) {
        $salaryError.text('Chỉ nhập số, không nhập chữ');
      } else {
        $salaryError.text('');
      }

      const numbers = originalValue.replace(/\D/g, '');

      if (numbers === '') {
        $this.val('');
        $salaryValue.val('');
        return;
      }

      const salary = parseInt(numbers, 10) * 1000000;

      $this.val(salary.toLocaleString('vi-VN'));
      $salaryValue.val(salary);
    });
  }

  function validateForm($form) {
    const $title = $form.find('#recruitment_title');
    const $slug = $form.find('#slug');
    const $workLocation = $form.find('textarea[name="work_location"]');
    const $quantity = $form.find('input[name="quantity"]');
    const $deadline = $form.find('input[name="deadline"]');

    const title = $title.val().trim();
    const slug = $slug.val().trim();
    const workLocation = $workLocation.val().trim();
    const quantity = $quantity.val();
    const deadline = $deadline.val();
    const errors = [];

    if (!title) {
      errors.push('Vui lòng nhập tiêu đề tin tuyển dụng');
      $title.trigger('focus');
    }

    if (!slug) {
      errors.push('Slug không được để trống');
    } else if (!isValidSlug(slug)) {
      errors.push('Slug không hợp lệ (chỉ chứa chữ thường, số và dấu gạch ngang)');
    }

    if (!workLocation) {
      errors.push('Vui lòng nhập địa điểm làm việc');
    }

    if (!quantity || parseInt(quantity, 10) < 1) {
      errors.push('Số lượng cần tuyển phải lớn hơn 0');
    }

    if (!deadline) {
      errors.push('Vui lòng chọn hạn nộp hồ sơ');
    }

    if (errors.length > 0) {
      showToast('• ' + errors.join('\n• '), 'error');
      return false;
    }

    return true;
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
    });

    $slug.on('copy cut paste', function(e) {
      e.preventDefault();
      showToast('Slug không thể chỉnh sửa trực tiếp!', 'error');
      return false;
    });

    $slug.on('drag drop', function(e) {
      e.preventDefault();
      return false;
    });

    $slug.on('keydown', function(e) {
      e.preventDefault();
      showToast('Slug được tạo tự động từ tiêu đề!', 'info');
      return false;
    });

    $slug.on('contextmenu', function(e) {
      e.preventDefault();
      return false;
    });

    $form.on('submit', function(e) {
      updateSlug($form);

      if (!validateForm($form)) {
        e.preventDefault();
      }
    });

    $form.find('#uploadBox').on('click', function() {
      $form.find('#imageInput').trigger('click');
    });

    $form.find('#imageInput').on('change', function() {
      previewImage(this, $form);
    });

    const initialTitle = $title.val();
    const initialSlug = $slug.val();

    if (initialTitle && initialTitle.trim() !== '' && (!initialSlug || initialSlug.trim() === '')) {
      updateSlug($form);
    }

    initSalaryHandler($form);
    $slug.attr('title', 'Slug được tự động tạo từ tiêu đề, không thể chỉnh sửa trực tiếp');
  }

  function initRecruitmentForm(scope = document) {
    const $scope = scope instanceof jQuery ? scope : $(scope);
    const $form = $scope.find('#recruitmentForm');

    bindFormHandlers($form);
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
      $title.trigger('focus');
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

  window.previewImage = function(input) {
    const $form = $(input).closest('form');
    previewImage(input, $form);
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
      showToast('Đã tạo gợi ý tiêu đề!', 'success');
    },

    generateAIDescription: function() {
      const $form = $('#recruitmentForm');
      const $description = $form.find('#job_description');

      if (!$form.length || !$description.length) {
        return;
      }

      $description.val(aiDescription);
      showToast('Đã tạo gợi ý mô tả công việc!', 'success');
    },

    generateAIRequirements: function() {
      const $form = $('#recruitmentForm');
      const $requirements = $form.find('#job_requirements');

      if (!$form.length || !$requirements.length) {
        return;
      }

      $requirements.val(aiRequirements);
      showToast('Đã tạo gợi ý yêu cầu ứng viên!', 'success');
    },

    generateAIBenefits: function() {
      const $form = $('#recruitmentForm');
      const $benefits = $form.find('#job_benefits');

      if (!$form.length || !$benefits.length) {
        return;
      }

      $benefits.val(aiBenefits);
      showToast('Đã tạo gợi ý quyền lợi!', 'success');
    },

    generateAIImage: function() {
      showToast('Tính năng tạo ảnh bằng AI đang phát triển!', 'info');
    }
  };

  $(function() {
    window.recruitmentForm.init(document);
  });
})(jQuery);
