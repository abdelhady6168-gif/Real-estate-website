{{--
    fit="cover" (default) crops to fill the box — right for grids/thumbnails/
    cards where every tile must be a uniform shape. fit="contain" shows the
    whole photo letterboxed instead of cropping it — use it for full-bleed/
    detail views (hero banners, the enlarged gallery photo) where cutting off
    part of the image would lose content. Pass a bg-* class of your own
    alongside fit="contain" to color the letterbox bars for that spot.

    deferSrc=true renders the URL as data-src instead of src — used by
    <x-nakheel.gallery> for every image it renders, since all of them live
    inside a <dialog> that starts closed. resources/js/lazy-dialog-images.js
    hydrates data-src into src the moment that dialog is actually opened
    (whatever triggers it — inline onclick, JS, delegated), so a property's
    full photo/floor-plan set no longer downloads on every page view
    regardless of whether anyone ever opens it.
--}}
@props(['seed' => 'nakheel', 'class' => '', 'fit' => 'cover', 'deferSrc' => false])

@php
    $uploaded = filled($seed) && \Illuminate\Support\Facades\Storage::disk('public')->exists($seed);
    $fitClass = $fit === 'contain' ? 'object-contain' : 'object-cover';
@endphp

@if ($uploaded)
    <img {{ $deferSrc ? 'data-src' : 'src' }}="{{ \Illuminate\Support\Facades\Storage::url($seed) }}" loading="lazy"
         {{ $attributes->merge(['class' => "$fitClass $class"]) }}>
@else
    @php
        $gradients = [
            'from-[#cdb88f] to-[#8a7148]',
            'from-[#b9c6b3] to-[#5c705f]',
            'from-[#cba077] to-[#7c5a37]',
            'from-[#aab8c4] to-[#4c5c68]',
            'from-[#cdb2a1] to-[#6a4d3c]',
            'from-[#9fb3ad] to-[#3f524d]',
        ];
        $gradient = $gradients[crc32($seed) % count($gradients)];
    @endphp

    <div {{ $attributes->merge(['class' => "flex items-center justify-center bg-gradient-to-br $gradient $class"]) }}>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-1/4 w-1/4 text-white/40">
            <path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5a1.5 1.5 0 0 1-1.5 1.5h-3a.75.75 0 0 1-.75-.75V15a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v5.25a.75.75 0 0 1-.75.75h-3a1.5 1.5 0 0 1-1.5-1.5v-6.88l-.97.97a.75.75 0 1 1-1.06-1.06l8.69-8.69Z" />
        </svg>
    </div>
@endif
