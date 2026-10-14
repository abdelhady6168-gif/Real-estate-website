{{--
    Availability calendar for Rentable/Booked properties. A property is
    available by default — :ranges carries only the stored UNAVAILABLE
    periods (NakheelPropertyAvailabilityService::forProperty()), which
    resources/js/calendar.js uses to mark any day inside one of them
    unavailable and everything else available. :displayRanges is the same
    underlying data pre-computed into the full available/unavailable
    timeline (calculatedRanges()) for the static list below the grid —
    one data source, two views of it.
--}}
@props(['ranges' => [], 'displayRanges' => []])

<div data-availability-calendar data-ranges='@json($ranges)' class="border border-border rounded-lg bg-cream overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-border">
        <button type="button" data-cal-prev aria-label="{{ __('Previous month') }}"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-border bg-surface text-ink/60 hover:text-ink">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 rtl:rotate-180">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1 .02 1.06L8.832 10l3.98 3.71a.75.75 0 1 1-1.024 1.098l-4.5-4.19a.75.75 0 0 1 0-1.098l4.5-4.19a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
            </svg>
        </button>
        <p data-cal-label class="font-serif text-base font-semibold text-ink"></p>
        <button type="button" data-cal-next aria-label="{{ __('Next month') }}"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-border bg-surface text-ink/60 hover:text-ink">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 rtl:rotate-180">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1-.02-1.06L11.168 10l-3.98-3.71a.75.75 0 1 1 1.024-1.098l4.5 4.19a.75.75 0 0 1 0 1.098l-4.5 4.19a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div data-cal-weekdays class="grid grid-cols-7 border-b border-border"></div>
    <div data-cal-grid class="grid grid-cols-7 border-t border-border"></div>

    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 px-4 py-3 border-t border-border text-[11px] text-ink/60">
        <span class="flex items-center gap-1.5">
            <span class="h-3 w-3 rounded-sm border border-emerald-400 bg-emerald-50"></span>
            {{ __('Available') }}
        </span>
        <span class="flex items-center gap-1.5">
            <span class="h-3 w-3 rounded-sm border border-rose-400 bg-rose-50"></span>
            {{ __('Unavailable') }}
        </span>
        <span class="flex items-center gap-1.5">
            <span class="h-3 w-3 rounded-full bg-gold"></span>
            {{ __('Today') }}
        </span>
    </div>

    @if (!empty($displayRanges))
        <div class="border-t border-border divide-y divide-border">
            @foreach ($displayRanges as $range)
                <div class="flex items-center justify-between gap-3 px-4 py-2.5 text-xs {{ $range['is_available'] ? 'bg-emerald-500/5' : 'bg-rose-500/5' }}">
                    <span class="flex items-center gap-1.5 font-medium {{ $range['is_available'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $range['is_available'] ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ $range['is_available'] ? __('Available') : __('Booked') }}
                    </span>
                    <span class="text-ink/60 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($range['start_date'])->format('d/m/Y') }} &rarr; {{ \Illuminate\Support\Carbon::parse($range['end_date'])->format('d/m/Y') }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
