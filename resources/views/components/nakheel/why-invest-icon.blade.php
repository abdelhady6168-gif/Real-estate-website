{{--
    Renders one "Why Invest" reason's icon: a preset icon (new picker) if
    set, else an old uploaded image for records saved before the picker
    existed, else the default checkmark. Matches the h-9 w-9 rounded-full
    badge already used in the Why Invest grid.
--}}
@props(['icon' => null, 'legacyImage' => null, 'tint' => true])

@if (blank($icon) && filled($legacyImage))
    <x-nakheel.ph :seed="$legacyImage" {{ $attributes->merge(['class' => 'h-9 w-9 shrink-0 rounded-full']) }} />
@else
    <span {{ $attributes->merge(['class' => 'flex h-9 w-9 shrink-0 items-center justify-center rounded-full '.($tint ? 'bg-gold/10 text-gold' : 'bg-brand/5 text-ink/60')]) }}>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4.5 w-4.5">
            {!! implode('', \App\Support\WhyInvestIcons::paths($icon)) !!}
        </svg>
    </span>
@endif
