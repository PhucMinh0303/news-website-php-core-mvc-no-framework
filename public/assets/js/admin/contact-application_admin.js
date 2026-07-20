$(document).ready(function() {
    // ===================== DATA =====================
    // Sample messages
    const messages = [
        {
            id: 1,
            from: "John Doe",
            email: "john@example.com",
            title: "Story Tip: Local Council Corruption",
            content: "I have evidence of council members taking bribes for development projects. Please contact me securely. This is a very sensitive matter and I would like to speak to someone in the investigative team specifically.",
            date: "8/11/2023 17:15",
            location: "New York, USA",
            platform: "Chrome v122 / MacOS",
            ip: "192.168.1.104",
            page: "/stories/latest-news",
            status: "inbox", // inbox | archive | deleted
            notes: [
                { author: "Alex Editor", time: "19:00 8/11/2023", content: "Spoke to legal about this. Need more verification." }
            ]
        },
        {
            id: 2,
            from: "Sarah Chen",
            email: "sarah@example.com",
            title: "Feedback: Recent Coverage on Climate",
            content: "I really appreciated your recent article about climate change policies. It was well-researched and balanced. However, I noticed a small error in the statistics on page 3 - the percentage should be 67% not 76%. Just wanted to bring it to your attention.",
            date: "7/11/2023 09:30",
            location: "San Francisco, USA",
            platform: "Firefox v119 / Windows",
            ip: "192.168.1.105",
            page: "/stories/climate-change",
            status: "inbox",
            notes: []
        },
        {
            id: 3,
            from: "Mike Johnson",
            email: "mike@example.com",
            title: "Story Idea: Tech Startups in Vietnam",
            content: "I'm a tech entrepreneur based in Ho Chi Minh City and I think there's a great story about the growing startup scene here. I can connect you with several founders and investors. There's been a 300% increase in VC funding in the last 2 years.",
            date: "6/11/2023 14:45",
            location: "Ho Chi Minh City, Vietnam",
            platform: "Chrome v121 / Windows",
            ip: "192.168.1.106",
            page: "/stories/tech-innovation",
            status: "inbox",
            notes: []
        },
        {
            id: 4,
            from: "Emily Wilson",
            email: "emily@example.com",
            title: "Update on School District Story",
            content: "Following up on our earlier conversation about the school district budget issues. I've obtained more documents that show irregularities in the allocation of funds for the new sports complex. Happy to meet in person to discuss.",
            date: "5/11/2023 11:20",
            location: "Boston, USA",
            platform: "Safari v17 / MacOS",
            ip: "192.168.1.107",
            page: "/stories/education",
            status: "archive",
            notes: [
                { author: "Sarah Editor", time: "10:00 6/11/2023", content: "Followed up with Emily. She will send more docs tomorrow." }
            ]
        },
        {
            id: 5,
            from: "David Park",
            email: "david@example.com",
            title: "Tip: Healthcare Scam Investigation",
            content: "I have information about a large-scale healthcare fraud operation in the Midwest. Several clinics are billing Medicare for services never rendered. I used to work in one of these clinics and have documentation to back this up.",
            date: "4/11/2023 08:10",
            location: "Chicago, USA",
            platform: "Chrome v121 / Windows",
            ip: "192.168.1.108",
            page: "/stories/healthcare",
            status: "archive",
            notes: [
                { author: "Mike Editor", time: "15:30 4/11/2023", content: "Very credible tip. Set up interview for next week." }
            ]
        },
        {
            id: 6,
            from: "Lisa Thompson",
            email: "lisa@example.com",
            title: "Spam: Free Money Offer",
            content: "Congratulations! You have been selected to receive $10,000,000. Please send your bank details to claim your prize. This is a once in a lifetime opportunity! Don't miss out!",
            date: "3/11/2023 22:30",
            location: "Unknown",
            platform: "Unknown",
            ip: "192.168.1.109",
            page: "/stories/contact",
            status: "deleted",
            notes: []
        }
    ];

    // ===================== STATE =====================
    let currentTab = 'inbox';
    let selectedId = null;
    let currentMessages = [...messages];

    // ===================== RENDER FUNCTIONS =====================
    function getMessagesByStatus(status) {
        return currentMessages.filter(m => m.status === status);
    }

    function renderMessageList(status) {
        const filtered = getMessagesByStatus(status);
        const listContainer = $('#inboxListContainer');
        const archiveEmpty = $('#archiveEmpty');
        const deletedEmpty = $('#deletedEmpty');

        // Hide empty states
        archiveEmpty.hide();
        deletedEmpty.hide();
        listContainer.show();

        if (status === 'archive' && filtered.length === 0) {
            listContainer.hide();
            archiveEmpty.show();
            return;
        }

        if (status === 'deleted' && filtered.length === 0) {
            listContainer.hide();
            deletedEmpty.show();
            return;
        }

        listContainer.show();

        if (filtered.length === 0) {
            listContainer.html(`
                <div style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                    <i class="fa-regular fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 12px;"></i>
                    <p>No messages in this folder</p>
                </div>
            `);
            return;
        }

        let html = '';
        filtered.forEach((msg, index) => {
            const activeClass = (selectedId === msg.id) ? 'active' : '';
            // Get short preview
            const preview = msg.content.substring(0, 60) + (msg.content.length > 60 ? '...' : '');
            html += `
                <div class="message-item ${activeClass}" data-id="${msg.id}" data-status="${msg.status}">
                    <h3>${msg.from}</h3>
                    <div class="title">${msg.title}</div>
                    <div style="font-size: 0.75rem; color: #6b7280; display: flex; justify-content: space-between;">
                        <span>${preview}</span>
                        <span class="date">${msg.date.split(' ')[0]}</span>
                    </div>
                </div>
            `;
        });

        listContainer.html(html);

        // Click handler for messages
        $('.message-item').on('click', function() {
            const id = parseInt($(this).data('id'));
            selectMessage(id);
        });
    }

    function selectMessage(id) {
        selectedId = id;
        const msg = currentMessages.find(m => m.id === id);
        if (!msg) return;

        // Update active class
        $('.message-item').removeClass('active');
        $(`.message-item[data-id="${id}"]`).addClass('active');

        // Show detail panel
        const detailPanel = $('#detailPanel');
        detailPanel.addClass('active-panel');
        $('#emptyPanel').hide();

        // Populate detail
        $('#detailTitle').text(msg.title);
        $('#detailSender').html(`${msg.from} — ${msg.email} <br /> ${msg.date}`);

        // Format content with paragraphs
        let contentHtml = msg.content.split('\n').filter(p => p.trim()).map(p => `<p>${p}</p>`).join('');
        $('#detailContent').html(contentHtml);

        // Notes
        const noteBox = $('#internalNoteBox');
        if (msg.notes && msg.notes.length > 0) {
            const notesHtml = msg.notes.map(n => `
                <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">
                    <strong>${n.author}</strong> — ${n.time} <br />
                    ${n.content}
                </div>
            `).join('');
            noteBox.html(notesHtml);
        } else {
            noteBox.html('<span style="color: #9ca3af;">No notes yet.</span>');
        }

        // Metadata
        $('#metadataArea').html(`
            <p><strong>Location:</strong> ${msg.location}</p>
            <p><strong>Platform:</strong> ${msg.platform}</p>
            <p><strong>IP Address:</strong> ${msg.ip}</p>
            <p><strong>Page Source:</strong> ${msg.page}</p>
        `);
    }

    function updateCounts() {
        const inboxCount = getMessagesByStatus('inbox').length;
        const archiveCount = getMessagesByStatus('archive').length;
        const deletedCount = getMessagesByStatus('deleted').length;

        $('#inboxCount').text(inboxCount);
        $('#archiveCount').text(archiveCount);
        $('#deletedCount').text(deletedCount);

        // Update badge
        if (inboxCount > 0) {
            $('#newMessagesBadge').text(`${inboxCount} thư mới`).show();
        } else {
            $('#newMessagesBadge').hide();
        }
    }

    // ===================== TAB SWITCHING =====================
    function switchTab(tab) {
        currentTab = tab;

        // Update tab UI
        $('.tab').removeClass('active');
        $(`.tab[data-tab="${tab}"]`).addClass('active');

        // Show/hide empty states
        $('#archiveEmpty').hide();
        $('#deletedEmpty').hide();
        $('#inboxListContainer').show();

        // Render list
        renderMessageList(tab);

        // Clear detail panel if no message selected or if selected message not in this tab
        const visibleMessages = getMessagesByStatus(tab);
        if (visibleMessages.length === 0) {
            $('#detailPanel').removeClass('active-panel');
            $('#emptyPanel').show();
            selectedId = null;
        } else if (!visibleMessages.some(m => m.id === selectedId)) {
            // Select first message
            selectMessage(visibleMessages[0].id);
        } else {
            // Re-select current
            selectMessage(selectedId);
        }
    }

    // ===================== ACTION BUTTONS =====================
    // Archive button
    $('#archiveMsgBtn').on('click', function() {
        if (!selectedId) return;
        const msg = currentMessages.find(m => m.id === selectedId);
        if (!msg || msg.status === 'archive') return;

        msg.status = 'archive';
        updateCounts();
        switchTab(currentTab);
    });

    // Delete button
    $('#deleteMsgBtn').on('click', function() {
        if (!selectedId) return;
        const msg = currentMessages.find(m => m.id === selectedId);
        if (!msg) return;

        if (msg.status === 'deleted') {
            // Permanently delete
            const index = currentMessages.indexOf(msg);
            if (index > -1) {
                currentMessages.splice(index, 1);
            }
            selectedId = null;
            updateCounts();
            switchTab(currentTab);
            return;
        }

        msg.status = 'deleted';
        updateCounts();
        switchTab(currentTab);
    });

    // Jump to Archive
    $('#jumpToArchiveBtn').on('click', function() {
        switchTab('archive');
    });

    // ===================== SEARCH =====================
    let searchTimeout = null;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = $(this).val().toLowerCase().trim();
            
            if (query === '') {
                // Reset to original messages
                currentMessages = [...messages];
            } else {
                // Filter messages
                currentMessages = messages.filter(m => 
                    m.from.toLowerCase().includes(query) ||
                    m.title.toLowerCase().includes(query) ||
                    m.content.toLowerCase().includes(query) ||
                    m.email.toLowerCase().includes(query)
                );
            }

            updateCounts();
            switchTab(currentTab);
        }, 300);
    });

    // ===================== NOTES =====================
    $('#addNoteBtn').on('click', function() {
        const input = $('#newNoteInput');
        const content = input.val().trim();
        if (!content || !selectedId) return;

        const msg = currentMessages.find(m => m.id === selectedId);
        if (!msg) return;

        // Add note
        if (!msg.notes) msg.notes = [];
        msg.notes.push({
            author: 'Current Editor',
            time: new Date().toLocaleString('en-GB', { 
                hour: '2-digit', 
                minute: '2-digit', 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            }),
            content: content
        });

        input.val('');
        selectMessage(selectedId);
    });

    // ===================== REPLY BUTTON =====================
    $('#replyBtn').on('click', function() {
        if (!selectedId) return;
        const msg = currentMessages.find(m => m.id === selectedId);
        if (!msg) return;
        
        alert(`Reply to: ${msg.from} (${msg.email})\n\nSubject: Re: ${msg.title}\n\n[Your reply would open in a modal or new page]`);
    });

    // ===================== EXPAND BUTTON =====================
    $('#expandBtn').on('click', function() {
        const panel = $('#detailPanel');
        const container = $('.container');
        
        if (panel.hasClass('expanded')) {
            panel.removeClass('expanded');
            container.css('display', 'flex');
            $('.message-list, #archiveEmpty, #deletedEmpty').show();
            $(this).html('<i class="fa-solid fa-expand"></i>');
        } else {
            panel.addClass('expanded');
            container.css('display', 'block');
            $('.message-list, #archiveEmpty, #deletedEmpty').hide();
            $(this).html('<i class="fa-solid fa-compress"></i>');
        }
    });

    // ===================== INIT =====================
    // Set default selected
    const defaultMsg = messages.find(m => m.status === 'inbox');
    if (defaultMsg) {
        selectedId = defaultMsg.id;
    }

    updateCounts();
    renderMessageList('inbox');
    
    if (selectedId) {
        selectMessage(selectedId);
    } else {
        $('#detailPanel').removeClass('active-panel');
        $('#emptyPanel').show();
    }

    // Tab click handlers
    $('.tab').on('click', function() {
        const tab = $(this).data('tab');
        switchTab(tab);
    });

    // ===================== ADDITIONAL STYLES FOR EXPAND =====================
    // Inject expand styles
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .detail-panel.expanded {
                display: flex !important;
                width: 100%;
                max-width: 100%;
            }
            .container.block-mode .message-list,
            .container.block-mode #archiveEmpty,
            .container.block-mode #deletedEmpty {
                display: none !important;
            }
            .container.block-mode .detail-panel {
                display: flex !important;
            }
        `)
        .appendTo('head');

    // Fix for detail panel display on init
    if (!selectedId) {
        $('#emptyPanel').show();
    }
});