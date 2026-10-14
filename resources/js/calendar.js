// Availability calendar — see resources/views/components/nakheel/availability-calendar.blade.php.
// Renders one month at a time from the UNAVAILABLE periods baked into the
// container's data-ranges attribute — a property is available by default,
// so any day inside one of those periods is unavailable and every other
// day is available. Prev/Next just move the displayed month, no server
// round-trip. Month/weekday names use Intl so Arabic works for free.

function parseRanges(container) {
    try {
        return JSON.parse(container.dataset.ranges || '[]');
    } catch (e) {
        return [];
    }
}

// 'YYYY-MM-DD' compares fine as plain strings, and avoids timezone drift
// from parsing dates through `new Date('YYYY-MM-DD')`.
function toDateKey(year, month, day) {
    return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

function statusFor(dateKey, unavailablePeriods) {
    const isUnavailable = unavailablePeriods.some((r) => dateKey >= r.start_date && dateKey <= r.end_date);
    return isUnavailable ? 'unavailable' : 'available';
}

function dayCellClass(status) {
    const base = 'relative flex aspect-square items-center justify-center border-e border-b border-border text-sm';

    return status === 'unavailable' ? `${base} bg-rose-50 text-rose-700` : `${base} bg-emerald-50 text-emerald-700`;
}

function render(container) {
    const ranges = parseRanges(container);
    const locale = document.documentElement.lang || 'en';
    const [year, month] = container.dataset.calMonth.split('-').map(Number);
    const monthIndex = month - 1;

    const todayKey = (() => {
        const d = new Date();
        return toDateKey(d.getFullYear(), d.getMonth(), d.getDate());
    })();

    const label = container.querySelector('[data-cal-label]');
    label.textContent = new Intl.DateTimeFormat(locale, { month: 'long', year: 'numeric' }).format(new Date(year, monthIndex, 1));

    const weekdaysRow = container.querySelector('[data-cal-weekdays]');
    weekdaysRow.innerHTML = '';
    const formatter = new Intl.DateTimeFormat(locale, { weekday: 'short' });
    for (let i = 0; i < 7; i++) {
        const cell = document.createElement('span');
        cell.className = 'py-2 text-center text-[10px] font-semibold uppercase tracking-wide text-ink/50';
        // A fixed reference Sunday, so day 0 = Sunday regardless of today's date.
        cell.textContent = formatter.format(new Date(2023, 0, 1 + i));
        weekdaysRow.appendChild(cell);
    }

    const grid = container.querySelector('[data-cal-grid]');
    grid.innerHTML = '';

    const firstWeekday = new Date(year, monthIndex, 1).getDay();
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
    const totalCells = Math.ceil((firstWeekday + daysInMonth) / 7) * 7;

    for (let i = 0; i < totalCells; i++) {
        const day = i - firstWeekday + 1;
        const cell = document.createElement('span');

        if (day < 1 || day > daysInMonth) {
            cell.className = 'aspect-square border-e border-b border-border bg-ink/5';
            grid.appendChild(cell);
            continue;
        }

        const dateKey = toDateKey(year, monthIndex, day);
        const status = statusFor(dateKey, ranges);
        cell.className = dayCellClass(status);

        if (dateKey === todayKey) {
            cell.innerHTML = `<span class="flex h-6 w-6 items-center justify-center rounded-full bg-gold font-semibold text-white">${day}</span>`;
        } else {
            cell.textContent = String(day);
        }

        grid.appendChild(cell);
    }
}

function shiftMonth(container, delta) {
    const [year, month] = container.dataset.calMonth.split('-').map(Number);
    const next = new Date(year, month - 1 + delta, 1);
    container.dataset.calMonth = `${next.getFullYear()}-${String(next.getMonth() + 1).padStart(2, '0')}`;
    render(container);
}

document.querySelectorAll('[data-availability-calendar]').forEach((container) => {
    const today = new Date();
    container.dataset.calMonth = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;
    render(container);
});

document.addEventListener('click', (event) => {
    const prevBtn = event.target.closest('[data-cal-prev]');
    const nextBtn = event.target.closest('[data-cal-next]');
    if (!prevBtn && !nextBtn) return;

    const container = (prevBtn || nextBtn).closest('[data-availability-calendar]');
    if (!container) return;

    shiftMonth(container, prevBtn ? -1 : 1);
});
