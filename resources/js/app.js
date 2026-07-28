import { initNav } from './nav';
import { initReveal } from './reveal';
import { initBackToTop } from './back-to-top';
import { initToast } from './toast';

/**
 * Public-site entry point. Everything here is dependency-free vanilla JS —
 * Livewire and Alpine ship only inside the Filament panel at /admin and never
 * reach this bundle.
 *
 * Heavier, page-specific modules (hero slider, lightbox, registration wizard,
 * schedule filters) are pulled in dynamically so a visitor downloads only what
 * the page they're actually on needs.
 */
function boot() {
    initNav();
    initReveal();
    initBackToTop();
    initToast();

    if (document.querySelector('[data-hall]')) {
        import('./hall').then((m) => m.initHall());
    }

    if (document.querySelector('[data-slider]')) {
        import('./slider').then((m) => m.initSlider());
    }

    if (document.querySelector('[data-lightbox]')) {
        import('./lightbox').then((m) => m.initLightbox());
    }

    if (document.querySelector('[data-filters]')) {
        import('./filters').then((m) => m.initFilters());
    }

    if (document.querySelector('[data-wizard]')) {
        import('./wizard').then((m) => m.initWizard());
    }

    if (document.querySelector('[data-counter]')) {
        import('./counters').then((m) => m.initCounters());
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
    boot();
}
