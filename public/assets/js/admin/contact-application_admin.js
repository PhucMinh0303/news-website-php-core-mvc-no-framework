(function() {
      // ------------------------------
      // MOCK DATA for counts
      // ------------------------------
      let folderCounts = {
        inbox: 3,
        archive: 2,
        deleted: 1
      };

      let currentActiveTab = 'inbox'; // inbox, archive, deleted

      // Get DOM elements
      const tabsContainer = document.getElementById('tabsContainer');
      const tabs = document.querySelectorAll('.tab');
      const inboxCountSpan = document.getElementById('inboxCount');
      const archiveCountSpan = document.getElementById('archiveCount');
      const deletedCountSpan = document.getElementById('deletedCount');
      const currentFolderLabel = document.getElementById('currentFolderLabel');
      const folderMessageDiv = document.getElementById('folderMessage');
      const jumpToArchiveBtn = document.getElementById('jumpToArchiveBtn');

      // ----- Create & Append Slider Element -----
      const slider = document.createElement('div');
      slider.classList.add('tab-slider');
      tabsContainer.style.position = 'relative';
      tabsContainer.appendChild(slider);

      // Function to update slider position based on active tab
      function updateSliderPosition(activeTabElement) {
        if (!activeTabElement) return;
        const containerRect = tabsContainer.getBoundingClientRect();
        const tabRect = activeTabElement.getBoundingClientRect();

        // calculate relative left offset and width
        const leftOffset = tabRect.left - containerRect.left;
        const tabWidth = tabRect.width;

        slider.style.width = `${tabWidth}px`;
        slider.style.left = `${leftOffset}px`;
      }

      // Function to update active tab styling + counts display + folder content
      function setActiveTab(tabValue, skipAnimation = false) {
        // update currentActiveTab
        currentActiveTab = tabValue;

        // update tab active classes
        tabs.forEach(tab => {
          const tabData = tab.getAttribute('data-tab');
          if (tabData === tabValue) {
            tab.classList.add('active');
            // update slider position (with animation, unless skipAnimation flag is true for initial)
            if (!skipAnimation) {
              updateSliderPosition(tab);
            } else {
              // for initial load, set instantly
              const containerRect = tabsContainer.getBoundingClientRect();
              const tabRect = tab.getBoundingClientRect();
              slider.style.width = `${tabRect.width}px`;
              slider.style.left = `${tabRect.left - containerRect.left}px`;
            }
          } else {
            tab.classList.remove('active');
          }
        });

        // update UI text based on folder
        let folderName = '';
        let messageText = '';
        switch (tabValue) {
          case 'inbox':
            folderName = 'Inbox';
            messageText = `Showing ${folderCounts.inbox} messages in your inbox. New story tips appear here.`;
            break;
          case 'archive':
            folderName = 'Archived';
            messageText = `You have ${folderCounts.archive} archived conversations.`;
            break;
          case 'deleted':
            folderName = 'Recycle Bin';
            messageText = `${folderCounts.deleted} item(s) in recycle bin. Messages are kept for 30 days.`;
            break;
          default:
            folderName = 'Inbox';
        }
        currentFolderLabel.innerText = folderName;
        folderMessageDiv.innerHTML = messageText;
      }

      // Function to update counts on badges
      function updateCounters() {
        inboxCountSpan.innerText = folderCounts.inbox;
        archiveCountSpan.innerText = folderCounts.archive;
        deletedCountSpan.innerText = folderCounts.deleted;
      }

      // ---- Add click event listeners to each tab with smooth slider effect ----
      tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
          const tabValue = this.getAttribute('data-tab');
          if (tabValue === currentActiveTab) return; // already active

          // 1. Update active class and internal state
          setActiveTab(tabValue);

          // 2. (Optional) simulate changing counts? Not required, but could.
          // For demo we keep same counts, but you could also call mock folder switch.
          // Just to demonstrate the smooth move, we call updateSliderPosition again inside setActiveTab already.
          // additional: maybe update message detail!
          console.log(`Switched to ${tabValue} with sliding effect`);
        });
      });

      // Jump to Archive button functionality
      if (jumpToArchiveBtn) {
        jumpToArchiveBtn.addEventListener('click', function() {
          // find archive tab and trigger click
          const archiveTab = document.querySelector('.tab[data-tab="archive"]');
          if (archiveTab && currentActiveTab !== 'archive') {
            archiveTab.click(); // this triggers slider animation and changes active
            // Also add a subtle visual feedback on button
            jumpToArchiveBtn.style.transform = 'scale(0.97)';
            setTimeout(() => {
              jumpToArchiveBtn.style.transform = '';
            }, 150);
          } else if (currentActiveTab === 'archive') {
            folderMessageDiv.innerHTML = '✨ Already in Archive folder.';
            setTimeout(() => {
              if (currentActiveTab === 'archive')
                folderMessageDiv.innerHTML = `You have ${folderCounts.archive} archived conversations.`;
            }, 1500);
          }
        });
      }

      // --- Resize handling: reposition slider on window resize (to keep accuracy)
      let resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          const activeTabElem = document.querySelector('.tab.active');
          if (activeTabElem) {
            updateSliderPosition(activeTabElem);
          }
        }, 100);
      });

      // --- INITIAL SETUP: set slider position without animation on first load ---
      const initialActiveTab = document.querySelector('.tab.active');
      if (initialActiveTab) {
        // Immediately set slider without transition for first paint (then enable transitions)
        slider.style.transition = 'none';
        const containerRect = tabsContainer.getBoundingClientRect();
        const tabRect = initialActiveTab.getBoundingClientRect();
        slider.style.width = `${tabRect.width}px`;
        slider.style.left = `${tabRect.left - containerRect.left}px`;
        // Force reflow to apply no-transition style
        slider.offsetHeight;
        // Enable transition back for future moves
        slider.style.transition = 'all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
      }

      // synchronize UI counts & folder label with current active
      updateCounters();
      // set folder description based on the default active tab "inbox"
      setActiveTab('inbox', true); // true = skip extra slider update? but we already positioned

      // Also, if any dynamic counts change demo (just for showing effect we can add test buttons)
      // Optional: small interactive simulation for counts change (not mandatory, but shows button keeps effect)
      const style = document.createElement('style');
      style.textContent = `
            .tab-slider {
                background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
                border: 0.5px solid rgba(59,130,246,0.2);
            }
            .tab {
                transition: color 0.2s ease;
                user-select: none;
            }
            .btn-archive:active {
                transform: scale(0.96);
            }
        `;
      document.head.appendChild(style);
    })();