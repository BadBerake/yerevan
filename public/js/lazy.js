/**
 * Lazy Loading Utility for Yerevango
 * Handles native lazy loading fallback and background image lazy loading.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Background Image Lazy Loading
    const lazyBackgrounds = [].slice.call(document.querySelectorAll('.lazy-bg'));

    if ('IntersectionObserver' in window) {
        let lazyBackgroundObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const src = el.dataset.src;
                    if (src) {
                        el.style.backgroundImage = `url('${src}')`;
                        el.classList.add('lazy-bg-loaded');
                    }
                    observer.unobserve(entry.target);
                }
            });
        });

        lazyBackgrounds.forEach((lazyBackground) => {
            lazyBackgroundObserver.observe(lazyBackground);
        });
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyBackgrounds.forEach((el) => {
            const src = el.dataset.src;
            if (src) {
                el.style.backgroundImage = `url('${src}')`;
            }
        });
    }
});
