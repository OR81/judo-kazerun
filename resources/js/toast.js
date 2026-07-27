/**
 * Toast notifications.
 *
 * Server-side flash messages are rendered into [data-toast-seed] by Blade and
 * picked up on load; client code can raise its own with `toast(message, type)`.
 *
 * The live region is polite for success/info and assertive for errors, so a
 * screen reader interrupts only when something actually went wrong.
 */

const ICONS = {
    success: 'fa-circle-check',
    error: 'fa-circle-exclamation',
    warning: 'fa-triangle-exclamation',
    info: 'fa-circle-info',
};

const TONES = {
    success: 'border-emerald-500/30 bg-emerald-50 text-emerald-900',
    error: 'border-crimson-500/30 bg-crimson-50 text-crimson-900',
    warning: 'border-gold-500/30 bg-gold-50 text-gold-900',
    info: 'border-line bg-surface text-copy',
};

function container() {
    let el = document.getElementById('toast-region');

    if (!el) {
        el = document.createElement('div');
        el.id = 'toast-region';
        el.className = 'fixed inset-inline-start-0 bottom-6 z-[60] flex flex-col items-start gap-3 px-4 sm:px-6';
        document.body.appendChild(el);
    }

    return el;
}

export function toast(message, type = 'info', { timeout = 6000 } = {}) {
    if (!message) return;

    const node = document.createElement('output');
    node.setAttribute('role', type === 'error' ? 'alert' : 'status');
    node.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    node.className = [
        'pointer-events-auto flex max-w-sm items-start gap-3 rounded-card border px-4 py-3',
        'shadow-lift animate-fade-up',
        TONES[type] ?? TONES.info,
    ].join(' ');

    const icon = document.createElement('i');
    icon.className = `fa-solid ${ICONS[type] ?? ICONS.info} mt-1 shrink-0`;
    icon.setAttribute('aria-hidden', 'true');

    const text = document.createElement('p');
    text.className = 'text-sm leading-relaxed';
    text.textContent = message;

    const dismiss = document.createElement('button');
    dismiss.type = 'button';
    dismiss.className = 'shrink-0 rounded-md p-1 opacity-60 transition hover:opacity-100';
    dismiss.setAttribute('aria-label', 'بستن اعلان');
    dismiss.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';

    const remove = () => {
        node.style.opacity = '0';
        node.style.transform = 'translateY(0.5rem)';
        setTimeout(() => node.remove(), 200);
    };

    dismiss.addEventListener('click', remove);
    node.append(icon, text, dismiss);
    container().appendChild(node);

    if (timeout) {
        let timer = setTimeout(remove, timeout);
        // Don't yank it away while it's being read or hovered.
        node.addEventListener('mouseenter', () => clearTimeout(timer));
        node.addEventListener('focusin', () => clearTimeout(timer));
        node.addEventListener('mouseleave', () => {
            timer = setTimeout(remove, 2500);
        });
    }

    return node;
}

export function initToast() {
    document.querySelectorAll('[data-toast-seed]').forEach((seed) => {
        toast(seed.dataset.message, seed.dataset.type || 'info');
        seed.remove();
    });
}

// Let inline scripts and other modules raise toasts.
window.judoToast = toast;
