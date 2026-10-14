{{--
    Add → Edit → Remove list editor for admin forms. Two modes, both
    bilingual (English + Arabic per item — each is the only consumer of its
    mode, so there is no monolingual variant to preserve):
    - mode="fields": label/value row pairs (Specifications) — submits as
      {{ $name }}[<key>][label] / [label_ar] / [value] / [value_ar].
    - mode="tags": a flat list of removable chips (Features) — submits as a
      single hidden input holding a JSON array of {value, value_ar} pairs.
--}}
@props(['name', 'mode', 'label', 'hint' => null, 'items' => []])

@if ($mode === 'fields')
    @php
        // Browser-side hint only (numeric keypad + a message before submit);
        // the server re-validates every row — see AdminPropertyController::
        // validateSpecRow(), which also decides whole number vs decimal.
        // Accepts Western and Arabic-keyboard digits.
        $numberPattern = '[0-9٠-٩۰-۹]+([.٫][0-9٠-٩۰-۹]{1,2})?';
        $numberTitle = __('Numbers only, e.g. 4. Decimals (e.g. 240.5) only for measurements whose name includes a unit, e.g. Area (m²).');
    @endphp
    <div data-repeater="fields" data-name="{{ $name }}">
        <label class="block text-xs font-medium text-ink/60 mb-1.5">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </label>

        <div data-repeater-rows class="space-y-2">
            @foreach ($items as $i => $item)
                <div class="rounded-md border border-border p-3 space-y-2" data-repeater-row>
                    <div class="flex items-start gap-2">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="{{ $name }}[{{ $i }}][label]" value="{{ $item['label'] ?? '' }}" placeholder="{{ __('Label (English)') }}"
                                   class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                            <input type="text" name="{{ $name }}[{{ $i }}][label_ar]" dir="rtl" value="{{ $item['label_ar'] ?? '' }}" placeholder="{{ __('Label (Arabic)') }}"
                                   class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                        </div>
                        <button type="button" data-repeater-remove aria-label="{{ __('Remove') }}"
                                class="shrink-0 flex h-8 w-8 items-center justify-center rounded-md text-red-500 hover:bg-red-50">&times;</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" inputmode="decimal" pattern="{{ $numberPattern }}" title="{{ $numberTitle }}" name="{{ $name }}[{{ $i }}][value]" value="{{ $item['value'] ?? '' }}" placeholder="{{ __('Value (English)') }}"
                               class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                        <input type="text" inputmode="decimal" pattern="{{ $numberPattern }}" title="{{ $numberTitle }}" name="{{ $name }}[{{ $i }}][value_ar]" dir="rtl" value="{{ $item['value_ar'] ?? '' }}" placeholder="{{ __('Value (Arabic)') }}"
                               class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                </div>
            @endforeach
        </div>

        <template data-repeater-template>
            <div class="rounded-md border border-border p-3 space-y-2" data-repeater-row>
                <div class="flex items-start gap-2">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" name="{{ $name }}[__KEY__][label]" placeholder="{{ __('Label (English)') }}"
                               class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                        <input type="text" name="{{ $name }}[__KEY__][label_ar]" dir="rtl" placeholder="{{ __('Label (Arabic)') }}"
                               class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                    <button type="button" data-repeater-remove aria-label="{{ __('Remove') }}"
                            class="shrink-0 flex h-8 w-8 items-center justify-center rounded-md text-red-500 hover:bg-red-50">&times;</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input type="text" inputmode="decimal" pattern="{{ $numberPattern }}" title="{{ $numberTitle }}" name="{{ $name }}[__KEY__][value]" placeholder="{{ __('Value (English)') }}"
                           class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    <input type="text" inputmode="decimal" pattern="{{ $numberPattern }}" title="{{ $numberTitle }}" name="{{ $name }}[__KEY__][value_ar]" dir="rtl" placeholder="{{ __('Value (Arabic)') }}"
                           class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                </div>
            </div>
        </template>

        <button type="button" data-repeater-add
                class="mt-2 rounded-md border border-dashed border-border px-3 py-1.5 text-xs font-semibold text-ink/60 hover:text-ink hover:border-ink">
            + {{ __('Add') }}
        </button>
    </div>
@else
    <div data-repeater="tags" data-name="{{ $name }}" data-no-digits-message="{{ __('Feature names can\'t contain numbers.') }}">
        <label class="block text-xs font-medium text-ink/60 mb-1.5">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </label>

        <input type="hidden" name="{{ $name }}" data-repeater-value value="{{ json_encode(collect($items)->map(fn ($item) => ['value' => $item['value'] ?? '', 'value_ar' => $item['value_ar'] ?? null])->all()) }}">

        <div data-repeater-chips class="flex flex-wrap gap-2 mb-2">
            @foreach ($items as $item)
                <span class="inline-flex items-center gap-1.5 rounded-md border border-border bg-surface px-3 py-1.5 text-xs text-ink/80"
                      data-repeater-chip data-value="{{ $item['value'] ?? '' }}" data-value-ar="{{ $item['value_ar'] ?? '' }}">
                    <span>{{ $item['value'] ?? '' }}</span>
                    @if (filled($item['value_ar'] ?? null))
                        <span class="text-ink/40" dir="rtl">({{ $item['value_ar'] }})</span>
                    @endif
                    <button type="button" data-repeater-chip-remove aria-label="{{ __('Remove') }}" class="text-ink/40 hover:text-red-600">&times;</button>
                </span>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <input type="text" data-repeater-chip-input-en placeholder="{{ __('Add feature (English)...') }}"
                   class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            <input type="text" data-repeater-chip-input-ar dir="rtl" placeholder="{{ __('Add feature (Arabic)...') }}"
                   class="rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
        </div>
        <button type="button" data-repeater-chip-add
                class="mt-2 rounded-md border border-dashed border-border px-3 py-1.5 text-xs font-semibold text-ink/60 hover:text-ink hover:border-ink">
            + {{ __('Add') }}
        </button>
    </div>
@endif
