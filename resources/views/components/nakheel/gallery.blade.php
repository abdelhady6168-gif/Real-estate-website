{{--
    Property/offer image gallery: a hero photo + thumbnail filmstrip,
    used for both photos and floor plans — opens a full lightbox for
    browsing every image (not just what's shown up front).
--}}
@props(['images' => [], 'id', 'label' => null, 'badge' => null, 'fallbackSeed' => null, 'placeholder' => null])

@php
    $list = collect($images)->filter()->values();
    $shown = $list->take(4);
    $remaining = max(0, $list->count() - 4);
    $heroSeed = $shown->first() ?? $fallbackSeed;
    $filmstrip = $shown->slice(1)->values();
@endphp

@if (!$heroSeed && $placeholder)
    <div>
        @if ($label)
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-2">{{ $label }}</p>
        @endif
        <div class="aspect-[4/3] w-full rounded-md border border-dashed border-border bg-cream/40 flex flex-col items-center justify-center gap-2 text-ink/35">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4.5h11.25L20 9.25V19.5a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1-.5-.5v-15a.5.5 0 0 1 .5-.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 4.5V9.5H20" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 13h8M8 16h5" />
            </svg>
            <p class="text-xs font-medium">{{ __('No blueprint available') }}</p>
        </div>
    </div>
@elseif ($heroSeed)
    <div>
        @if ($label)
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-2">{{ $label }}</p>
        @endif

        @if ($list->isNotEmpty())
            <button type="button" data-gallery-open data-gallery-index="0" data-gallery-target="{{ $id }}"
                    class="relative aspect-[4/3] w-full rounded-md overflow-hidden block">
                <x-nakheel.ph :seed="$heroSeed" :defer-src="true" class="h-full w-full" />
                @if ($badge)
                    <span class="absolute bottom-2.5 left-2.5 bg-brand text-white text-[10px] font-semibold tracking-wider uppercase px-2 py-1 rounded">
                        {{ $badge }}
                    </span>
                @endif
            </button>
        @else
            <div class="relative aspect-[4/3] w-full rounded-md overflow-hidden">
                <x-nakheel.ph :seed="$heroSeed" :defer-src="true" class="h-full w-full" />
                @if ($badge)
                    <span class="absolute bottom-2.5 left-2.5 bg-brand text-white text-[10px] font-semibold tracking-wider uppercase px-2 py-1 rounded">
                        {{ $badge }}
                    </span>
                @endif
            </div>
        @endif

        @if ($filmstrip->isNotEmpty())
            <div class="flex gap-1 mt-1 h-20 rounded-md overflow-hidden bg-brand">
                @foreach ($filmstrip as $i => $path)
                    <button type="button" data-gallery-open data-gallery-index="{{ $i + 1 }}" data-gallery-target="{{ $id }}"
                            class="relative h-full flex-1">
                        <x-nakheel.ph :seed="$path" :defer-src="true" class="h-full w-full" />
                        @if ($loop->last && $remaining > 0)
                            <span class="absolute inset-0 flex items-center justify-center bg-brand/60 text-white font-semibold">+{{ $remaining }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        @endif
    </div>
@endif

@if ($list->isNotEmpty())
    <dialog id="{{ $id }}" data-gallery-dialog data-dialog-backdrop-close
            class="m-auto p-0 border-0 rounded-lg bg-brand max-w-3xl w-[92vw] max-h-[88vh] backdrop:bg-brand/80">
        <div class="relative">
            <button type="button" data-dialog-close aria-label="{{ __('Close') }}"
                    class="absolute top-3 right-3 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-surface/90 text-ink shadow hover:bg-surface">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
            </button>

            <div class="relative aspect-[4/3]">
                @foreach ($list as $i => $path)
                    <div class="absolute inset-0 {{ $i === 0 ? '' : 'hidden' }}" data-gallery-slide>
                        <x-nakheel.ph :seed="$path" :defer-src="true" fit="contain" class="h-full w-full" />
                    </div>
                @endforeach
            </div>

            @if ($list->count() > 1)
                <button type="button" data-gallery-prev aria-label="{{ __('Previous') }}"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white shadow-lg ring-1 ring-white/25 hover:bg-black/70">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" /></svg>
                </button>
                <button type="button" data-gallery-next aria-label="{{ __('Next') }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white shadow-lg ring-1 ring-white/25 hover:bg-black/70">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
                </button>
                <p class="pb-3 text-center text-xs text-white/70" data-gallery-counter></p>
            @endif
        </div>
    </dialog>
@endif
