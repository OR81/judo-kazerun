/**
 * Back-to-top button. Appears once the visitor is a screenful down the page.
 */

export function initBackToTop() {
    const button = document.querySelector('[data-back-to-top]');
    if (!button) return;

    const show = () => {
        const past = window.scrollY > window.innerHeight * 0.8;
        button.classList.toggle('translate-y-24', !past);
        button.classList.toggle('opacity-0', !past);
        // Keep it out of the tab order while it's invisible.
        button.tabIndex = past ? 0 : -1;
        button.setAttribute('aria-hidden', past ? 'false' : 'true');
    };

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            show();
            ticking = false;
        });
    };

    show();
    window.addEventListener('scroll', onScroll, { passive: true });

    button.addEventListener('click', () => {
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });

        // Send focus somewhere sensible instead of leaving it on a vanishing button.
        const target = document.querySelector('#main') ?? document.body;
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
    });
}
