{{--
    A dropdown that submits like a native <select> (same name/value, works
    inside a plain GET form) but renders its own option list in regular
    HTML/CSS instead of the browser's native popup — native option-list
    sizing can't be styled reliably across browsers, which is what was
    clipping Arabic text in Firefox's list.
--}}
@props(['name', 'value' => null, 'options'])

@php
    $current = collect($options)->firstWhere('value', $value);
@endphp

<div class="relative" data-select>
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-select-value>

    <button type="button" data-select-trigger
            class="w-full flex items-center justify-between gap-2 rounded-md border border-border bg-cream/40 px-2.5 py-2 text-sm text-ink text-start">
        <span data-select-label class="truncate">{{ $current['label'] ?? $value }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 shrink-0 text-ink/70"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.293l3.71-4.06a.75.75 0 1 1 1.08 1.04l-4.25 4.65a.75.75 0 0 1-1.08 0l-4.25-4.65a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
    </button>

    <div data-select-menu class="hidden absolute z-30 mt-1 max-h-56 w-full overflow-y-auto rounded-md border border-border bg-surface py-1 text-sm shadow-lg">
        @foreach ($options as $option)
            <button type="button" data-select-option data-value="{{ $option['value'] }}"
                    class="block w-full px-3 py-1.5 text-start hover:bg-cream {{ $option['value'] === $value ? 'font-semibold text-ink bg-cream/60' : 'text-ink/80' }}">
                {{ $option['label'] }}
            </button>
        @endforeach
    </div>
</div>
