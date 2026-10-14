{{--
    A small calendar pop-up for picking a single date — used by
    <x-nakheel.availability-manager> for the Start date / End date fields
    (see resources/js/date-picker.js). Same "trigger toggles a positioned
    panel, outside click / Escape closes it" pattern as the existing
    <x-nakheel.filter-select> dropdown (resources/js/select.js), so it
    behaves consistently with the rest of the admin UI. Always displays
    DD/MM/YYYY regardless of browser locale; the actual value submitted is
    the ISO date in the hidden input.
--}}
@props(['name', 'value' => null])

<div {{ $attributes->merge(['class' => 'relative']) }} data-date-picker>
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-date-picker-value>

    <button type="button" data-date-picker-trigger
            class="flex w-[9.5rem] items-center justify-between gap-2 rounded-md border border-border bg-cream/40 px-2.5 py-1.5 text-sm text-start">
        <span data-date-picker-label class="min-w-0 truncate {{ $value ? 'text-ink' : 'text-ink/40' }}">
            {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : 'DD/MM/YYYY' }}
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 shrink-0 text-ink/40">
            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2ZM3.5 8.5v6.75c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25V8.5h-13Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div data-date-picker-popup class="hidden absolute z-30 mt-1 w-60 rounded-md border border-border bg-surface p-3 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <button type="button" data-date-picker-prev aria-label="{{ __('Previous month') }}"
                    class="flex h-7 w-7 items-center justify-center rounded-md border border-border text-ink/60 hover:text-ink">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 rtl:rotate-180">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1 .02 1.06L8.832 10l3.98 3.71a.75.75 0 1 1-1.024 1.098l-4.5-4.19a.75.75 0 0 1 0-1.098l4.5-4.19a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                </svg>
            </button>
            <span data-date-picker-month class="font-serif text-xs font-semibold text-ink"></span>
            <button type="button" data-date-picker-next aria-label="{{ __('Next month') }}"
                    class="flex h-7 w-7 items-center justify-center rounded-md border border-border text-ink/60 hover:text-ink">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 rtl:rotate-180">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1-.02-1.06L11.168 10l-3.98-3.71a.75.75 0 1 1 1.024-1.098l4.5 4.19a.75.75 0 0 1 0 1.098l-4.5 4.19a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div data-date-picker-weekdays class="grid grid-cols-7 gap-y-1 mb-1"></div>
        <div data-date-picker-grid class="grid grid-cols-7 gap-y-1"></div>
    </div>
</div>
