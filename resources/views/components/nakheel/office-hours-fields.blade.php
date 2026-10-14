{{--
    Day-dropdown + native time-picker pair for office hours, replacing the old
    free-typed "Mon-Fri 9am-5pm" text box. Field names are fixed
    (office_hours_from_day/to_day/from_time/to_time) since every table that
    uses this shares those exact column names — see FormatsOfficeHours.
--}}
@props(['fromDay' => null, 'toDay' => null, 'fromTime' => null, 'toTime' => null])

@php
    $days = [
        'sunday' => 'Sunday', 'monday' => 'Monday', 'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday',
    ];
@endphp

<div>
    <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Office Hours') }}</label>
    <div class="grid grid-cols-2 gap-3 mb-2">
        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('From day') }}</span>
            <select name="office_hours_from_day" class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                <option value="">{{ __('— Select —') }}</option>
                @foreach ($days as $value => $label)
                    <option value="{{ $value }}" {{ old('office_hours_from_day', $fromDay) === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('To day') }}</span>
            <select name="office_hours_to_day" class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                <option value="">{{ __('— Select —') }}</option>
                @foreach ($days as $value => $label)
                    <option value="{{ $value }}" {{ old('office_hours_to_day', $toDay) === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('From time') }}</span>
            <input type="time" name="office_hours_from_time" value="{{ old('office_hours_from_time', $fromTime) }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
        </div>
        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('To time') }}</span>
            <input type="time" name="office_hours_to_time" value="{{ old('office_hours_to_time', $toTime) }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
        </div>
    </div>
</div>
