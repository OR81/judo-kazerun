/**
 * Scroll-reveal. Elements carrying .reveal fade up once as they enter view.
 *
 * The .reveal utility only hides content when <html> has the `js` class, which the
 * head script sets before paint — so a visitor without JS never sees blank sections.
 */

export function initReveal() {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                // Stagger siblings so a grid lands as a wave rather than all at once.
                const delay = Number(entry.target.dataset.revealDelay ?? 0);
                setTimeout(() => entry.target.classList.add('is-visible'), delay);

                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '0px 0px -10% 0px', threshold: 0.1 },
    );

    targets.forEach((el) => observer.observe(el));
}
