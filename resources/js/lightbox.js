import { trapFocus, lockScroll, unlockScroll } from './focus-trap';

/**
 * Gallery lightbox for photos and videos.
 *
 * Built lazily on first open so the gallery page pays nothing until used.
 */

let overlay = null;
let items = [];
let index = 0;
let release = null;

function build() {
    overlay = document.createElement('div');
    overlay.className =
        'fixed inset-0 z-[70] hidden items-center justify-center bg-ink/92 p-4 backdrop-blur-sm sm:p-8';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'نمایشگر تصاویر');

    overlay.innerHTML = `
        <button type="button" data-lb-close
            class="absolute top-4 inset-inline-end-4 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
            aria-label="بستن نمایشگر">
            <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
        </button>

        <button type="button" data-lb-prev
            class="absolute inset-inline-end-2 sm:inset-inline-end-6 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
            aria-label="تصویر قبلی">
            <i class="fa-solid fa-chevron-right text-lg" aria-hidden="true"></i>
        </button>

        <button type="button" data-lb-next
            class="absolute inset-inline-start-2 sm:inset-inline-start-6 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
            aria-label="تصویر بعدی">
            <i class="fa-solid fa-chevron-left text-lg" aria-hidden="true"></i>
        </button>

        <figure class="flex max-h-full w-full max-w-5xl flex-col items-center gap-4">
            <div data-lb-stage class="flex max-h-[75vh] w-full items-center justify-center"></div>
            <figcaption class="text-center text-sm text-white/80">
                <span data-lb-caption class="block"></span>
                <span data-lb-counter class="mt-1 block text-xs text-white/55"></span>
            </figcaption>
        </figure>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('[data-lb-close]').addEventListener('click', close);
    overlay.querySelector('[data-lb-prev]').addEventListener('click', () => show(index - 1));
    overlay.querySelector('[data-lb-next]').addEventListener('click', () => show(index + 1));

    // Clicking the backdrop (but not the figure) dismisses.
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) close();
    });

    overlay.addEventListener('keydown', (event) => {
        // Mirrored for RTL, matching the slider.
        if (event.key === 'ArrowLeft') show(index + 1);
        if (event.key === 'ArrowRight') show(index - 1);
    });
}

function show(next) {
    index = (next + items.length) % items.length;

    const item = items[index];
    const stage = overlay.querySelector('[data-lb-stage]');
    const isVideo = item.dataset.lightboxType === 'video';

    stage.innerHTML = '';

    if (isVideo) {
        const video = document.createElement('video');
        video.src = item.dataset.lightboxSrc;
        video.controls = true;
        video.autoplay = true;
        video.className = 'max-h-[75vh] w-auto rounded-panel';
        stage.appendChild(video);
    } else {
        const img = document.createElement('img');
        img.src = item.dataset.lightboxSrc;
        img.alt = item.dataset.lightboxCaption ?? '';
        img.className = 'max-h-[75vh] w-auto rounded-panel object-contain';
        stage.appendChild(img);
    }

    overlay.querySelector('[data-lb-caption]').textContent = item.dataset.lightboxCaption ?? '';
    overlay.querySelector('[data-lb-counter]').textContent = `${index + 1} از ${items.length}`;
}

function open(startIndex) {
    if (!overlay) build();

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');

    show(startIndex);
    lockScroll();
    release = trapFocus(overlay, { onEscape: close });
}

function close() {
    overlay.querySelector('[data-lb-stage]').innerHTML = '';
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');

    unlockScroll();
    release?.();
    release = null;
}

export function initLightbox() {
    items = Array.from(document.querySelectorAll('[data-lightbox]'));
    if (!items.length) return;

    items.forEach((item, i) => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            open(i);
        });
    });
}
