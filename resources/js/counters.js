/**
 * Statistic counters that tick up once, when they scroll into view.
 * Numbers are rendered with Persian digits and grouping.
 */

const fa = new Intl.NumberFormat('fa-IR');

export function initCounters() {
    const targets = document.querySelectorAll('[data-counter]');
    if (!targets.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const settle = (el) => {
        el.textContent = fa.format(Number(el.dataset.counter));
    };

    if (reduced || !('IntersectionObserver' in window)) {
        targets.forEach(settle);
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                run(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.4 },
    );

    targets.forEach((el) => {
        el.textContent = fa.format(0);
        observer.observe(el);
    });

    function run(el) {
        const target = Number(el.dataset.counter);
        const duration = Number(el.dataset.counterDuration ?? 1600);
        const start = performance.now();

        function frame(now) {
            const progress = Math.min((now - start) / duration, 1);
            // easeOutExpo — fast start, gentle landing.
            const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

            el.textContent = fa.format(Math.round(target * eased));

            if (progress < 1) requestAnimationFrame(frame);
        }

        requestAnimationFrame(frame);
    }
}
