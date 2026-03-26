/**
 * EduSkill Custom JavaScript
 * Advanced animations and interactive features
 */

// Typing Animation for Hero Section
class TypingAnimation {
    constructor(elementId, text, speed = 70, delay = 500) {
        this.element = document.getElementById(elementId);
        this.fullText = text;
        this.displayText = '';
        this.speed = speed; // milliseconds per character
        this.delay = delay; // initial delay before typing starts
        this.deleteSpeed = 50; // speed of deleting characters
        this.pauseTime = 450; // short pause after completion before restarting
        this.currentIndex = 0;
        this.isTyping = false;
        this.isDeleting = false;
    }

    start() {
        if (!this.element) return;
        
        // Clear initial content
        this.element.textContent = '';
        
        // Add cursor styling
        this.element.classList.add('typing-active');
        
        // Start typing after initial delay
        setTimeout(() => this.type(), this.delay);
    }

    type() {
        if (this.currentIndex < this.fullText.length) {
            this.isTyping = true;
            this.displayText += this.fullText[this.currentIndex];
            this.element.textContent = this.displayText;
            this.currentIndex++;
            
            setTimeout(() => this.type(), this.speed);
        } else {
            this.isTyping = false;
            this.element.classList.remove('typing-active');
            this.element.classList.add('typing-complete');

            // Restart quickly so the completed state doesn't wait too long
            setTimeout(() => this.reset(), this.pauseTime);
        }
    }

    reset() {
        this.currentIndex = 0;
        this.displayText = '';
        this.element.textContent = '';
        this.element.classList.remove('typing-active', 'typing-complete');
        this.start();
    }
}

// Initialize typing animation when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const typingElement = document.getElementById('typingText');
    
    if (typingElement) {
        const typingText = 'the Digital Age';
        const typing = new TypingAnimation('typingText', typingText, 60, 220);
        typing.start();
    }
});

// Optional: Restart animation on scroll into view
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.target.id === 'typingText') {
                // Animation already started, but you can add additional logic here
            }
        });
    }, observerOptions);

    const typingElement = document.getElementById('typingText');
    if (typingElement) {
        observer.observe(typingElement);
    }
});

