// courcedetails.js - Professional Course Details Page Interactions v2

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive features
    initializeCurriculumCollapse();
    initializeTabNavigation();
    initializeEnrollButton();
    initializeCartButton();
    initializeWishlistButton();
    initializeShareButtons();
    initializeSmoothScroll();
});

function getCourseDetailsContext() {
    const fallback = {
        courseId: 0,
        courseTitle: 'Course',
        baseUrl: '/',
        promoVideoUrl: '',
        isLearnerLoggedIn: false,
        csrfToken: '',
        learnerApiUrl: '',
        loginUrl: 'auth/login.php'
    };

    if (!window.eduSkillCourseDetailsContext || typeof window.eduSkillCourseDetailsContext !== 'object') {
        return fallback;
    }

    return Object.assign({}, fallback, window.eduSkillCourseDetailsContext || {});
}

function buildCourseDetailsUrl(courseId) {
    const ctx = getCourseDetailsContext();
    const safeId = Number(courseId || 0);
    if (!Number.isFinite(safeId) || safeId <= 0) {
        return '';
    }
    return (ctx.baseUrl || '/') + 'pages/courcedetails.php?id=' + encodeURIComponent(String(safeId));
}

function openCourseDetails(courseId) {
    const targetUrl = buildCourseDetailsUrl(courseId);
    if (!targetUrl) {
        return;
    }
    window.location.href = targetUrl;
}

function postLearnerAction(action, payload) {
    const ctx = getCourseDetailsContext();
    if (!ctx.learnerApiUrl) {
        return Promise.reject(new Error('Learner API URL is missing.'));
    }

    const fd = new FormData();
    fd.set('action', String(action || ''));
    fd.set('csrf_token', String(ctx.csrfToken || ''));

    Object.keys(payload || {}).forEach(function(key) {
        const value = payload[key];
        if (value !== undefined && value !== null) {
            fd.set(key, String(value));
        }
    });

    return fetch(ctx.learnerApiUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: fd
    }).then(function(response) {
        return response.json().catch(function() {
            throw new Error('Invalid JSON response from learner API.');
        }).then(function(payloadResponse) {
            if (!response.ok || !payloadResponse || payloadResponse.ok !== true) {
                throw new Error((payloadResponse && payloadResponse.message) || 'Learner API request failed.');
            }
            return payloadResponse.data || {};
        });
    });
}

// Curriculum Module Collapse/Expand
function initializeCurriculumCollapse() {
    const moduleHeaders = document.querySelectorAll('.module-header');
    
    moduleHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const chevron = this.querySelector('i');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Rotate chevron
            if (chevron) {
                chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    });
}

// Tab Navigation
function initializeTabNavigation() {
    const tabButtons = document.querySelectorAll('.nav-link');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
        });
    });
}

// Enroll Button
function initializeEnrollButton() {
    const enrollBtn = document.querySelector('.btn-enroll-primary');
    const ctx = getCourseDetailsContext();

    if (enrollBtn) {
        enrollBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const baseUrl = String(ctx.baseUrl || '/');
            const courseId = Number(this.getAttribute('data-course-id') || ctx.courseId || 0);
            const paymentUrl = courseId > 0
                ? (baseUrl + 'pages/payment.php?course_id=' + encodeURIComponent(String(courseId)))
                : (baseUrl + 'pages/payment.php');

            window.location.href = paymentUrl;
        });
    }
}

function initializeCartButton() {
    const cartBtn = document.querySelector('.btn-add-cart');
    const ctx = getCourseDetailsContext();
    if (!cartBtn) return;

    cartBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const courseId = Number(this.getAttribute('data-course-id') || ctx.courseId || 0);
        if (courseId <= 0) {
            alert('Course is unavailable at the moment.');
            return;
        }

        if (!ctx.isLearnerLoggedIn) {
            window.location.href = ctx.loginUrl || 'auth/login.php';
            return;
        }

        const originalHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';

        postLearnerAction('cart_add', { course_id: courseId })
            .then(function() {
                cartBtn.innerHTML = '<i class="fas fa-check me-2"></i>Added to Cart';
                window.setTimeout(function() {
                    cartBtn.disabled = false;
                    cartBtn.innerHTML = originalHtml;
                }, 1200);
            })
            .catch(function(error) {
                cartBtn.disabled = false;
                cartBtn.innerHTML = originalHtml;
                alert(error.message || 'Unable to add this course to cart.');
            });
    });
}

