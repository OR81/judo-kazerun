import { trapFocus, lockScroll, unlockScroll } from './focus-trap';

/**
 * Sticky header, mega menu, mobile drawer and search overlay.
 *
 * The mega menu opens on hover for pointer users but is driven by real
 * button + aria-expanded semantics, so it works identically from the keyboard.
 */

const DESKTOP = '(min-width: 1024px)';

/* ------------------------------------------------------------------ header */

function initStickyHeader() {
    const header = document.querySelector('[data-header]');
    if (!header) return;

    let ticking = false;

    const update = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 12);
        ticking = false;
    };

    update();
    window.addEventListener(
        'scroll',
        () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(update);
        },
        { passive: true },
    );
}

/* --------------------------------------------------------------- mega menu */

function initMegaMenu() {
    const items = Array.from(document.querySelectorAll('[data-mega]'));
    if (!items.length) return;

    const desktop = window.matchMedia(DESKTOP);
    let openItem = null;
    let hoverTimer;

    const panelOf = (item) => item.querySelector('[data-mega-panel]');
    const triggerOf = (item) => item.querySelector('[data-mega-trigger]');

    function open(item) {
        if (openItem && openItem !== item) close(openItem);

        openItem = item;
        triggerOf(item)?.setAttribute('aria-expanded', 'true');
        panelOf(item)?.classList.remove('invisible', 'opacity-0', 'translate-y-2');
    }

    function close(item, { focusTrigger = false } = {}) {
        const trigger = triggerOf(item);
        trigger?.setAttribute('aria-expanded', 'false');
        panelOf(item)?.classList.add('invisible', 'opacity-0', 'translate-y-2');

        if (openItem === item) openItem = null;
        if (focusTrigger) trigger?.focus();
    }

    const closeAll = () => items.forEach((item) => close(item));

    items.forEach((item) => {
        const trigger = triggerOf(item);
        if (!trigger) return;

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openItem === item ? close(item) : open(item);
        });

        item.addEventListener('mouseenter', () => {
            if (!desktop.matches) return;
            clearTimeout(hoverTimer);
            open(item);
        });

        item.addEventListener('mouseleave', () => {
            if (!desktop.matches) return;
            // Small grace period so a diagonal mouse path doesn't snap it shut.
            clearTimeout(hoverTimer);
            hoverTimer = setTimeout(() => close(item), 160);
        });

        item.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && openItem === item) {
                event.preventDefault();
                close(item, { focusTrigger: true });
            }

            if (event.key === 'ArrowDown' && document.activeElement === trigger) {
                event.preventDefault();
                open(item);
                panelOf(item)?.querySelector('a, button')?.focus();
            }
        });
    });

    // Clicking or tabbing away closes whatever is open.
    document.addEventListener('click', (event) => {
        if (openItem && !openItem.contains(event.target)) close(openItem);
    });

    document.addEventListener('focusin', (event) => {
        if (openItem && !openItem.contains(event.target)) close(openItem);
    });

    desktop.addEventListener('change', closeAll);
}

/* ------------------------------------------------------------------ drawer */

function initDrawer() {
    const drawer = document.querySelector('[data-drawer]');
    const openButton = document.querySelector('[data-drawer-open]');
    if (!drawer || !openButton) return;

    const panel = drawer.querySelector('[data-drawer-panel]');
    let release = null;

    function open() {
        drawer.classList.remove('pointer-events-none');
        drawer.removeAttribute('inert');
        drawer.querySelector('[data-drawer-backdrop]')?.classList.remove('opacity-0');
        panel?.classList.remove('drawer-closed');

        openButton.setAttribute('aria-expanded', 'true');
        lockScroll();
        release = trapFocus(drawer, { onEscape: close });
    }

    function close() {
        drawer.classList.add('pointer-events-none');
        drawer.querySelector('[data-drawer-backdrop]')?.classList.add('opacity-0');
        panel?.classList.add('drawer-closed');

        openButton.setAttribute('aria-expanded', 'false');
        unlockScroll();
        release?.();
        release = null;

        // Hide from AT once the transition has run.
        setTimeout(() => drawer.setAttribute('inert', ''), 300);
    }

    openButton.addEventListener('click', open);
    drawer.querySelectorAll('[data-drawer-close]').forEach((el) => el.addEventListener('click', close));

    // Any in-drawer navigation should dismiss it.
    panel?.addEventListener('click', (event) => {
        if (event.target.closest('a')) close();
    });

    // Collapsible sections inside the drawer.
    drawer.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const expanded = trigger.getAttribute('aria-expanded') === 'true';
            trigger.setAttribute('aria-expanded', String(!expanded));
            document.getElementById(trigger.getAttribute('aria-controls'))?.classList.toggle('hidden', expanded);
        });
    });

    window.matchMedia(DESKTOP).addEventListener('change', (event) => {
        if (event.matches && release) close();
    });
}

/* ------------------------------------------------------------------ search */

function initSearch() {
    const overlay = document.querySelector('[data-search]');
    if (!overlay) return;

    const input = overlay.querySelector('input[type="search"]');
    let release = null;

    function open() {
        overlay.classList.remove('pointer-events-none', 'opacity-0');
        overlay.removeAttribute('inert');
        lockScroll();
        release = trapFocus(overlay, { onEscape: close, initialFocus: input });
    }

    function close() {
        overlay.classList.add('pointer-events-none', 'opacity-0');
        unlockScroll();
        release?.();
        release = null;
        setTimeout(() => overlay.setAttribute('inert', ''), 250);
    }

    document.querySelectorAll('[data-search-open]').forEach((el) => el.addEventListener('click', open));
    overlay.querySelectorAll('[data-search-close]').forEach((el) => el.addEventListener('click', close));

    document.addEventListener('keydown', (event) => {
        const typing = /^(INPUT|TEXTAREA|SELECT)$/.test(event.target.tagName) || event.target.isContentEditable;

        if (event.key === '/' && !typing && !release) {
            event.preventDefault();
            open();
        }

        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            release ? close() : open();
        }
    });
}

export function initNav() {
    initStickyHeader();
    initMegaMenu();
    initDrawer();
    initSearch();
}