// Learner dashboard interactions
(function () {
    'use strict';

    function isLearnerPage() {
        return window.location.pathname.toLowerCase().includes('/learner/');
    }

    function closeDropdowns() {
        document.querySelectorAll('.notifications-menu, .messages-menu, .profile-menu').forEach(menu => {
            menu.classList.remove('active');
        });

        const profileBtn = document.querySelector('.profile-btn');
        if (profileBtn) {
            profileBtn.classList.remove('active');
        }
    }

    function initDropdowns() {
        function toggleMenu(menu, button) {
            const wasOpen = menu.classList.contains('active');
            closeDropdowns();

            if (!wasOpen) {
                menu.classList.add('active');
                if (button && button.classList.contains('profile-btn')) {
                    button.classList.add('active');
                }
            }
        }

        const notificationsBtn = document.querySelector('.notifications-btn');
        const notificationsMenu = document.querySelector('.notifications-menu');
        if (notificationsBtn && notificationsMenu) {
            notificationsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(notificationsMenu, notificationsBtn);
            });
        }

        const messagesBtn = document.querySelector('.messages-btn');
        const messagesMenu = document.querySelector('.messages-menu');
        if (messagesBtn && messagesMenu) {
            messagesBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(messagesMenu, messagesBtn);
            });
        }

        const profileBtn = document.querySelector('.profile-btn');
        const profileMenu = document.querySelector('.profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(profileMenu, profileBtn);
            });
        }

        document.addEventListener('click', closeDropdowns);
    }

    function initSidebarToggle() {
        const sidebar = document.querySelector('.provider-sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');

        if (!sidebar || !toggleBtn) {
            return;
        }

        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = sidebar.classList.toggle('active');
            toggleBtn.classList.toggle('active', isOpen);
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        const sidebarLinks = sidebar.querySelectorAll('.sidebar-menu-item a');
        sidebarLinks.forEach(link => {
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

            const clickedInsideSidebar = sidebar.contains(e.target);
            const clickedToggle = toggleBtn.contains(e.target);
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

    function getLearnerContext() {
        const fallback = {
            apiUrl: '',
            csrfToken: '',
            loginUrl: 'auth/login.php'
        };

        if (!window.eduSkillLearnerContext || typeof window.eduSkillLearnerContext !== 'object') {
            return fallback;
        }

        return Object.assign({}, fallback, window.eduSkillLearnerContext || {});
    }

    function learnerApiRequest(action, payload) {
        const ctx = getLearnerContext();
        if (!ctx.apiUrl) {
            return Promise.reject(new Error('Learner API is unavailable.'));
        }

        const fd = new FormData();
        fd.set('action', String(action || ''));
        fd.set('csrf_token', String(ctx.csrfToken || ''));

        Object.keys(payload || {}).forEach(function (key) {
            const value = payload[key];
            if (value !== undefined && value !== null) {
                fd.set(key, String(value));
            }
        });

        return fetch(ctx.apiUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        }).then(function (response) {
            return response.json().catch(function () {
                throw new Error('Invalid API response.');
            }).then(function (apiPayload) {
                if (!response.ok || !apiPayload || apiPayload.ok !== true) {
                    throw new Error((apiPayload && apiPayload.message) || 'Request failed.');
                }
                return apiPayload.data || {};
            });
        });
    }

    function initLearnerActionButtons() {
        if (!isLearnerPage()) {
            return;
        }

        const ctx = getLearnerContext();
        if (!ctx.apiUrl) {
            return;
        }

        document.addEventListener('click', function (event) {
            const actionEl = event.target.closest('[data-action]');
            if (!actionEl) {
                return;
            }

            const action = String(actionEl.getAttribute('data-action') || '').trim();
            if (!action) {
                return;
            }

            if (action === 'cart-remove') {
                event.preventDefault();
                const courseId = Number(actionEl.getAttribute('data-course-id') || 0);
                if (courseId <= 0) {
                    return;
                }

                actionEl.disabled = true;
                learnerApiRequest('cart_remove', { course_id: courseId })
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function (error) {
                        actionEl.disabled = false;
                        alert(error.message || 'Unable to remove item from cart.');
                    });
                return;
            }

            if (action === 'wishlist-add-to-cart') {
                event.preventDefault();
                const courseId = Number(actionEl.getAttribute('data-course-id') || 0);
                if (courseId <= 0) {
                    return;
                }

                actionEl.disabled = true;
                learnerApiRequest('cart_add', { course_id: courseId })
                    .then(function () {
                        return learnerApiRequest('wishlist_toggle', { course_id: courseId }).catch(function () {
                            return {};
                        });
                    })
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function (error) {
                        actionEl.disabled = false;
                        alert(error.message || 'Unable to move this course to cart.');
                    });
                return;
            }

            if (action === 'notification-mark-read') {
                const notificationId = Number(actionEl.getAttribute('data-notification-id') || 0);
                if (notificationId <= 0) {
                    return;
                }

                learnerApiRequest('notification_mark_read', { notification_id: notificationId })
                    .then(function () {
                        actionEl.classList.remove('unread');
                    })
                    .catch(function () {
                        // Keep UI silent for dropdown quick actions.
                    });
                return;
            }

            if (action === 'notifications-mark-all-read') {
                event.preventDefault();
                learnerApiRequest('notification_mark_all_read', {})
                    .then(function () {
                        document.querySelectorAll('.notification-item.unread').forEach(function (item) {
                            item.classList.remove('unread');
                        });
                    })
                    .catch(function (error) {
                        alert(error.message || 'Unable to mark notifications as read.');
                    });
                return;
            }

            if (action === 'message-mark-read') {
                const messageId = Number(actionEl.getAttribute('data-message-id') || 0);
                if (messageId <= 0) {
                    return;
                }

                learnerApiRequest('message_mark_read', { message_id: messageId })
                    .then(function () {
                        actionEl.classList.remove('unread');
                    })
                    .catch(function () {
                        // Keep UI silent for dropdown quick actions.
                    });
            }
        });
    }

    // Always initialize sidebar toggle for both provider and learner dashboards
    function initDashboardSidebarAndDropdowns() {
        initDropdowns();
        initSidebarToggle();
        initLearnerActionButtons();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardSidebarAndDropdowns);
    } else {
        initDashboardSidebarAndDropdowns();
    }
})();
