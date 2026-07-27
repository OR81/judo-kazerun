/**
 * Focus management for overlay UI (drawer, search, lightbox, modal).
 *
 * Keeps Tab inside the open surface, restores focus to whatever opened it, and
 * locks background scrolling without the page jumping as the scrollbar vanishes.
 */

const FOCUSABLE = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

export function focusableWithin(container) {
    return Array.from(container.querySelectorAll(FOCUSABLE)).filter(
        (el) => el.offsetParent !== null || el === document.activeElement,
    );
}

let lockCount = 0;
let savedPadding = '';

export function lockScroll() {
    if (lockCount++ > 0) return;

    // Compensate for the scrollbar so the layout doesn't shift on open.
    const gap = window.innerWidth - document.documentElement.clientWidth;
    savedPadding = document.body.style.paddingInlineEnd;
    if (gap > 0) document.body.style.paddingInlineEnd = `${gap}px`;
    document.body.style.overflow = 'hidden';
}

export function unlockScroll() {
    if (lockCount === 0 || --lockCount > 0) return;

    document.body.style.overflow = '';
    document.body.style.paddingInlineEnd = savedPadding;
}

/**
 * Trap focus inside `container` until the returned release() is called.
 */
export function trapFocus(container, { onEscape, initialFocus } = {}) {
    const previouslyFocused = document.activeElement;

    const onKeydown = (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            onEscape?.();
            return;
        }

        if (event.key !== 'Tab') return;

        const items = focusableWithin(container);
        if (!items.length) {
            event.preventDefault();
            return;
        }

        const first = items[0];
        const last = items[items.length - 1];
        const active = document.activeElement;

        if (event.shiftKey && (active === first || !container.contains(active))) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && active === last) {
            event.preventDefault();
            first.focus();
        }
    };

    document.addEventListener('keydown', onKeydown, true);

    const target = initialFocus ?? focusableWithin(container)[0] ?? container;
    // Wait a frame so the element is painted and actually focusable.
    requestAnimationFrame(() => target.focus({ preventScroll: true }));

    return function release({ restoreFocus = true } = {}) {
        document.removeEventListener('keydown', onKeydown, true);
        if (restoreFocus && previouslyFocused instanceof HTMLElement) {
            previouslyFocused.focus({ preventScroll: true });
        }
    };
}
