// contact-admin.js - Quản lý thư phản hồi cho Admin Panel
$(document).ready(function() {
    'use strict';

    // ========================================
    // DỮ LIỆU GIẢ ĐỊNH CHO THƯ PHẢN HỒI
    // ========================================

    const mockMessages = {
        inbox: [
            {
                id: 1,
                from: 'Michael Chen',
                email: 'm.chen@example.com',
                subject: 'Story Tip: Local Council Corruption',
                preview: 'I have evidence of council members taking bribes for development projects...',
                content: 'I have evidence of council members taking bribes for development projects. Please contact me securely. This is a very sensitive matter and I would like to speak to someone in the investigative team specifically.',
                date: '2023-07-11 09:15',
                isRead: false,
                status: 'new',
                location: 'New York, USA',
                platform: 'Chrome v122 / MacOS',
                ip: '192.168.1.104',
                pageSource: '/stories/latest-news',
                notes: [
                    { author: 'Alex Editor', date: '2023-11-08 19:00', text: 'Spoke to legal about this. Need more verification.' }
                ]
            },
            {
                id: 2,
                from: 'Emily Watson',
                email: 'emily.w@example.com',
                subject: 'Report: School Board Budget Irregularities',
                preview: 'I have documents showing unexplained expenses in the school board budget...',
                content: 'I have documents showing unexplained expenses in the school board budget for the 2022-2023 academic year. The total discrepancy is approximately $247,000. I can provide more details and source documents upon secure contact.',
                date: '2023-07-05 14:30',
                isRead: true,
                status: 'applied',
                location: 'Chicago, USA',
                platform: 'Firefox v119 / Windows',
                ip: '192.168.1.156',
                pageSource: '/stories/education',
                notes: [
                    { author: 'Alex Editor', date: '2023-11-08 15:30', text: 'Initial review suggests this could be a major story. Following up.' },
                    { author: 'Sarah Mitchell', date: '2023-11-08 16:45', text: 'Sent additional documents via secure email.' }
                ]
            },
            {
                id: 3,
                from: 'Robert Kim',
                email: 'robert.k@example.com',
                subject: 'Security Breach at City Hall',
                preview: 'I have evidence of unauthorized access to the city hall computer systems...',
                content: 'I have evidence of unauthorized access to the city hall computer systems. The breach occurred on October 15th and appears to have been ongoing. I am a former IT employee and can provide detailed logs and evidence.',
                date: '2023-07-03 22:45',
                isRead: true,
                status: 'reviewing',
                location: 'Unknown (VPN detected)',
                platform: 'Tor Browser / Linux',
                ip: '10.0.0.15',
                pageSource: '/security',
                notes: [
                    { author: 'System', date: '2023-11-07 23:00', text: 'Security team notified. Initiating investigation.' }
                ]
            },
            {
                id: 4,
                from: 'Sarah Johnson',
                email: 'sarah.j@example.com',
                subject: 'FOIA Request: Police Department Records',
                preview: 'We are requesting records under the Freedom of Information Act...',
                content: 'We are requesting records under the Freedom of Information Act regarding the use of facial recognition technology by the police department. This is a formal request on behalf of the Civil Liberties Watchdog organization.',
                date: '2023-07-01 10:00',
                isRead: false,
                status: 'new',
                location: 'Washington DC, USA',
                platform: 'Safari v17 / iOS',
                ip: '192.168.1.200',
                pageSource: '/legal/foia',
                notes: []
            }
        ],
        archive: [
            {
                id: 101,
                from: 'Tom Miller',
                email: 'tom.m@citywatch.com',
                subject: 'Zoning Board Meeting Coverage',
                preview: 'I would like to request coverage for the upcoming zoning board meeting...',
                content: 'I would like to request coverage for the upcoming zoning board meeting on November 15th. There are several controversial items on the agenda including a new mixed-use development proposal that has divided the community.',
                date: '2023-10-25 09:00',
                isRead: true,
                status: 'applied',
                location: 'Austin, USA',
                platform: 'Chrome v120 / Windows',
                ip: '192.168.1.88',
                pageSource: '/community',
                notes: [
                    { author: 'Alex Editor', date: '2023-10-25 10:30', text: 'Assigned to reporter Jane for coverage. Archived.' }
                ]
            },
            {
                id: 102,
                from: 'Megan Walker',
                email: 'megan.w@educationsupport.org',
                subject: 'Teacher Shortage Report',
                preview: 'We have compiled a comprehensive report on the teacher shortage crisis...',
                content: 'We have compiled a comprehensive report on the teacher shortage crisis in the district. The report includes data from 50 schools and interviews with 100+ teachers. We would like to share this with the public.',
                date: '2023-10-20 15:20',
                isRead: true,
                status: 'reviewing',
                location: 'Denver, USA',
                platform: 'Edge v119 / Windows',
                ip: '192.168.1.130',
                pageSource: '/stories/education',
                notes: [
                    { author: 'Alex Editor', date: '2023-10-21 08:00', text: 'Good data. Published as a special report. Archived.' }
                ]
            }
        ],
        deleted: [
            {
                id: 201,
                from: 'Spam Bot',
                email: 'spam@example.com',
                subject: 'BUY VIAGRA CHEAP!',
                preview: 'Get the best deals on...',
                content: 'Get the best deals on pharmaceutical products at unbeatable prices. Limited time offer!',
                date: '2023-10-28 03:00',
                isRead: true,
                status: 'applied',
                location: 'Unknown',
                platform: 'Automated Script',
                ip: '10.0.0.25',
                pageSource: '/spam',
                notes: []
            }
        ]
    };

    // ========================================
    // BIẾN TOÀN CỤC
    // ========================================

    let selectedMessageId = null;
    let currentTab = 'inbox';
    let activeMessageId = null;
    let currentMessageTab = 'inbox';

    // ========================================
    // HÀM HIỂN THỊ DANH SÁCH THƯ
    // ========================================

    function renderMessageList(tabName) {
        const messages = mockMessages[tabName] || [];
        const containerMap = {
            'inbox': '#inboxListContainer',
            'archive': '#archiveListContainer',
            'deleted': '#deletedListContainer'
        };

        const container = containerMap[tabName];
        if (!container) return;

        // Tạo container nếu chưa tồn tại
        if ($(container).length === 0) {
            const newContainer = $('<div>')
                .attr('id', container.replace('#', ''))
                .addClass('message-list')
                .css('display', tabName === currentTab ? 'block' : 'none');
            $('#inboxListContainer').after(newContainer);
        }

        // Xóa nội dung cũ
        $(container).empty();

        if (messages.length === 0) {
            if (tabName === 'archive') {
                $('#archiveEmpty').show();
                $(container).hide();
            } else if (tabName === 'deleted') {
                $('#deletedEmpty').show();
                $(container).hide();
            }
            updateTabCount(tabName, 0);
            return;
        }

        if (tabName === 'archive') $('#archiveEmpty').hide();
        if (tabName === 'deleted') $('#deletedEmpty').hide();
        $(container).show();

        // Render từng message item
        messages.forEach((msg) => {
            const date = new Date(msg.date);
            const formattedDate = date.toLocaleDateString('vi-VN', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            });

            // Xác định trạng thái hiển thị
            let statusBadge = '';
            if (!msg.isRead && msg.status === 'new') {
                statusBadge = `<span class="status-badge status-new">NEW</span>`;
            } else if (msg.status === 'applied') {
                statusBadge = `<span class="status-badge status-applied">APPLIED ${formattedDate}</span>`;
            } else if (msg.status === 'reviewing') {
                statusBadge = `<span class="status-badge status-reviewing">REVIEWING</span>`;
            }

            const item = $('<div>')
                .addClass('message-item')
                .addClass('clickable')
                .data('message-id', msg.id)
                .data('tab', tabName)
                .html(`
                    <div class="message-item-header">
                        <h3 class="message-sender">${msg.from}</h3>
                        <div class="message-date">${formattedDate}</div>
                    </div>
                    <div class="message-item-body">
                        <div class="message-email">${msg.email}</div>
                        ${statusBadge}
                    </div>
                    <div class="message-subject">${msg.subject}</div>
                    <div class="message-preview">${msg.preview}</div>
                `);

            if (msg.id === activeMessageId && tabName === currentTab) {
                item.addClass('active');
            }

            item.on('click', function() {
                const msgId = $(this).data('message-id');
                const tab = $(this).data('tab');
                loadMessageDetail(msgId, tab);
                
                $(container).find('.message-item').removeClass('active');
                $(this).addClass('active');
                activeMessageId = msgId;
            });

            $(container).append(item);
        });

        updateTabCount(tabName, messages.length);
    }

    // ========================================
    // HÀM LOAD CHI TIẾT THƯ
    // ========================================

    function loadMessageDetail(messageId, tabName) {
        const messages = mockMessages[tabName] || [];
        const message = messages.find(m => m.id === messageId);
        
        if (!message) {
            $('#detailPanel').removeClass('active-panel');
            $('#emptyPanel').show();
            return;
        }

        if (!message.isRead) {
            message.isRead = true;
            renderMessageList(tabName);
            updateNewMessagesBadge();
        }

        $('#emptyPanel').hide();
        $('#detailPanel').addClass('active-panel');

        const date = new Date(message.date);
        const formattedDate = date.toLocaleDateString('vi-VN', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
        const timeStr = date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        $('#detailTitle').text(message.subject);
        $('#detailSender').html(`${message.from} — ${message.email}<br />${formattedDate} ${timeStr}`);
        $('#detailContent').text(message.content);

        $('#metadataArea').html(`
            <p><strong>Vị trí:</strong> ${message.location}</p>
            <p><strong>Nền tảng:</strong> ${message.platform}</p>
            <p><strong>Địa chỉ IP:</strong> ${message.ip}</p>
            <p><strong>Nguồn trang:</strong> ${message.pageSource}</p>
        `);

        const noteBox = $('#internalNoteBox');
        noteBox.empty();
        
        if (message.notes && message.notes.length > 0) {
            message.notes.forEach(note => {
                const noteDate = new Date(note.date);
                const noteFormatted = noteDate.toLocaleDateString('vi-VN', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric' 
                });
                const noteTime = noteDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                
                noteBox.append(`
                    <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f0e6b0;">
                        <strong>${note.author}</strong> — ${noteFormatted} ${noteTime}<br />
                        ${note.text}
                    </div>
                `);
            });
        } else {
            noteBox.html('<em>Chưa có ghi chú nào.</em>');
        }

        selectedMessageId = messageId;
        currentMessageTab = tabName;

        window.currentMessage = message;
        window.currentTab = tabName;
    }

    // ========================================
    // HÀM XỬ LÝ THAO TÁC VỚI THƯ
    // ========================================

    // Xóa thư
    $('#deleteMsgBtn').on('click', function() {
        if (!selectedMessageId) {
            showToast('Vui lòng chọn một thư để xóa.');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn xóa thư này?')) return;

        const currentMsg = window.currentMessage;
        const currentTab = window.currentTab;

        if (currentTab === 'inbox') {
            const inboxIndex = mockMessages.inbox.findIndex(m => m.id === selectedMessageId);
            if (inboxIndex > -1) {
                const deletedMsg = mockMessages.inbox.splice(inboxIndex, 1)[0];
                deletedMsg.id = 200 + mockMessages.deleted.length + 1;
                mockMessages.deleted.push(deletedMsg);
            }
        } else if (currentTab === 'archive') {
            const archiveIndex = mockMessages.archive.findIndex(m => m.id === selectedMessageId);
            if (archiveIndex > -1) {
                const deletedMsg = mockMessages.archive.splice(archiveIndex, 1)[0];
                deletedMsg.id = 200 + mockMessages.deleted.length + 1;
                mockMessages.deleted.push(deletedMsg);
            }
        } else if (currentTab === 'deleted') {
            const deletedIndex = mockMessages.deleted.findIndex(m => m.id === selectedMessageId);
            if (deletedIndex > -1) {
                mockMessages.deleted.splice(deletedIndex, 1);
            }
        }

        renderMessageList('inbox');
        renderMessageList('archive');
        renderMessageList('deleted');

        $('#detailPanel').removeClass('active-panel');
        $('#emptyPanel').show();
        selectedMessageId = null;
        activeMessageId = null;

        updateNewMessagesBadge();
        showToast('Đã xóa thư thành công.');
    });

    // Lưu trữ thư
    $('#archiveMsgBtn').on('click', function() {
        if (!selectedMessageId) {
            showToast('Vui lòng chọn một thư để lưu trữ.');
            return;
        }

        if (window.currentTab === 'archive') {
            showToast('Thư này đã được lưu trữ.');
            return;
        }

        if (window.currentTab === 'deleted') {
            showToast('Không thể lưu trữ thư trong mục đã xóa.');
            return;
        }

        const inboxIndex = mockMessages.inbox.findIndex(m => m.id === selectedMessageId);
        if (inboxIndex > -1) {
            const archivedMsg = mockMessages.inbox.splice(inboxIndex, 1)[0];
            archivedMsg.id = 100 + mockMessages.archive.length + 1;
            mockMessages.archive.push(archivedMsg);
        }

        renderMessageList('inbox');
        renderMessageList('archive');

        $('#detailPanel').removeClass('active-panel');
        $('#emptyPanel').show();
        selectedMessageId = null;
        activeMessageId = null;

        updateNewMessagesBadge();
        showToast('Đã lưu trữ thư thành công.');
    });

    // Reply
    $('#replyBtn').on('click', function() {
        if (!selectedMessageId) {
            showToast('Vui lòng chọn một thư để trả lời.');
            return;
        }

        const msg = window.currentMessage;
        if (!msg) return;

        const replyContent = prompt(
            `Trả lời ${msg.from} (${msg.email}):\n\nChủ đề: RE: ${msg.subject}\n\nNhập nội dung trả lời:`
        );

        if (replyContent !== null && replyContent.trim() !== '') {
            if (!mockMessages[window.currentTab].find(m => m.id === selectedMessageId)) {
                showToast('Không thể thêm ghi chú cho thư đã được xử lý.');
                return;
            }

            const note = {
                author: 'Admin System',
                date: new Date().toISOString().replace('T', ' ').slice(0, 16),
                text: `Đã gửi trả lời: "${replyContent.trim()}"`
            };

            const targetMsg = mockMessages[window.currentTab].find(m => m.id === selectedMessageId);
            if (targetMsg) {
                if (!targetMsg.notes) targetMsg.notes = [];
                targetMsg.notes.push(note);
                
                loadMessageDetail(selectedMessageId, window.currentTab);
                renderMessageList(window.currentTab);
                
                showToast('Đã gửi trả lời thành công.');
            }
        }
    });

    // Thêm ghi chú
    $('#addNoteBtn').on('click', function() {
        const noteInput = $('#newNoteInput');
        const noteText = noteInput.val().trim();

        if (!selectedMessageId) {
            showToast('Vui lòng chọn một thư để thêm ghi chú.');
            return;
        }

        if (noteText === '') {
            showToast('Vui lòng nhập nội dung ghi chú.');
            return;
        }

        const targetMsg = mockMessages[window.currentTab].find(m => m.id === selectedMessageId);
        if (!targetMsg) {
            showToast('Không tìm thấy thư.');
            return;
        }

        const note = {
            author: 'Admin',
            date: new Date().toISOString().replace('T', ' ').slice(0, 16),
            text: noteText
        };

        if (!targetMsg.notes) targetMsg.notes = [];
        targetMsg.notes.push(note);

        noteInput.val('');

        loadMessageDetail(selectedMessageId, window.currentTab);
        renderMessageList(window.currentTab);

        showToast('Đã thêm ghi chú thành công.');
    });

    $('#newNoteInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#addNoteBtn').click();
        }
    });

    // Tìm kiếm thư
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const currentTabName = $('.tab.active').data('tab') || 'inbox';
        const messages = mockMessages[currentTabName] || [];
        
        const containerMap = {
            'inbox': '#inboxListContainer',
            'archive': '#archiveListContainer',
            'deleted': '#deletedListContainer'
        };
        const container = containerMap[currentTabName];
        if (!container) return;

        const filtered = messages.filter(msg => 
            msg.from.toLowerCase().includes(searchTerm) ||
            msg.subject.toLowerCase().includes(searchTerm) ||
            msg.content.toLowerCase().includes(searchTerm) ||
            msg.email.toLowerCase().includes(searchTerm)
        );

        $(container).empty();

        if (filtered.length === 0) {
            $(container).html(`
                <div style="padding: 40px 20px; text-align: center; color: #6c757d;">
                    <i class="fas fa-search" style="font-size: 2rem; display: block; margin-bottom: 12px;"></i>
                    <p>Không tìm thấy kết quả phù hợp với "<strong>${searchTerm}</strong>"</p>
                </div>
            `);
            $(container).show();
            return;
        }

        filtered.forEach((msg) => {
            const date = new Date(msg.date);
            const formattedDate = date.toLocaleDateString('vi-VN', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            });

            let statusBadge = '';
            if (!msg.isRead && msg.status === 'new') {
                statusBadge = `<span class="status-badge status-new">NEW</span>`;
            } else if (msg.status === 'applied') {
                statusBadge = `<span class="status-badge status-applied">APPLIED ${formattedDate}</span>`;
            } else if (msg.status === 'reviewing') {
                statusBadge = `<span class="status-badge status-reviewing">REVIEWING</span>`;
            }

            const item = $('<div>')
                .addClass('message-item')
                .addClass('clickable')
                .data('message-id', msg.id)
                .data('tab', currentTabName)
                .html(`
                    <div class="message-item-header">
                        <h3 class="message-sender">${msg.from}</h3>
                        <div class="message-date">${formattedDate}</div>
                    </div>
                    <div class="message-item-body">
                        <div class="message-email">${msg.email}</div>
                        ${statusBadge}
                    </div>
                    <div class="message-subject">${msg.subject}</div>
                    <div class="message-preview">${msg.preview}</div>
                `);

            item.on('click', function() {
                const msgId = $(this).data('message-id');
                const tab = $(this).data('tab');
                loadMessageDetail(msgId, tab);
                $(container).find('.message-item').removeClass('active');
                $(this).addClass('active');
                activeMessageId = msgId;
            });

            $(container).append(item);
        });
    });

    // ========================================
    // TOAST NOTIFICATION
    // ========================================

    function showToast(message) {
        $('#customToast').remove();

        const toast = $(`
            <div id="customToast" style="
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: #1e293b;
                color: white;
                padding: 14px 24px;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 500;
                box-shadow: 0 8px 30px rgba(0,0,0,0.2);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 12px;
                animation: slideUp 0.3s ease;
                min-width: 250px;
            ">
                <i class="fas fa-check-circle" style="color: #34d399; font-size: 1.2rem;"></i>
                <span>${message}</span>
            </div>
        `);

        $('body').append(toast);

        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // ========================================
    // SEGMENTED TABS FUNCTIONALITY
    // ========================================

    const tabsContainer = $('#tabsContainer');
    const tabs = $('.tab');
    const slider = $('.tab-slider');

    function updateSlider(activeTab) {
        if (!slider.length || !activeTab.length) return;

        const tabOffset = activeTab.position();
        const tabWidth = activeTab.outerWidth();

        slider.css({
            left: tabOffset.left + 'px',
            width: tabWidth + 'px'
        });
    }

    function activateTab(tabElement) {
        const $tab = $(tabElement);
        const tabName = $tab.data('tab');

        tabs.removeClass('active').attr('aria-selected', 'false');
        $tab.addClass('active').attr('aria-selected', 'true');

        updateSlider($tab);
        switchTabContent(tabName);
    }

    function switchTabContent(tabName) {
        currentTab = tabName;

        const containers = {
            'inbox': '#inboxListContainer',
            'archive': '#archiveListContainer',
            'deleted': '#deletedListContainer'
        };

        $('.message-list, .empty-panel, #archiveEmpty, #deletedEmpty')
            .stop()
            .fadeOut(200);

        const container = containers[tabName];
        if (container) {
            if ($(container).length === 0) {
                const newContainer = $('<div>')
                    .attr('id', container.replace('#', ''))
                    .addClass('message-list')
                    .css('display', 'block');
                $('#inboxListContainer').after(newContainer);
            }
            
            const messages = mockMessages[tabName] || [];
            if (tabName === 'archive') {
                if (messages.length === 0) {
                    $('#archiveEmpty').stop().fadeIn(300);
                    $(container).hide();
                } else {
                    $('#archiveEmpty').hide();
                    $(container).stop().fadeIn(300);
                }
            } else if (tabName === 'deleted') {
                if (messages.length === 0) {
                    $('#deletedEmpty').stop().fadeIn(300);
                    $(container).hide();
                } else {
                    $('#deletedEmpty').hide();
                    $(container).stop().fadeIn(300);
                }
            } else {
                $(container).stop().fadeIn(300);
            }

            renderMessageList(tabName);
        }

        $('#detailPanel').removeClass('active-panel');
        $('#emptyPanel').show();
        selectedMessageId = null;
        activeMessageId = null;

        $(document).trigger('tabChanged', [tabName]);
    }

    // ========================================
    // TAB EVENT HANDLERS
    // ========================================

    tabs.on('click', function(e) {
        const $this = $(this);
        if ($this.hasClass('active')) return;
        activateTab(this);
    });

    tabs.on('keydown', function(e) {
        const $this = $(this);
        const currentIndex = tabs.index($this);
        let newIndex = currentIndex;

        switch (e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                newIndex = (currentIndex + 1) % tabs.length;
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                newIndex = (currentIndex - 1 + tabs.length) % tabs.length;
                break;
            case 'Home':
                e.preventDefault();
                newIndex = 0;
                break;
            case 'End':
                e.preventDefault();
                newIndex = tabs.length - 1;
                break;
            default:
                return;
        }

        const $targetTab = tabs.eq(newIndex);
        $targetTab.focus();
        activateTab($targetTab);
    });

    // ========================================
    // UPDATE BADGE COUNTS
    // ========================================

    window.updateTabCount = function(tabName, count) {
        const tabMap = {
            'inbox': '#inboxCount',
            'archive': '#archiveCount',
            'deleted': '#deletedCount'
        };

        const selector = tabMap[tabName];
        if (selector) {
            const $badge = $(selector);
            const $tab = $badge.closest('.tab');
            
            $badge.fadeOut(150, function() {
                $(this).text(count).fadeIn(150);
            });

            if ($tab.length) {
                const tabLabel = $tab.text().trim().replace(/\d+/, count);
                $tab.attr('aria-label', tabLabel);
            }
        }
    };

    function updateNewMessagesBadge() {
        const unreadCount = mockMessages.inbox.filter(m => !m.isRead).length;
        const badge = $('#newMessagesBadge');
        if (unreadCount > 0) {
            badge.text(`${unreadCount} thư mới`).show();
        } else {
            badge.hide();
        }
    }

    // ========================================
    // WINDOW RESIZE HANDLER
    // ========================================

    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const activeTab = tabs.filter('.active');
            if (activeTab.length) {
                updateSlider(activeTab);
            }
        }, 150);
    });

    // ========================================
    // PUBLIC API
    // ========================================

    window.ContactTabs = {
        activate: activateTab,
        getActiveTab: function() {
            return tabs.filter('.active').data('tab');
        },
        refreshSlider: function() {
            const activeTab = tabs.filter('.active');
            if (activeTab.length) {
                updateSlider(activeTab);
            }
        }
    };

    // ========================================
    // KHỞI TẠO
    // ========================================

    function initTabs() {
        const activeTab = tabs.filter('.active');
        
        if (!activeTab.length) {
            tabs.first().addClass('active').attr('aria-selected', 'true');
            updateSlider(tabs.first());
        } else {
            updateSlider(activeTab);
        }
    }

    if (slider.length) {
        initTabs();
    }

    function createMissingContainers() {
        const containers = [
            { id: 'archiveListContainer', label: 'Archive' },
            { id: 'deletedListContainer', label: 'Deleted' }
        ];

        containers.forEach(cont => {
            if ($('#' + cont.id).length === 0) {
                const newContainer = $('<div>')
                    .attr('id', cont.id)
                    .addClass('message-list')
                    .css('display', 'none');
                $('#inboxListContainer').after(newContainer);
            }
        });
    }

    createMissingContainers();

    // Render danh sách ban đầu
    renderMessageList('inbox');

    // Tự động load thư đầu tiên
    setTimeout(function() {
        const firstMessage = mockMessages.inbox[0];
        if (firstMessage) {
            loadMessageDetail(firstMessage.id, 'inbox');
            $('#inboxListContainer .message-item:first').addClass('active');
            activeMessageId = firstMessage.id;
        }
    }, 300);

    updateNewMessagesBadge();

    // Thêm CSS animation cho toast
    $('head').append(`
        <style>
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    `);

    console.log('Contact Management System initialized successfully');
    console.log('Mock data loaded:', mockMessages);


        // Hàm load chi tiết thư - Cập nhật để hiển thị đúng format
