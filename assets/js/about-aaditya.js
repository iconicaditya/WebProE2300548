(function () {
    'use strict';

    const section = document.getElementById('aboutSection');
    if (!section) return;

    const revealElements = section.querySelectorAll('[data-reveal]');
    const counters = section.querySelectorAll('.about-pro-counter');
    const gallery = section.querySelector('.about-pro-gallery');
    const parallaxLayers = section.querySelectorAll('[data-depth]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Staggered reveal animation
    if (revealElements.length) {
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

        revealElements.forEach((el, index) => {
            el.style.transitionDelay = `${Math.min(index * 70, 490)}ms`;
            revealObserver.observe(el);
        });
    }

    // Counter animation
    const animateCounters = () => {
        counters.forEach((counter) => {
            const target = parseInt(counter.dataset.target || '0', 10);
            const prefix = counter.dataset.prefix || '';
            const suffix = counter.dataset.suffix || '';
            const duration = 1700;
            const startTime = performance.now();

            const update = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(target * eased);
                counter.textContent = `${prefix}${current.toLocaleString()}${suffix}`;

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = `${prefix}${target.toLocaleString()}${suffix}`;
                }
            };

            requestAnimationFrame(update);
        });
    };

    if (counters.length) {
        let hasAnimated = false;
        const counterObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !hasAnimated) {
                        hasAnimated = true;
                        animateCounters();
                        observer.disconnect();
                    }
                });
            },
            {
                threshold: 0.35
            }
        );

        counterObserver.observe(section);
    }

    // Subtle pointer-based parallax for image collage
    if (!reduceMotion && gallery && parallaxLayers.length) {
        gallery.addEventListener('mousemove', (event) => {
            const rect = gallery.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            parallaxLayers.forEach((layer) => {
                const depth = Number(layer.dataset.depth || 0);
                layer.style.setProperty('--parallax-x', `${(-x * depth).toFixed(2)}px`);
                layer.style.setProperty('--parallax-y', `${(-y * depth).toFixed(2)}px`);
            });
        });

        gallery.addEventListener('mouseleave', () => {
            parallaxLayers.forEach((layer) => {
                layer.style.setProperty('--parallax-x', '0px');
                layer.style.setProperty('--parallax-y', '0px');
            });
        });
    }
})();
