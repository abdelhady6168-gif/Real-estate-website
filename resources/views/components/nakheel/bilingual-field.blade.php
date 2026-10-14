{{--
    English/Arabic paired input for admin forms — reuses the exact label and
    input/textarea classes already used across every admin _form.blade.php so
    no visual redesign occurs. Under dir="rtl" (Arabic admin session) the grid
    naturally re-orders so the Arabic field renders first, reading right to left.

    When $required is true, neither side carries a native HTML "required" —
    only ONE of the two languages has to be filled in (enforced server-side
    via matching "required_without" rules in the controller); whichever one
    is left blank simply falls back to the other on display, on both site
    locales (see ResolvesTranslations::tr()). Both blank is still rejected.

    $requiredEn is the one exception: a field whose English value is used
    as a system matching key, not just a display label (currently only the
    property Status "Name" — see NakheelPropertyStatusService and the
    Rentable/Booked availability checks), where English genuinely must be
    filled in regardless of Arabic.
--}}
@props(['label', 'nameEn', 'nameAr', 'valueEn' => null, 'valueAr' => null, 'type' => 'text', 'hint' => null, 'rows' => 3, 'required' => false, 'requiredEn' => false])

<div>
    <label class="block text-xs font-medium text-ink/60 mb-1.5">
        {{ $label }}
        @if ($hint)
            <span class="text-ink/30">{{ $hint }}</span>
        @endif
        @if ($required)
            <span class="text-ink/30">{{ __('(fill in English or Arabic — at least one is required)') }}</span>
        @endif
    </label>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('English') }}</span>
            @if ($type === 'textarea')
                <textarea name="{{ $nameEn }}" rows="{{ $rows }}" {{ $requiredEn ? 'required' : '' }}
                          class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">{{ old($nameEn, $valueEn) }}</textarea>
            @else
                <input type="text" name="{{ $nameEn }}" value="{{ old($nameEn, $valueEn) }}" {{ $requiredEn ? 'required' : '' }}
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            @endif
        </div>

        <div>
            <span class="block text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('Arabic') }}</span>
            @if ($type === 'textarea')
                <textarea name="{{ $nameAr }}" dir="rtl" rows="{{ $rows }}"
                          class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">{{ old($nameAr, $valueAr) }}</textarea>
            @else
                <input type="text" name="{{ $nameAr }}" dir="rtl" value="{{ old($nameAr, $valueAr) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            @endif
        </div>
    </div>
</div>
