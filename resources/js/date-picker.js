// Calendar pop-up date field — see
// resources/views/components/nakheel/date-picker-field.blade.php. Same
// "trigger toggles a positioned panel, outside click / Escape closes it"
// pattern as resources/js/select.js. Always shows DD/MM/YYYY; the hidden
// input carries the ISO value that actually gets submitted.

function closeAllDatePickers(except = null) {
    document.querySelectorAll('[data-date-picker-popup]').forEach((popup) => {
        if (popup !== except) popup.classList.add('hidden');
    });
}

function toDateKey(year, month, day) {
    return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

function isoToDisplay(iso) {
    const [y, m, d] = iso.split('-');
    return `${d}/${m}/${y}`;
}

function renderDatePicker(wrapper) {
    const popup = wrapper.querySelector('[data-date-picker-popup]');
    const hidden = wrapper.querySelector('[data-date-picker-value]');
    const locale = document.documentElement.lang || 'en';
    const [year, month] = wrapper.dataset.calMonth.split('-').map(Number);
    const monthIndex = month - 1;
    const selected = hidden.value || null;

    const todayKey = (() => {
        const d = new Date();
        return toDateKey(d.getFullYear(), d.getMonth(), d.getDate());
    })();

    popup.querySelector('[data-date-picker-month]').textContent =
        new Intl.DateTimeFormat(locale, { month: 'long', year: 'numeric' }).format(new Date(year, monthIndex, 1));

    const weekdaysRow = popup.querySelector('[data-date-picker-weekdays]');
    weekdaysRow.innerHTML = '';
    const formatter = new Intl.DateTimeFormat(locale, { weekday: 'narrow' });
    for (let i = 0; i < 7; i++) {
        const cell = document.createElement('span');
        cell.className = 'flex h-7 items-center justify-center text-[10px] font-semibold text-ink/40';
        cell.textContent = formatter.format(new Date(2023, 0, 1 + i));
        weekdaysRow.appendChild(cell);
    }

    const grid = popup.querySelector('[data-date-picker-grid]');
    grid.innerHTML = '';

    const firstWeekday = new Date(year, monthIndex, 1).getDay();
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();

    for (let i = 0; i < firstWeekday; i++) {
        grid.appendChild(document.createElement('span'));
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateKey = toDateKey(year, monthIndex, day);
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.datePickerDay = '';
        button.dataset.value = dateKey;
        button.textContent = String(day);

        const isSelected = dateKey === selected;
        const isToday = dateKey === todayKey;
        button.className = [
            'flex h-7 w-7 items-center justify-center rounded-full mx-auto text-xs',
            isSelected ? 'bg-gold text-white font-semibold' : isToday ? 'border border-gold text-ink' : 'text-ink/70 hover:bg-cream',
        ].join(' ');

        grid.appendChild(button);
    }
}

function openDatePicker(wrapper) {
    const popup = wrapper.querySelector('[data-date-picker-popup]');
    const hidden = wrapper.querySelector('[data-date-picker-value]');

    if (!wrapper.dataset.calMonth) {
        const base = hidden.value ? new Date(hidden.value) : new Date();
        wrapper.dataset.calMonth = `${base.getFullYear()}-${String(base.getMonth() + 1).padStart(2, '0')}`;
    }

    closeAllDatePickers(popup);
    renderDatePicker(wrapper);
    popup.classList.remove('hidden');
}

function selectDate(wrapper, iso) {
    const hidden = wrapper.querySelector('[data-date-picker-value]');
    const label = wrapper.querySelector('[data-date-picker-label]');

    hidden.value = iso;
    hidden.dispatchEvent(new Event('change', { bubbles: true }));
    label.textContent = isoToDisplay(iso);
    label.classList.remove('text-ink/40');
    label.classList.add('text-ink');

    closeAllDatePickers();
}

function shiftDatePickerMonth(wrapper, delta) {
    const [year, month] = wrapper.dataset.calMonth.split('-').map(Number);
    const next = new Date(year, month - 1 + delta, 1);
    wrapper.dataset.calMonth = `${next.getFullYear()}-${String(next.getMonth() + 1).padStart(2, '0')}`;
    renderDatePicker(wrapper);
}

// Public helper so other scripts (resources/js/availability.js, when
// repopulating the form for "Edit") can set a picker's value programmatically.
window.setDatePickerValue = function (wrapper, iso) {
    const hidden = wrapper.querySelector('[data-date-picker-value]');
    const label = wrapper.querySelector('[data-date-picker-label]');

    hidden.value = iso || '';
    label.textContent = iso ? isoToDisplay(iso) : 'DD/MM/YYYY';
    label.classList.toggle('text-ink', !!iso);
    label.classList.toggle('text-ink/40', !iso);

    if (iso) {
        const d = new Date(iso);
        wrapper.dataset.calMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    } else {
        delete wrapper.dataset.calMonth;
    }
};

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-date-picker-trigger]');
    if (trigger) {
        const wrapper = trigger.closest('[data-date-picker]');
        const popup = wrapper.querySelector('[data-date-picker-popup]');
        const wasOpen = !popup.classList.contains('hidden');
        closeAllDatePickers();
        if (!wasOpen) openDatePicker(wrapper);
        return;
    }

    const prevBtn = event.target.closest('[data-date-picker-prev]');
    const nextBtn = event.target.closest('[data-date-picker-next]');
    if (prevBtn || nextBtn) {
        const wrapper = (prevBtn || nextBtn).closest('[data-date-picker]');
        shiftDatePickerMonth(wrapper, prevBtn ? -1 : 1);
        return;
    }

    const day = event.target.closest('[data-date-picker-day]');
    if (day) {
        const wrapper = day.closest('[data-date-picker]');
        selectDate(wrapper, day.dataset.value);
        return;
    }

    if (!event.target.closest('[data-date-picker]')) {
        closeAllDatePickers();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAllDatePickers();
});
