let activeMessageId = null;

function loadMessageDetail(msgId) {
    const contact = (window.ADMIN_CONTACTS || []).find(item => Number(item.id) === Number(msgId));
    if (!contact) return;

    $('#detailSubject').text(contact.contact_type || 'Liên hệ');
    $('#detailSenderName').text(contact.customer_name || '');
    $('#detailSenderEmail').text(contact.email || '');
    $('#detailSenderPhone').text(contact.phone || '');
    $('#detailStatus').text('Trạng thái: ' + (contact.status || 'new'));
    $('#detailContent').text(contact.content || '');
    $('#internalNoteBox').text(contact.response_content || 'Chưa có phản hồi.');
    $('#detailIpAddress').text(contact.ip_address || '');
    $('#detailUserAgent').text(contact.user_agent || '');
    $('#detailPageUrl').text(contact.page_url || '');
    $('#detailCreatedAt').text(contact.created_at || contact.date_sent || '');
    $('#restoreMsgBtn').toggle(['archived'].includes(contact.status));
    $('#emptyPanel').hide();
    $('#detailPanel').show();
}

function loadMessagesByTab(tab, selectedRow) {
    $('#tabsContainer .tab').removeClass('active').attr('aria-selected', 'false');
    $('#tabsContainer .tab[data-tab="' + tab + '"]').addClass('active').attr('aria-selected', 'true');
    $('#contactAdmin .message-item').removeClass('active').hide();

    const $visibleRows = $('#contactAdmin .message-item[data-tab="' + tab + '"]').show();
    const $row = selectedRow && selectedRow.length && selectedRow.data('tab') === tab
        ? selectedRow
        : $visibleRows.first();

    $('#archiveEmpty').toggle(tab === 'archive' && !$visibleRows.length);
    $('#deletedEmpty').toggle(tab === 'deleted' && !$visibleRows.length);

    if ($row.length) {
        $row.addClass('active');
        activeMessageId = $row.data('message-id');
        loadMessageDetail(activeMessageId);
    } else {
        $('#detailPanel').hide();
        $('#emptyPanel').show();
    }
}

function updateContactCounts() {
    $('#inboxCount').text($('#contactAdmin .message-item[data-tab="inbox"]').length);
    $('#archiveCount').text($('#contactAdmin .message-item[data-tab="archive"]').length);
    $('#deletedCount').text($('#contactAdmin .message-item[data-tab="deleted"]').length);
    $('#newMessagesBadge').text((window.ADMIN_CONTACTS || []).filter(item => item.status === 'new').length + ' thư mới');
}

function moveActiveContact(status, destinationTab) {
    const $row = $('#contactAdmin .message-item.active').first();
    const contactId = Number($row.data('message-id'));
    const contact = (window.ADMIN_CONTACTS || []).find(item => Number(item.id) === contactId);
    const $actionButtons = $('#archiveMsgBtn, #restoreMsgBtn, #deleteMsgBtn');

    if (!contactId || !contact) return;

    if (contact.status === status) {
        $row.attr('data-tab', destinationTab).data('tab', destinationTab);
        updateContactCounts();
        loadMessagesByTab(destinationTab, $row);
        return;
    }

    $actionButtons.prop('disabled', true);
    $.ajax({
        url: $('#contactAdmin').data('status-url'),
        method: 'POST',
        data: { id: contactId, status: status },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).done(function(response) {
        if (!response || !response.success) {
            window.alert(response && response.message ? response.message : 'Không thể cập nhật thư. Vui lòng thử lại.');
            return;
        }

        contact.status = response.status;
        $row.attr('data-tab', destinationTab).data('tab', destinationTab);

        const $badge = $row.find('.status-badge');
        $badge.attr('class', 'status-badge ' + response.status);
        $badge.empty().append($('<span>').addClass('dot'), document.createTextNode(response.status.toUpperCase()));

        updateContactCounts();
        loadMessagesByTab(destinationTab, $row);
    }).fail(function(xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.message;
        window.alert(message || 'Không thể cập nhật thư. Vui lòng thử lại.');
    }).always(function() {
        $actionButtons.prop('disabled', false);
    });
}

$(document).on('click', '#contactAdmin .message-item', function() {
    $('#contactAdmin .message-item').removeClass('active');
    $(this).addClass('active');
    activeMessageId = $(this).data('message-id');
    loadMessageDetail(activeMessageId);
});

$(document).on('click', '#tabsContainer .tab', function() {
    loadMessagesByTab($(this).data('tab'));
});

$(document).on('click', '#archiveMsgBtn', function() {
    moveActiveContact('archived', 'archive');
});

$(document).on('click', '#deleteMsgBtn', function() {
    moveActiveContact('spam', 'deleted');
});

$(document).on('click', '#restoreMsgBtn', function() {
    moveActiveContact('read', 'inbox');
});

$(function() {
    const tab = $('#tabsContainer .tab.active').data('tab') || 'inbox';
    loadMessagesByTab(tab, $('#contactAdmin .message-item.active').first());
    updateContactCounts();
});

