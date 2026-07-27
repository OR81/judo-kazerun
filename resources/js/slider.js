/**
 * Hero slider.
 *
 * Built as an ARIA carousel: a labelled region, slides marked aria-hidden when
 * off-screen, real buttons for every control, and autoplay that pauses on hover,
 * on focus, when the tab is hidden, and whenever reduced motion is requested.
 */

export function initSlider() {
    document.querySelectorAll('[data-slider]').forEach(setup);
}

function setup(root) {
    const track = root.querySelector('[data-slider-track]');
    const slides = Array.from(root.querySelectorAll('[data-slide]'));
    if (!track || slides.length === 0) return;

    const dots = Array.from(root.querySelectorAll('[data-slide-dot]'));
    const prev = root.querySelector('[data-slide-prev]');
    const next = root.querySelector('[data-slide-next]');
    const status = root.querySelector('[data-slider-status]');

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const interval = Number(root.dataset.sliderInterval ?? 6500);

    let index = 0;
    let timer = null;
    let paused = false;

    function render() {
        // RTL: slide N sits N widths to the *right* of the first one.
        track.style.transform = `translateX(${index * 100}%)`;

        slides.forEach((slide, i) => {
            const active = i === index;
            slide.setAttribute('aria-hidden', String(!active));
            // Keep links inside hidden slides out of the tab order.
            slide.querySelectorAll('a, button').forEach((el) => {
                el.tabIndex = active ? 0 : -1;
            });
        });

        dots.forEach((dot, i) => {
            const active = i === index;
            dot.setAttribute('aria-current', active ? 'true' : 'false');
            dot.classList.toggle('w-8', active);
            dot.classList.toggle('bg-accent', active);
            dot.classList.toggle('w-2.5', !active);
            dot.classList.toggle('bg-white/45', !active);
        });

        if (status) status.textContent = `اسلاید ${index + 1} از ${slides.length}`;
    }

    const goTo = (i) => {
        index = (i + slides.length) % slides.length;
        render();
    };

    function play() {
        stop();
        if (paused || reduced.matches || slides.length < 2) return;
        timer = setInterval(() => goTo(index + 1), interval);
    }

    function stop() {
        if (timer) clearInterval(timer);
        timer = null;
    }

    prev?.addEventListener('click', () => {
        goTo(index - 1);
        play();
    });

    next?.addEventListener('click', () => {
        goTo(index + 1);
        play();
    });

    dots.forEach((dot, i) =>
        dot.addEventListener('click', () => {
            goTo(i);
            play();
        }),
    );

    // Arrow keys are mirrored for RTL: Left advances, Right goes back.
    root.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goTo(index + 1);
            play();
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            goTo(index - 1);
            play();
        }
    });

    const hold = () => {
        paused = true;
        stop();
    };
    const resume = () => {
        paused = false;
        play();
    };

    root.addEventListener('mouseenter', hold);
    root.addEventListener('mouseleave', resume);
    root.addEventListener('focusin', hold);
    root.addEventListener('focusout', (event) => {
        if (!root.contains(event.relatedTarget)) resume();
    });

    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : play()));
    reduced.addEventListener('change', play);

    /* Touch swipe. Horizontal drags move the carousel; anything closer to
       vertical is left alone so the page can still scroll. */
    let startX = 0;
    let startY = 0;
    let dragging = false;

    root.addEventListener(
        'touchstart',
        (event) => {
            startX = event.touches[0].clientX;
            startY = event.touches[0].clientY;
            dragging = true;
            hold();
        },
        { passive: true },
    );

    root.addEventListener(
        'touchend',
        (event) => {
            if (!dragging) return;
            dragging = false;

            const dx = event.changedTouches[0].clientX - startX;
            const dy = event.changedTouches[0].clientY - startY;

            if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy)) {
                // RTL: swiping right-to-left moves forward.
                goTo(dx < 0 ? index + 1 : index - 1);
            }

            resume();
        },
        { passive: true },
    );

    render();
    play();
}