function loadMessageDetail(messageId, tabName) {
    const messages = mockMessages[tabName] || [];
    const message = messages.find(m => m.id === messageId);
    
    if (!message) {
        $('#detailPanel').removeClass('active-panel').hide();
        $('#emptyPanel').show();
        return;
    }

    // Đánh dấu đã đọc
    if (!message.isRead) {
        message.isRead = true;
        renderMessageList(tabName);
    }

    // Ẩn empty panel, hiển thị detail panel
    $('#emptyPanel').hide();
    $('#detailPanel').show().addClass('active-panel');

    // Cập nhật subject
    $('#detailSubject').text(message.subject);

    // Cập nhật sender info - format như trong hình
    $('#detailSenderName').text(message.from);
    $('#detailSenderEmail').html(`<a href="mailto:${message.email}">${message.email}</a>`);
    $('#detailSenderTo').text('To: NovaNews Support Team');

    // Cập nhật nội dung - giữ nguyên xuống dòng
    $('#detailContent').html(message.content.replace(/\n/g, '<br />'));

    // Cập nhật metadata
    $('#metadataArea').html(`
        <p><strong>Location:</strong> ${message.location}</p>
        <p><strong>Platform:</strong> ${message.platform}</p>
        <p><strong>IP Address:</strong> ${message.ip}</p>
        <p><strong>Page Source:</strong> ${message.pageSource}</p>
    `);

    // Cập nhật notes
    const noteBox = $('#internalNoteBox');
    noteBox.empty();
    
    if (message.notes && message.notes.length > 0) {
        message.notes.forEach(note => {
            const noteDate = new Date(note.date);
            const noteFormatted = noteDate.toLocaleDateString('vi-VN', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            });
            const noteTime = noteDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            
            noteBox.append(`
                <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f0e6b0;">
                    <strong>${note.author}</strong> — ${noteTime} ${noteFormatted}<br />
                    ${note.text}
                </div>
            `);
        });
    } else {
        noteBox.html('<em>Chưa có ghi chú nào.</em>');
    }

    // Lưu trạng thái
    selectedMessageId = messageId;
    currentMessageTab = tabName;
    window.currentMessage = message;
    window.currentTab = tabName;
}

// Cập nhật event click cho message items
$(document).on('click', '.message-item', function() {
    const msgId = $(this).data('message-id');
    const tab = $(this).data('tab') || 'inbox';
    
    // Remove active class từ tất cả items
    $('.message-item').removeClass('active');
    $(this).addClass('active');
    
    // Load detail
    loadMessageDetail(msgId, tab);
    activeMessageId = msgId;
});
});