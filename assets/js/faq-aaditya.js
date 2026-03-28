(function () {
    'use strict';

    const faqSection = document.getElementById('faqSection');
    if (!faqSection) return;

    const revealItems = faqSection.querySelectorAll('[data-faq-reveal]');
    const faqItems = faqSection.querySelectorAll('[data-faq-item]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Reveal animation on scroll
    if (revealItems.length && !reduceMotion) {
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
                threshold: 0.15,
                rootMargin: '0px 0px -10% 0px'
            }
        );

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 70, 420)}ms`;
            observer.observe(item);
        });
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }

    if (!faqItems.length) return;

    const closeItem = (item) => {
        const trigger = item.querySelector('.faq-pro-trigger');
        const answer = item.querySelector('.faq-pro-answer-wrap');
        if (!trigger || !answer) return;

        item.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        answer.setAttribute('aria-hidden', 'true');
        answer.style.maxHeight = '0px';
    };

    const openItem = (item) => {
        const trigger = item.querySelector('.faq-pro-trigger');
        const answer = item.querySelector('.faq-pro-answer-wrap');
        if (!trigger || !answer) return;

        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        answer.setAttribute('aria-hidden', 'false');
        answer.style.maxHeight = `${answer.scrollHeight + 12}px`;
    };

    faqItems.forEach((item) => {
        const answer = item.querySelector('.faq-pro-answer-wrap');
        if (!answer) return;
        if (item.classList.contains('is-open')) {
            answer.setAttribute('aria-hidden', 'false');
            answer.style.maxHeight = `${answer.scrollHeight + 12}px`;
        } else {
            answer.setAttribute('aria-hidden', 'true');
            answer.style.maxHeight = '0px';
        }
    });

    faqItems.forEach((item) => {
        const trigger = item.querySelector('.faq-pro-trigger');
        if (!trigger) return;

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-open');

            faqItems.forEach((entry) => {
                if (entry !== item) {
                    closeItem(entry);
                }
            });

            if (isOpen) {
                closeItem(item);
            } else {
                openItem(item);
            }
        });
    });

    window.addEventListener('resize', () => {
        faqItems.forEach((item) => {
            if (!item.classList.contains('is-open')) return;
            const answer = item.querySelector('.faq-pro-answer-wrap');
            if (!answer) return;
            answer.style.maxHeight = `${answer.scrollHeight + 12}px`;
        });
    });
})();

