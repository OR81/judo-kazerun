/**
 * Multi-step registration wizard.
 *
 * Progressive enhancement: without JS the form is a single long page that still
 * submits and is validated server-side. With JS it becomes stepped, validating
 * each step client-side before advancing and keeping a draft in localStorage so
 * a refresh doesn't lose the visitor's work.
 *
 * File inputs are deliberately never persisted — they can't be, and pretending
 * otherwise would mislead the user.
 */

const DRAFT_KEY = 'judo-registration-draft';

export function initWizard() {
    const root = document.querySelector('[data-wizard]');
    if (!root) return;

    const form = root.closest('form') ?? root.querySelector('form');
    const steps = Array.from(root.querySelectorAll('[data-step]'));
    const indicators = Array.from(root.querySelectorAll('[data-step-indicator]'));
    const progress = root.querySelector('[data-step-progress]');
    const status = root.querySelector('[data-step-status]');
    if (!steps.length) return;

    let current = 0;

    /* ------------------------------------------------------------- rendering */

    function render() {
        steps.forEach((step, i) => {
            step.hidden = i !== current;
        });

        indicators.forEach((indicator, i) => {
            const done = i < current;
            const active = i === current;

            indicator.setAttribute('aria-current', active ? 'step' : 'false');
            indicator.dataset.state = done ? 'done' : active ? 'active' : 'todo';
        });

        if (progress) {
            const pct = ((current + 1) / steps.length) * 100;
            progress.style.inlineSize = `${pct}%`;
            progress.parentElement?.setAttribute('aria-valuenow', String(current + 1));
        }

        if (status) {
            status.textContent = `مرحله ${current + 1} از ${steps.length}: ${steps[current].dataset.stepTitle ?? ''}`;
        }

        // Move focus to the new step heading so keyboard and SR users follow along.
        const heading = steps[current].querySelector('h2, h3, [data-step-heading]');
        if (heading) {
            heading.setAttribute('tabindex', '-1');
            heading.focus({ preventScroll: true });
        }

        root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ------------------------------------------------------------ validation */

    function fieldsOf(step) {
        return Array.from(step.querySelectorAll('input, select, textarea')).filter(
            (el) => !el.disabled && el.type !== 'hidden',
        );
    }

    function showError(field, message) {
        field.setAttribute('aria-invalid', 'true');
        field.classList.add('border-crimson-600');

        const holder = field.closest('[data-field]');
        const slot = holder?.querySelector('[data-field-error]');
        if (slot) {
            slot.textContent = message;
            slot.hidden = false;
        }
    }

    function clearError(field) {
        field.removeAttribute('aria-invalid');
        field.classList.remove('border-crimson-600');

        const slot = field.closest('[data-field]')?.querySelector('[data-field-error]');
        if (slot) {
            slot.textContent = '';
            slot.hidden = true;
        }
    }

    function validate(step) {
        let firstBad = null;

        fieldsOf(step).forEach((field) => {
            clearError(field);

            if (!field.checkValidity()) {
                // Prefer the Persian message the markup supplies over the browser's.
                const message = field.dataset.errorMessage || field.validationMessage;
                showError(field, message);
                firstBad ??= field;
            }
        });

        if (firstBad) {
            firstBad.focus();
            if (status) status.textContent = 'لطفاً خطاهای این مرحله را برطرف کنید.';
        }

        return !firstBad;
    }

    /* ----------------------------------------------------------------- draft */

    function saveDraft() {
        if (!form) return;

        const data = {};
        new FormData(form).forEach((value, key) => {
            if (typeof value === 'string') data[key] = value;
        });

        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
        } catch {
            // Storage full or blocked — the draft is a convenience, not a requirement.
        }
    }

    function restoreDraft() {
        if (!form) return;

        let data;
        try {
            data = JSON.parse(localStorage.getItem(DRAFT_KEY) ?? 'null');
        } catch {
            return;
        }
        if (!data) return;

        Object.entries(data).forEach(([key, value]) => {
            const field = form.elements[key];
            if (!field || field.type === 'file' || field instanceof RadioNodeList) return;
            if (!field.value) field.value = value;
        });
    }

    /* -------------------------------------------------------------- controls */

    root.addEventListener('click', (event) => {
        const next = event.target.closest('[data-step-next]');
        const back = event.target.closest('[data-step-back]');

        if (next) {
            event.preventDefault();
            if (!validate(steps[current])) return;

            saveDraft();
            fillReview();
            current = Math.min(current + 1, steps.length - 1);
            render();
        }

        if (back) {
            event.preventDefault();
            current = Math.max(current - 1, 0);
            render();
        }
    });

    // Clear the inline error as soon as the visitor fixes the field.
    root.addEventListener('input', (event) => {
        const field = event.target;
        if (field.matches('input, select, textarea') && field.checkValidity()) clearError(field);
    });

    form?.addEventListener('submit', () => localStorage.removeItem(DRAFT_KEY));

    /* --------------------------------------------------------- file previews */

    root.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => {
            const holder = input.closest('[data-field]');
            const preview = holder?.querySelector('[data-file-preview]');
            const nameSlot = holder?.querySelector('[data-file-name]');
            const file = input.files?.[0];

            if (!file) {
                if (preview) preview.innerHTML = '';
                if (nameSlot) nameSlot.textContent = '';
                return;
            }

            if (nameSlot) {
                const kb = new Intl.NumberFormat('fa-IR').format(Math.round(file.size / 1024));
                nameSlot.textContent = `${file.name} — ${kb} کیلوبایت`;
            }

            if (preview && file.type.startsWith('image/')) {
                const url = URL.createObjectURL(file);
                preview.innerHTML = `<img src="${url}" alt="" class="h-24 w-24 rounded-card object-cover">`;
                preview.querySelector('img').onload = () => URL.revokeObjectURL(url);
            } else if (preview) {
                preview.innerHTML = '<i class="fa-solid fa-file-lines text-2xl text-muted" aria-hidden="true"></i>';
            }
        });
    });

    /* ----------------------------------------------------------- review step */

    function fillReview() {
        if (!form) return;

        root.querySelectorAll('[data-review-for]').forEach((slot) => {
            const field = form.elements[slot.dataset.reviewFor];
            if (!field) return;

            let value = field.value;

            if (field.tagName === 'SELECT' && field.selectedIndex >= 0) {
                value = field.options[field.selectedIndex].text;
            }

            if (field.type === 'file') {
                value = field.files?.[0]?.name ?? '';
            }

            slot.textContent = value || '—';
        });
    }

    restoreDraft();
    render();
}

/** Drop the saved draft — used by the success page once payment settles. */
export function clearDraft() {
    localStorage.removeItem(DRAFT_KEY);
}
