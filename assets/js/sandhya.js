/**
 * Provider Dashboard - JavaScript Functionality
 * Handles mobile sidebar navigation, active menu states, and interactive features
 */

(function () {
    'use strict';

    // ====== NAVBAR DROPDOWN MENUS ======

    /**
     * Initialize navbar dropdown menus (Notifications, Messages, Profile)
     */
    function initNavbarDropdowns() {
        function toggleMenu(menu, activateCallback) {
            const wasOpen = menu.classList.contains('active');
            closeAllDropdowns();

            if (!wasOpen) {
                menu.classList.add('active');
                if (typeof activateCallback === 'function') {
                    activateCallback();
                }
            }
        }

        // Notifications Dropdown
        const notificationsBtn = document.querySelector('.notifications-btn');
        const notificationsMenu = document.querySelector('.notifications-menu');
        if (notificationsBtn && notificationsMenu) {
            notificationsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(notificationsMenu, () => {
                    const wrapper = this.closest('.notifications-dropdown');
                    if (wrapper) {
                        wrapper.classList.add('active');
                    }
                });
            });
        }

        // Messages Dropdown
        const messagesBtn = document.querySelector('.messages-btn');
        const messagesMenu = document.querySelector('.messages-menu');
        if (messagesBtn && messagesMenu) {
            messagesBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(messagesMenu, () => {
                    const wrapper = this.closest('.messages-dropdown');
                    if (wrapper) {
                        wrapper.classList.add('active');
                    }
                });
            });
        }

        // Profile Dropdown
        const profileBtn = document.querySelector('.profile-btn');
        const profileMenu = document.querySelector('.profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(profileMenu, () => {
                    this.classList.add('active');
                });
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function () {
            closeAllDropdowns();
        });
    }

    /**
     * Close all open dropdowns
     */
    function closeAllDropdowns() {
        const allMenus = document.querySelectorAll('.notifications-menu, .messages-menu, .profile-menu');
        allMenus.forEach(menu => {
            menu.classList.remove('active');
        });

        const wrappers = document.querySelectorAll('.notifications-dropdown, .messages-dropdown');
        wrappers.forEach(wrapper => {
            wrapper.classList.remove('active');
        });
        
        const profileBtn = document.querySelector('.profile-btn');
        if (profileBtn) {
            profileBtn.classList.remove('active');
        }
    }

    /**
     * Add interactivity to notification items
     */
    function initNotificationInteractions() {
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                // Remove unread status
                this.classList.remove('unread');
                closeAllDropdowns();
                console.log('Notification clicked');
                // TODO: Navigate to relevant section
            });
        });

        const markAllReadBtn = document.querySelector('.mark-all-read');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const unreadItems = document.querySelectorAll('.notification-item.unread');
                unreadItems.forEach(item => {
                    item.classList.remove('unread');
                });
                showNotification('All notifications marked as read', 'success');
            });
        }
    }

    /**
     * Add interactivity to message items
     */
    function initMessageInteractions() {
        const messageItems = document.querySelectorAll('.message-item');
        messageItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                this.classList.remove('unread');
                closeAllDropdowns();
                console.log('Message clicked');
                // TODO: Open message conversation
            });
        });

        const newMessageBtn = document.querySelector('.new-message-btn');
        if (newMessageBtn) {
            newMessageBtn.addEventListener('click', function (e) {
                e.preventDefault();
                closeAllDropdowns();
                showNotification('New message form coming soon!', 'info');
            });
        }
    }

    /**
     * Add interactivity to profile menu
     */
    function initProfileInteractions() {
        const profileMenuLinks = document.querySelectorAll('.profile-menu-link');
        profileMenuLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const text = this.textContent.trim();
                closeAllDropdowns();
                showNotification(`Feature: ${text} coming soon!`, 'info');
            });
        });

        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function () {
                if (confirm('Are you sure you want to logout?')) {
                    showNotification('Logging out...', 'info');
                    // TODO: Handle logout
                    setTimeout(() => {
                        window.location.href = '/eduskill-marketplace/auth/login.php';
                    }, 1000);
                }
            });
        }
    }

    // ====== MOBILE SIDEBAR TOGGLE ======
    
    /**
     * Initialize sidebar toggle functionality
     * Works on all devices - desktop, tablet, mobile
     */
    function initMobileSidebar() {
        const sidebar = document.querySelector('.provider-sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');
        const mainContent = document.querySelector('.provider-main-content');
        
        if (!sidebar || !toggleBtn) {
            console.warn('Sidebar or toggle button not found');
            return;
        }

        // Toggle sidebar on hamburger click (all devices)
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isActive = sidebar.classList.toggle('active');
            // Toggle active state on button too (for hamburger animation)
            toggleBtn.classList.toggle('active', isActive);
            this.setAttribute('aria-expanded', isActive);
            // Debug: Add a border to the button when toggled
            if (isActive) {
                toggleBtn.style.border = '2px solid #4186a0';
            } else {
                toggleBtn.style.border = '';
            }
            // console.log('[DEBUG] Sidebar toggled:', isActive);
        });

        // Close sidebar when menu item is clicked (mobile only)
        const menuItems = sidebar.querySelectorAll('.sidebar-menu-item a');
        menuItems.forEach(item => {
            item.addEventListener('click', function (e) {
                // Keep sidebar open on desktop, close on mobile
                const isMobile = window.innerWidth < 768;
                if (isMobile) {
                    sidebar.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Close sidebar when clicking outside (all devices, but intelligent)
        document.addEventListener('click', function (e) {
            const isClickInsideSidebar = sidebar.contains(e.target);
            const isClickOnToggle = toggleBtn.contains(e.target);
            const isMobile = window.innerWidth < 768;
            
            if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
                // On mobile, close sidebar when clicking outside
                // On desktop, allow it to stay open
                if (isMobile) {
                    sidebar.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });

        // Keyboard support - ESC key closes sidebar
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                const isMobile = window.innerWidth < 768;
                if (isMobile) {
                    sidebar.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });

        // Handle resize to manage sidebar state
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                const isMobile = window.innerWidth < 768;
                // Auto-reset sidebar state on resize
                if (!isMobile && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            }, 250);
        });

        console.log('Sidebar toggle initialized successfully');
    }

    // ====== ACTIVE MENU ITEM HIGHLIGHTING ======

    /**
     * Set active menu item based on current page
     * Can be determined by URL, page title, or data attributes
     */
    function setActiveMenuItem() {
        const menuItems = document.querySelectorAll('.sidebar-menu-item');
        const currentPage = getCurrentPageSection();

        menuItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                let linkPage = '';

                try {
                    const url = new URL(link.getAttribute('href'), window.location.origin);
                    // In provider routes, no page query means dashboard.
                    linkPage = url.searchParams.get('page') || 'dashboard';
                } catch (err) {
                    linkPage = '';
                }

                // Exact page match prevents false positives (e.g., dashboard matching everything)
                if (linkPage === currentPage) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            }
        });

        // Default to dashboard if no match found
        if (!document.querySelector('.sidebar-menu-item.active')) {
            const firstMenuItem = menuItems[0];
            if (firstMenuItem) {
                firstMenuItem.classList.add('active');
            }
        }
    }

    /**
     * Determine current page section from URL or page data
     */
    function getCurrentPageSection() {
        // Provider pages use ?page=... in URL.
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page');
        if (page) return page;

        // Backward compatibility for any legacy ?section=... links.
        const section = params.get('section');
        if (section) return section;

        // Default to dashboard
        return 'dashboard';
    }

    // ====== TABLE ROW INTERACTIONS ======

    /**
     * Add interactive features to table rows
     */
    function initTableInteractions() {
        const tables = document.querySelectorAll('.dashboard-table');

        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach((row, index) => {
                // Add hover effect
                row.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateX(2px)';
                });

                row.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateX(0)';
                });

                // Add click handlers for action buttons
                const editBtn = row.querySelector('.edit-btn');
                const deleteBtn = row.querySelector('.delete-btn');

                if (editBtn) {
                    editBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const rowData = getTableRowData(row);
                        handleEditAction(rowData);
                    });
                }

                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const rowData = getTableRowData(row);
                        handleDeleteAction(rowData);
                    });
                }
            });
        });
    }

    /**
     * Extract data from table row
     */
    function getTableRowData(row) {
        const cells = row.querySelectorAll('td');
        const data = {};
        
        cells.forEach((cell, index) => {
            data[index] = cell.textContent.trim();
        });

        return data;
    }

    /**
     * Handle edit action (placeholder)
     */
    function handleEditAction(data) {
        console.log('Edit action triggered for:', data);
        // TODO: Open edit modal/form when backend is ready
        // For now, just show a console message
        showNotification('Edit functionality coming soon!', 'info');
    }

    /**
     * Handle delete action (placeholder)
     */
    function handleDeleteAction(data) {
        console.log('Delete action triggered for:', data);
        // TODO: Show confirmation modal when backend is ready
        const confirmDelete = confirm('Are you sure you want to delete this item?');
        if (confirmDelete) {
            showNotification('Delete functionality coming soon!', 'info');
        }
    }

    // ====== CHART INTERACTIONS ======

    /**
     * Add interactive features to charts
     */
    function initChartInteractions() {
        const chartBars = document.querySelectorAll('.chart-bar');

        chartBars.forEach((bar) => {
            bar.style.cursor = 'pointer';
            bar.title = 'Chart bar (data interactive features coming soon)';

            bar.addEventListener('click', function () {
                const height = this.style.height;
                console.log('Chart bar clicked with height:', height);
                // TODO: Show tooltip or detail view
            });
        });
    }

    // ====== NOTIFICATIONS ======

    /**
     * Show temporary notification message
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `dashboard-notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 130px;
            right: 20px;
            padding: 12px 20px;
            background: ${type === 'success' ? '#d1e7dd' : '#cfe2ff'};
            color: ${type === 'success' ? '#0f5132' : '#084298'};
            border: 1px solid ${type === 'success' ? '#badbcc' : '#b6d4fe'};
            border-radius: 6px;
            font-size: 14px;
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // ====== ANIMATIONS ======

    /**
     * Add animation styles to document
     */
    function initAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }

            .provider-sidebar {
                transition: left 0.3s ease;
            }

            .dashboard-table tbody tr {
                transition: transform 0.2s ease;
            }

            .overview-card {
                transition: all 0.3s ease;
            }

            .action-btn {
                transition: all 0.2s ease;
            }
        `;
        document.head.appendChild(style);
    }

    // ====== SMOOTH SCROLL FOR MENU LINKS ======

    /**
     * Enable smooth scrolling for internal links
     */
    function initSmoothScroll() {
        const menuLinks = document.querySelectorAll('.sidebar-menu-item a');
        
        menuLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                
                // Only handle internal links (starting with #)
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        
                        // Update active state
                        setActiveMenuFromHash(href);
                    }
                }
            });
        });
    }

    /**
     * Update active menu item from hash
     */
    function setActiveMenuFromHash(hash) {
        const menuItems = document.querySelectorAll('.sidebar-menu-item');
        menuItems.forEach(item => {
            const link = item.querySelector('a');
            if (link && link.getAttribute('href') === hash) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // ====== INITIALIZATION ======

    /**
     * Initialize all dashboard features when DOM is ready
     */
    function init() {
        console.log('Initializing Provider Dashboard...');
        
        // Initialize all features
        initAnimations();
        initNavbarDropdowns();
        initNotificationInteractions();
        initMessageInteractions();
        initProfileInteractions();
        initMobileSidebar();
        setActiveMenuItem();
        initTableInteractions();
        initChartInteractions();
        initSmoothScroll();
        
        console.log('Provider Dashboard initialized successfully');
    }

    // Run initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export functions for testing/external use
    window.ProviderDashboard = {
        showNotification,
        getCurrentPageSection,
        setActiveMenuItem,
        initMobileSidebar,
        initNavbarDropdowns,
        closeAllDropdowns
    };

})();
