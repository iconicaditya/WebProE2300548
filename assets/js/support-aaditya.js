(function () {
    'use strict';

    const supportPage = document.querySelector('.support-page');
    if (!supportPage) return;

    const revealElements = supportPage.querySelectorAll('[data-support-reveal]');
    const searchInput = supportPage.querySelector('#supportSearchInput');
    const cards = supportPage.querySelectorAll('[data-support-item]');
    const emptyState = supportPage.querySelector('#supportEmptyState');

    // Smooth right-to-left reveal animation
    if (revealElements.length) {
        const observer = new IntersectionObserver(
            (entries, io) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.14,
                rootMargin: '0px 0px -8% 0px'
            }
        );

        revealElements.forEach((element, index) => {
            element.style.transitionDelay = `${Math.min(index * 60, 420)}ms`;
            observer.observe(element);
        });
    }

    // Simple support topic filter
    const applyFilter = () => {
        if (!cards.length) return;

        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach((card) => {
            const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();
            const isMatch = query === '' || keywords.includes(query);

            card.hidden = !isMatch;
            if (isMatch) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.hidden = visibleCount !== 0;
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    applyFilter();
})();
