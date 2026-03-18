// courcedetails.js - Professional Course Details Page Interactions v2

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive features
    initializeCurriculumCollapse();
    initializeTabNavigation();
    initializeEnrollButton();
    initializeWishlistButton();
    initializeShareButtons();
    initializeSmoothScroll();
});

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
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'payment.php';
        });
    }
}

// Wishlist Button Toggle
function initializeWishlistButton() {
    const wishlistBtn = document.querySelector('.btn-outline-secondary');
    
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.style.background = '#f0f4f8';
                this.style.borderColor = var(--primary-color);
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.style.background = '';
                this.style.borderColor = '';
            }
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

// Play Button for Video
document.addEventListener('DOMContentLoaded', function() {
    const playButton = document.querySelector('.play-button');
    
    if (playButton) {
        playButton.addEventListener('click', function() {
            console.log('Play video');
            // Add your video player logic here
            alert('Video player coming soon!');
        });
    }
});

// Course Card Hover Effects
document.addEventListener('DOMContentLoaded', function() {
    const courseCards = document.querySelectorAll('.course-card-item');
    
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('click', function() {
            // Navigate to course details
            console.log('Navigate to course');
        });
    });
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
        const scrollTop = window.scrollY;
        const navHeight = 80;
        
        if (scrollTop > 300) {
            sidebar.style.position = 'fixed';
            sidebar.style.top = navHeight + 'px';
            sidebar.style.width = 'calc(33.333% - 16px)';
            sidebar.style.zIndex = '100';
        } else {
            sidebar.style.position = 'sticky';
            sidebar.style.top = '80px';
            sidebar.style.width = 'auto';
        }
    }
});

// Responsive Sidebar
function handleResponsiveSidebar() {
    const sidebar = document.querySelector('.course-sidebar');
    if (window.innerWidth < 992) {
        if (sidebar) {
            sidebar.style.position = 'static';
            sidebar.style.top = 'auto';
            sidebar.style.width = 'auto';
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
