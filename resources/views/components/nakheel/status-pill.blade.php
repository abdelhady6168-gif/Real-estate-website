{{--
    Tinted pill badge (colored background + colored text, no dot). Used for
    the "Our Project Portfolio" timeline entry status. This is a separate
    style from the dot-badge used for property availability on
    property-card.blade.php, which is unaffected by this component.
--}}
@props(['label', 'color' => 'emerald'])

@php
    $tints = [
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'sky' => 'bg-sky-50 text-sky-700 border-sky-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
        'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
        'violet' => 'bg-violet-50 text-violet-700 border-violet-200',
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'red' => 'bg-red-50 text-red-700 border-red-200',
    ];
    $tint = $tints[$color] ?? $tints['gray'];
@endphp

<span class="inline-flex items-center w-fit text-[10px] font-medium px-2.5 py-1 rounded-full border {{ $tint }}">
    {{ $label }}
</span>
