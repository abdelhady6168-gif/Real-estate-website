@props(['images' => [], 'dots' => true, 'arrows' => true])

@php
    $images = collect($images)->filter()->values();
    $multiple = $images->count() > 1;
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }} @if ($multiple) data-carousel @endif>
    @forelse ($images as $i => $seed)
        <div class="absolute inset-0 {{ $i === 0 ? '' : 'hidden' }}" data-slide>
            <x-nakheel.ph :seed="$seed" class="h-full w-full" />
        </div>
    @empty
        <x-nakheel.ph seed="nakheel" class="absolute inset-0 h-full w-full" />
    @endforelse

    @if ($multiple)
        @if ($arrows)
            <button type="button" data-carousel-prev aria-label="Previous image"
                    class="absolute left-3 top-1/2 -translate-y-1/2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-surface/15 text-white hover:bg-surface/25">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" /></svg>
            </button>
            <button type="button" data-carousel-next aria-label="Next image"
                    class="absolute right-3 top-1/2 -translate-y-1/2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-surface/15 text-white hover:bg-surface/25">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </button>
        @endif

        @if ($dots)
            <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-1.5 z-10">
                @foreach ($images as $i => $seed)
                    <button type="button" data-dot="{{ $i }}" aria-label="Go to image {{ $i + 1 }}"
                            class="h-1.5 rounded-full transition-all {{ $i === 0 ? 'bg-surface w-4' : 'bg-surface/40 w-1.5' }}"></button>
                @endforeach
            </div>
        @endif
    @endif
</div>
