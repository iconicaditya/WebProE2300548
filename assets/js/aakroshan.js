// Admin Officer Dashboard JavaScript
// Handles sidebar toggle and navbar dropdown interactions

(function () {
    'use strict';

    function initSidebarToggle() {
        var sidebar = document.querySelector('.provider-sidebar');
        var toggleBtn = document.querySelector('.sidebar-toggle-btn');

        if (!sidebar || !toggleBtn) {
            return;
        }

        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = sidebar.classList.toggle('active');
            toggleBtn.classList.toggle('active', isOpen);
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        var sidebarLinks = sidebar.querySelectorAll('.sidebar-menu-item a');
        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    toggleBtn.classList.remove('active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth > 768) {
                return;
            }

            var clickedInsideSidebar = sidebar.contains(e.target);
            var clickedToggle = toggleBtn.contains(e.target);
            if (!clickedInsideSidebar && !clickedToggle && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                toggleBtn.classList.remove('active');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                sidebar.classList.remove('active');
                toggleBtn.classList.remove('active');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function initNotificationsDropdown() {
        var notificationsBtn = document.querySelector('.notifications-btn');
        var notificationsMenu = document.querySelector('.notifications-menu');

        if (!notificationsBtn || !notificationsMenu) {
            return;
        }

        notificationsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            notificationsMenu.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
            if (!notificationsMenu.contains(e.target) && !notificationsBtn.contains(e.target)) {
                notificationsMenu.classList.remove('active');
            }
        });
    }

    function initAdminDashboard() {
        initSidebarToggle();
        initNotificationsDropdown();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminDashboard);
    } else {
        initAdminDashboard();
    }
})();
