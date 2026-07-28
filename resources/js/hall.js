/**
 * تابلوی سانس‌ها — the hall board on the home page.
 *
 * The server renders all seven days and marks today with `data-active`; CSS hides
 * the rest as soon as the document is known to be scripted. This module is only
 * the controller: it moves `data-active` between day panels and narrows the
 * visible day to one hall.
 *
 * The tab strip is a real ARIA tablist with roving tabindex. Arrow keys are
 * mirrored for RTL — on a right-to-left page ArrowLeft means "the next day", the
 * opposite of the WAI-ARIA pattern's left-to-right assumption.
 */

const rtl = document.documentElement.dir === 'rtl';

export function initHall() {
    const root = document.querySelector('[data-hall]');
    if (!root) return;

    const tabs = [...root.querySelectorAll('[data-hall-tab]')];
    const panels = [...root.querySelectorAll('[data-hall-panel]')];
    const venueButtons = [...root.querySelectorAll('[data-hall-venue]')];
    const status = root.querySelector('[data-hall-status]');

    if (!tabs.length) return;

    let venue = 'all';

    const activeTab = () => tabs.find((tab) => tab.hasAttribute('data-active')) ?? tabs[0];

    function selectDay(tab, { focus = false } = {}) {
        const day = tab.dataset.hallTab;

        tabs.forEach((item) => {
            const on = item === tab;
            item.toggleAttribute('data-active', on);
            item.setAttribute('aria-selected', String(on));
            item.tabIndex = on ? 0 : -1;
        });

        panels.forEach((panel) => {
            panel.toggleAttribute('data-active', panel.dataset.hallPanel === day);
        });

        if (focus) tab.focus();

        applyVenue();
    }

    /** Hide the slots of the halls that are filtered out, in every panel. */
    function applyVenue() {
        venueButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.hallVenue === venue));
        });

        let shown = 0;

        panels.forEach((panel) => {
            const slots = [...panel.querySelectorAll('[data-hall-slot]')];
            let visible = 0;

            slots.forEach((slot) => {
                const match = venue === 'all' || slot.dataset.venue === venue;
                slot.hidden = !match;
                if (match) visible++;
            });

            // Every panel keeps its own "nothing here" line, so the message stays
            // beside the day it belongs to rather than at the bottom of the board.
            const empty = panel.querySelector('[data-hall-empty]');
            if (empty) empty.hidden = visible > 0;

            if (panel.hasAttribute('data-active')) shown = visible;
        });

        announce(shown);
    }

    function announce(count) {
        if (!status) return;

        const day = activeTab().querySelector('span')?.textContent?.trim() ?? '';
        status.textContent = count
            ? `${day}: ${count} سانس نمایش داده شد.`
            : `${day}: سانسی با این فیلتر یافت نشد.`;
    }

    root.addEventListener('click', (event) => {
        const tab = event.target.closest('[data-hall-tab]');
        if (tab) {
            selectDay(tab);
            return;
        }

        const button = event.target.closest('[data-hall-venue]');
        if (button) {
            venue = button.dataset.hallVenue;
            applyVenue();
        }
    });

    root.addEventListener('keydown', (event) => {
        const tab = event.target.closest('[data-hall-tab]');
        if (!tab) return;

        const index = tabs.indexOf(tab);
        const forward = rtl ? 'ArrowLeft' : 'ArrowRight';
        const back = rtl ? 'ArrowRight' : 'ArrowLeft';

        const next = {
            [forward]: (index + 1) % tabs.length,
            [back]: (index - 1 + tabs.length) % tabs.length,
            Home: 0,
            End: tabs.length - 1,
        }[event.key];

        if (next === undefined) return;

        event.preventDefault();
        selectDay(tabs[next], { focus: true });
    });

    applyVenue();
}
