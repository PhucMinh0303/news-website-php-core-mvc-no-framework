window.applicationAdmin = {
  init: function (container) {
    const root = container.querySelector('#applicationAdmin');
    if (!root) return;

    const dataElement = root.querySelector('#adminApplicationsData');
    const applications = JSON.parse(dataElement ? dataElement.textContent : '[]');
    const statusLabels = {
      pending: 'Chờ xem xét',
      reviewed: 'Đã xem xét',
      interviewed: 'Phỏng vấn',
      accepted: 'Đã nhận',
      rejected: 'Từ chối',
      archived: 'Lưu trữ',
      deleted: 'Đã xóa',
    };
    const applicationsById = new Map(applications.map(function (application) {
      return [Number(application.id), application];
    }));
    const rows = Array.from(root.querySelectorAll('.message-item'));
    const emptyPanel = root.querySelector('#applicationEmptyPanel');
    const detailPanel = root.querySelector('#applicationDetailPanel');
    const searchInput = root.querySelector('#applicationSearchInput');
    const archiveBtn = root.querySelector('#archiveMsgBtn');
    const deleteBtn = root.querySelector('#deleteMsgBtn');
    const statusUrl = root.dataset.statusUrl;
    let activeTab = (root.querySelector('#tabsContainer .tab.active') || {}).dataset?.tab || 'inbox';

    function tabForStatus(status) {
      return status === 'deleted' ? 'deleted' : (status === 'archived' ? 'archive' : 'inbox');
    }

    function selectApplication(id) {
      const application = applicationsById.get(Number(id));
      if (!application) return;

      rows.forEach(function (row) {
        row.classList.toggle('active', Number(row.dataset.applicationId) === Number(id));
      });

      root.querySelector('#applicationJobTitle').textContent = application.recruitment_title || 'Tin tuyển dụng không còn tồn tại';
      root.querySelector('#applicationName').textContent = application.full_name || '';
      root.querySelector('#applicationEmail').textContent = application.email || '';
      root.querySelector('#applicationPhone').textContent = application.phone || '';
      root.querySelector('#applicationStatus').textContent = 'Trạng thái: ' + (statusLabels[application.status] || application.status || 'pending');
      root.querySelector('#applicationContent').textContent = application.content || '';
      root.querySelector('#applicationNotes').textContent = application.notes || 'Chưa có ghi chú.';
      root.querySelector('#applicationId').textContent = application.id;
      root.querySelector('#applicationDate').textContent = application.created_at || '';

      const cvLink = root.querySelector('#applicationCvLink');
      if (application.cv_url) {
        cvLink.href = application.cv_url;
        cvLink.style.display = '';
      } else {
        cvLink.removeAttribute('href');
        cvLink.style.display = 'none';
      }

      emptyPanel.style.display = 'none';
      detailPanel.style.display = 'flex';
    }

    function updateCounts() {
      ['inbox', 'archive', 'deleted'].forEach(function (tab) {
        root.querySelector('#' + tab + 'Count').textContent = rows.filter(function (row) {
          return row.dataset.tab === tab;
        }).length;
      });
      const pending = applications.filter(function (application) {
        return application.status === 'pending';
      }).length;
      root.querySelector('#newMessagesBadge').textContent = pending + ' hồ sơ mới';
    }

    function showEmpty(hasRowsInTab) {
      detailPanel.style.display = 'none';
      emptyPanel.textContent = hasRowsInTab
        ? 'Không tìm thấy hồ sơ phù hợp.'
        : (activeTab === 'archive' ? 'Chưa có hồ sơ lưu trữ.' : (activeTab === 'deleted' ? 'Thùng rác trống.' : 'Chưa có hồ sơ ứng tuyển.'));
      emptyPanel.style.display = 'flex';
    }

    function renderList(preferredRow) {
      const query = searchInput.value.trim().toLocaleLowerCase('vi');
      const rowsInTab = rows.filter(function (row) { return row.dataset.tab === activeTab; });
      const visibleRows = [];

      rows.forEach(function (row) {
        const visible = row.dataset.tab === activeTab && row.textContent.toLocaleLowerCase('vi').includes(query);
        row.style.display = visible ? '' : 'none';
        row.classList.remove('active');
        if (visible) visibleRows.push(row);
      });

      root.querySelectorAll('#tabsContainer .tab').forEach(function (tab) {
        const isActive = tab.dataset.tab === activeTab;
        tab.classList.toggle('active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      const isArchiveTab = activeTab === 'archive';
      archiveBtn.title = isArchiveTab ? 'quay về thư mục' : 'Lưu trữ';
      const icon = archiveBtn.querySelector('i');
      icon.classList.toggle('fa-reply', isArchiveTab);
      icon.classList.toggle('fa-box-archive', !isArchiveTab);

      const row = visibleRows.includes(preferredRow) ? preferredRow : visibleRows[0];
      if (row) {
        selectApplication(row.dataset.applicationId);
      } else {
        showEmpty(rowsInTab.length > 0);
      }
    }

    function moveActiveApplication(status) {
      const row = rows.find(function (item) { return item.classList.contains('active') && item.style.display !== 'none'; });
      if (!row) return;

      const id = Number(row.dataset.applicationId);
      const application = applicationsById.get(id);
      if (!application) return;

      const apply = function (newStatus) {
        application.status = newStatus;
        row.dataset.tab = tabForStatus(newStatus);

        const badge = row.querySelector('.status-badge');
        badge.className = 'status-badge status-' + newStatus;
        badge.replaceChildren(
          Object.assign(document.createElement('span'), { className: 'dot' }),
          document.createTextNode(statusLabels[newStatus] || newStatus)
        );

        updateCounts();
        renderList();
      };

      if (application.status === status) {
        apply(status);
        return;
      }

      const buttons = [archiveBtn, deleteBtn];
      buttons.forEach(function (button) { button.disabled = true; });

      fetch(statusUrl, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        },
        body: new URLSearchParams({ id: id, status: status }),
      })
        .then(function (response) {
          return response.json().catch(function () { return null; });
        })
        .then(function (data) {
          if (!data || !data.success) {
            window.alert(data && data.message ? data.message : 'Không thể cập nhật hồ sơ. Vui lòng thử lại.');
            return;
          }
          apply(data.status);
        })
        .catch(function () {
          window.alert('Không thể cập nhật hồ sơ. Vui lòng thử lại.');
        })
        .finally(function () {
          buttons.forEach(function (button) { button.disabled = false; });
        });
    }

    root.addEventListener('click', function (event) {
      const tab = event.target.closest('#tabsContainer .tab');
      if (tab && root.contains(tab)) {
        activeTab = tab.dataset.tab;
        renderList();
        return;
      }

      const row = event.target.closest('.message-item');
      if (row && root.contains(row)) {
        selectApplication(row.dataset.applicationId);
        return;
      }

      if (event.target.closest('#archiveMsgBtn')) {
        moveActiveApplication(activeTab === 'archive' ? 'reviewed' : 'archived');
      } else if (event.target.closest('#deleteMsgBtn')) {
        moveActiveApplication('deleted');
      }
    });

    searchInput.addEventListener('input', function () {
      renderList();
    });

    updateCounts();
    renderList(rows.find(function (row) { return row.classList.contains('active'); }));
  },
};