// Wishlist Button Toggle
function initializeWishlistButton() {
    const wishlistBtn = document.querySelector('.btn-wishlist');
    const ctx = getCourseDetailsContext();

    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const courseId = Number(this.getAttribute('data-course-id') || ctx.courseId || 0);
            if (courseId <= 0) {
                alert('Course is unavailable at the moment.');
                return;
            }

            if (!ctx.isLearnerLoggedIn) {
                window.location.href = ctx.loginUrl || 'auth/login.php';
                return;
            }

            const self = this;
            const icon = self.querySelector('i');
            const originalHtml = self.innerHTML;

            self.disabled = true;
            self.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            postLearnerAction('wishlist_toggle', { course_id: courseId })
                .then(function(data) {
                    const isAdded = String((data && data.state) || '') === 'added';
                    self.disabled = false;
                    self.innerHTML = originalHtml;

                    self.classList.toggle('active', isAdded);
                    const currentIcon = self.querySelector('i') || icon;
                    if (currentIcon) {
                        currentIcon.classList.toggle('fas', isAdded);
                        currentIcon.classList.toggle('far', !isAdded);
                    }
                })
                .catch(function(error) {
                    self.disabled = false;
                    self.innerHTML = originalHtml;
                    alert(error.message || 'Unable to update wishlist.');
                });
        });
    }
}

// Share Buttons
function initializeShareButtons() {
    const shareButtons = document.querySelectorAll('.share-btn');
    
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('fa-facebook-f')) {
                shareOnFacebook();
            } else if (icon.classList.contains('fa-twitter')) {
                shareOnTwitter();
            } else if (icon.classList.contains('fa-linkedin-in')) {
                shareOnLinkedIn();
            } else if (icon.classList.contains('fa-link')) {
                copyToClipboard();
            }
        });
    });
}

function initializeRelatedCourseCards() {
    const relatedCards = document.querySelectorAll('.courses-grid-related .course-card[data-course-id]');
    if (!relatedCards.length) {
        return;
    }

    relatedCards.forEach(function(card) {
        const courseId = Number(card.getAttribute('data-course-id') || 0);
        if (!Number.isFinite(courseId) || courseId <= 0) {
            return;
        }

        card.style.cursor = 'pointer';

        card.addEventListener('click', function() {
            openCourseDetails(courseId);
        });

        card.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openCourseDetails(courseId);
            }
        });
    });
}

function initializePromoPreview() {
    const ctx = getCourseDetailsContext();
    const playBtn = document.querySelector('.play-btn-overlay');
    if (!playBtn) {
        return;
    }

    const promoUrl = String(playBtn.getAttribute('data-promo-url') || ctx.promoVideoUrl || '').trim();

    if (!promoUrl) {
        playBtn.disabled = true;
        playBtn.style.opacity = '0.6';
        playBtn.style.cursor = 'not-allowed';
        return;
    }

    playBtn.addEventListener('click', function(event) {
        event.preventDefault();
        try {
            const win = window.open(promoUrl, '_blank', 'noopener,noreferrer');
            if (!win) {
                window.location.href = promoUrl;
            }
        } catch (error) {
            window.location.href = promoUrl;
        }
    });
}

function shareOnFacebook() {
    const url = window.location.href;
    const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    window.open(facebookUrl, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = window.location.href;
    const text = 'Check out this amazing React course!';
    const twitterUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
    window.open(twitterUrl, '_blank', 'width=600,height=400');
}

function shareOnLinkedIn() {
    const url = window.location.href;
    const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
    window.open(linkedInUrl, '_blank', 'width=600,height=400');
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

// Smooth Scroll for Anchor Links
function initializeSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initializeRelatedCourseCards();
    initializePromoPreview();
});

// Intersection Observer for Animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe course cards
document.querySelectorAll('.course-card-item').forEach(card => {
    observer.observe(card);
});

// Add fade-in animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Sticky Sidebar on Scroll
window.addEventListener('scroll', function() {
    const sidebar = document.querySelector('.course-sidebar');
    if (sidebar) {
        if (window.innerWidth < 992) {
            sidebar.style.position = 'static';
            sidebar.style.top = 'auto';
            sidebar.style.width = '100%';
            sidebar.style.maxWidth = '100%';
            return;
        }

        const scrollTop = window.scrollY;
        const navHeight = 80;
        
        if (scrollTop > 300) {
            sidebar.style.position = 'sticky';
            sidebar.style.top = navHeight + 'px';
            sidebar.style.width = '100%';
            sidebar.style.maxWidth = '100%';
            sidebar.style.zIndex = '10';
        } else {
            sidebar.style.position = 'sticky';
            sidebar.style.top = '80px';
            sidebar.style.width = '100%';
            sidebar.style.maxWidth = '100%';
        }
    }
});

// Responsive Sidebar
function handleResponsiveSidebar() {
    const sidebar = document.querySelector('.course-sidebar');
    if (sidebar) {
        if (window.innerWidth < 992) {
            sidebar.style.position = 'static';
            sidebar.style.top = 'auto';
            sidebar.style.width = '100%';
            sidebar.style.maxWidth = '100%';
        } else {
            sidebar.style.position = 'sticky';
            sidebar.style.top = '80px';
            sidebar.style.width = '100%';
            sidebar.style.maxWidth = '100%';
        }
    }
}

window.addEventListener('resize', handleResponsiveSidebar);
handleResponsiveSidebar();

// Page Load Animation
window.addEventListener('load', function() {
    document.body.style.opacity = '1';
});

console.log('Course Details Page Loaded Successfully');
