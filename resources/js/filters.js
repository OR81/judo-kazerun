/**
 * Client-side filtering for the training timetable.
 *
 * Rows carry data-day / data-age / data-gender; each control declares which
 * field it filters via data-filter. Filtering happens instantly without a round
 * trip, and the result count is announced through a live region.
 */

export function initFilters() {
    document.querySelectorAll('[data-filters]').forEach(setup);
}

function setup(root) {
    const rows = Array.from(root.querySelectorAll('[data-filter-item]'));
    const controls = Array.from(root.querySelectorAll('[data-filter]'));
    const status = root.querySelector('[data-filter-status]');
    const empty = root.querySelector('[data-filter-empty]');
    const reset = root.querySelector('[data-filter-reset]');
    if (!rows.length) return;

    const state = {};

    function matches(row) {
        return Object.entries(state).every(([field, value]) => {
            if (!value || value === 'all') return true;
            return (row.dataset[field] ?? '').split(' ').includes(value);
        });
    }

    function apply() {
        let visible = 0;

        rows.forEach((row) => {
            const show = matches(row);
            row.hidden = !show;
            if (show) visible++;
        });

        if (empty) empty.hidden = visible > 0;
        if (status) {
            status.textContent = visible
                ? `${visible.toLocaleString('fa-IR')} کلاس یافت شد.`
                : 'کلاسی با این فیلترها یافت نشد.';
        }

        // Sections whose rows are all filtered out collapse entirely.
        root.querySelectorAll('[data-filter-group]').forEach((group) => {
            group.hidden = !group.querySelector('[data-filter-item]:not([hidden])');
        });
    }

    controls.forEach((control) => {
        const field = control.dataset.filter;

        if (control.tagName === 'SELECT') {
            state[field] ??= control.value;
            control.addEventListener('change', () => {
                state[field] = control.value;
                apply();
            });
            return;
        }

        // Button-group control: one active value per field.
        control.addEventListener('click', () => {
            const group = root.querySelectorAll(`[data-filter="${field}"]`);
            group.forEach((button) => {
                const on = button === control;
                button.setAttribute('aria-pressed', String(on));
                button.classList.toggle('bg-brand', on);
                button.classList.toggle('text-on-brand', on);
                button.classList.toggle('border-brand', on);
                button.classList.toggle('bg-surface', !on);
                button.classList.toggle('text-copy', !on);
                button.classList.toggle('border-line', !on);
            });

            state[field] = control.dataset.value;
            apply();
        });

        if (control.getAttribute('aria-pressed') === 'true') {
            state[field] = control.dataset.value;
        }
    });

    reset?.addEventListener('click', () => {
        Object.keys(state).forEach((field) => {
            state[field] = 'all';

            root.querySelectorAll(`[data-filter="${field}"]`).forEach((control) => {
                if (control.tagName === 'SELECT') {
                    control.value = 'all';
                } else {
                    const on = control.dataset.value === 'all';
                    control.setAttribute('aria-pressed', String(on));
                    control.classList.toggle('bg-brand', on);
                    control.classList.toggle('text-on-brand', on);
                    control.classList.toggle('border-brand', on);
                    control.classList.toggle('bg-surface', !on);
                    control.classList.toggle('text-copy', !on);
                    control.classList.toggle('border-line', !on);
                }
            });
        });

        apply();
    });

    apply();
}
