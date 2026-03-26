(function () {
    'use strict';

    const section = document.getElementById('testimonialsSection');
    if (!section) return;

    const revealItems = section.querySelectorAll('[data-testi-reveal]');
    const viewport = section.querySelector('#testiViewport');
    const track = section.querySelector('.testi-pro-track');
    const prevButton = section.querySelector('.testi-pro-nav--prev');
    const nextButton = section.querySelector('.testi-pro-nav--next');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Reveal animation on scroll
    if (revealItems.length) {
        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.18,
                rootMargin: '0px 0px -10% 0px'
            }
        );

        revealItems.forEach((el, index) => {
            el.style.transitionDelay = `${Math.min(index * 65, 520)}ms`;
            revealObserver.observe(el);
        });
    }

    if (!viewport || !track) return;

    const getScrollAmount = () => {
        const firstCard = track.querySelector('.testi-pro-card');
        if (!firstCard) return Math.max(280, viewport.clientWidth * 0.9);

        const cardWidth = firstCard.getBoundingClientRect().width;
        const style = window.getComputedStyle(track);
        const gap = parseFloat(style.columnGap || style.gap || '16') || 16;
        return cardWidth + gap;
    };

    const updateNavState = () => {
        if (!prevButton || !nextButton) return;

        const maxScrollLeft = viewport.scrollWidth - viewport.clientWidth - 1;
        prevButton.disabled = viewport.scrollLeft <= 1;
        nextButton.disabled = viewport.scrollLeft >= maxScrollLeft;
    };

    const scrollCards = (direction) => {
        viewport.scrollBy({
            left: direction * getScrollAmount(),
            behavior: 'smooth'
        });
    };

    if (prevButton) {
        prevButton.addEventListener('click', () => scrollCards(-1));
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => scrollCards(1));
    }

    viewport.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateNavState);
    });

    window.addEventListener('resize', updateNavState);
    updateNavState();

    // Auto-scroll carousel effect (paused on hover/focus)
    let autoScrollTimer = null;

    const stopAutoScroll = () => {
        if (autoScrollTimer) {
            clearInterval(autoScrollTimer);
            autoScrollTimer = null;
        }
    };

    const startAutoScroll = () => {
        if (reduceMotion) return;

        stopAutoScroll();
        autoScrollTimer = setInterval(() => {
            const maxScrollLeft = viewport.scrollWidth - viewport.clientWidth - 1;
            const atEnd = viewport.scrollLeft >= maxScrollLeft;

            if (atEnd) {
                viewport.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                scrollCards(1);
            }
        }, 5200);
    };

    section.addEventListener('mouseenter', stopAutoScroll);
    section.addEventListener('mouseleave', startAutoScroll);
    section.addEventListener('focusin', stopAutoScroll);
    section.addEventListener('focusout', startAutoScroll);

    startAutoScroll();
})();
