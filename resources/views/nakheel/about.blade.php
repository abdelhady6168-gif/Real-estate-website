@extends('layouts.nakheel')

@section('title', 'About Us — Nakheel Properties')

@php
    $canCreateAboutUs = auth()->user()?->hasPermission('about_us', 'create');
    $canEditAboutUs = auth()->user()?->hasPermission('about_us', 'edit');
    $canDeleteAboutUs = auth()->user()?->hasPermission('about_us', 'delete');
@endphp

@section('content')

    <div class="relative h-[280px] md:h-[320px] overflow-hidden">
        <x-nakheel.ph :seed="$hero['image'] ?: 'nakheel-about-hero'" class="absolute inset-0 h-full w-full" />
        <div class="absolute inset-0 bg-black/55"></div>

        @if ($canEditAboutUs)
            <a href="{{ route('admin.page-content.edit', 'about_hero') }}" aria-label="{{ __('Edit hero section') }}"
               class="absolute top-4 right-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                    <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                </svg>
            </a>
        @endif

        <div class="absolute inset-x-0 bottom-0 px-6 pb-10">
            <div class="mx-auto max-w-7xl">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('OUR COMPANY') }}</p>
                <h1 class="font-serif text-4xl font-semibold text-white mb-2">{{ $hero['heading'] }}</h1>
                <p class="text-white/75 max-w-xl">{{ $hero['body'] }}</p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-14">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="relative lg:col-span-2">
                @if ($canEditAboutUs)
                    <a href="{{ route('admin.page-content.edit', 'about_story') }}" aria-label="{{ __('Edit our story') }}"
                       class="absolute -top-2.5 right-0 z-20 flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                            <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                        </svg>
                    </a>
                @endif
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('OUR STORY') }}</p>
                <h2 class="font-serif text-2xl font-semibold text-ink mb-4">{{ $story['heading'] }}</h2>
                @foreach ($storyParagraphs as $paragraph)
                    <p class="text-ink/60 leading-relaxed {{ !$loop->last ? 'mb-4' : '' }}">{{ $paragraph }}</p>
                @endforeach
            </div>

            <div class="flex flex-col gap-4">
                @foreach ($stats as $stat)
                    <div class="relative border border-border rounded-lg px-4 py-3.5 flex items-center gap-3">
                        @if ($canEditAboutUs || $canDeleteAboutUs)
                            <div class="absolute -top-2.5 -left-2.5 z-20 flex items-center gap-1.5">
                                @if ($canEditAboutUs)
                                    <a href="{{ route('admin.stats.edit', $stat['id']) }}" aria-label="{{ __('Edit :label', ['label' => $stat['label']]) }}"
                                       class="flex h-6 w-6 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                            <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                                        </svg>
                                    </a>
                                @endif
                                @if ($canDeleteAboutUs)
                                    <form method="POST" action="{{ route('admin.stats.destroy', $stat['id']) }}" data-confirm="{{ __('Remove this stat?') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="{{ __('Remove :label', ['label' => $stat['label']]) }}"
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                                <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                        <span class="font-serif text-xl font-semibold text-gold">{{ $stat['value'] }}</span>
                        <span class="text-sm text-ink/60">{{ $stat['label'] }}</span>
                    </div>
                @endforeach

                @if ($canCreateAboutUs)
                    <a href="{{ route('admin.stats.create') }}"
                       class="border border-dashed border-border rounded-lg px-4 py-3.5 flex items-center justify-center gap-2 text-xs font-semibold text-ink/50 hover:text-ink hover:border-ink">
                        + {{ __('ADD STAT') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-16">
            <div class="flex items-start justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('TIMELINE') }}</p>
                    <h2 class="font-serif text-2xl font-semibold text-ink">{{ __('Our Project Portfolio') }}</h2>
                </div>
                @if ($canCreateAboutUs)
                    <a href="{{ route('admin.timeline.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                        + {{ __('ADD TIMELINE ENTRY') }}
                    </a>
                @endif
            </div>

            <div class="flex flex-col">
                @foreach ($timeline as $entry)
                    <div class="flex">
                        <div class="w-14 shrink-0 pt-1">
                            <span class="font-serif text-lg font-semibold text-gold">{{ $entry['year'] }}</span>
                        </div>
                        <div class="relative w-10 lg:w-[26rem] shrink-0">
                            @if (!$loop->last)
                                <span class="absolute top-0 bottom-0 start-[1.25rem] lg:start-[15.5rem] w-px bg-border"></span>
                            @endif
                            <div class="flex items-center h-4 mt-1">
                                <span class="shrink-0 w-3 lg:w-60"></span>
                                <span class="relative flex h-4 w-4 shrink-0 items-center justify-center rounded-full border border-border bg-cream">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                                </span>
                                <span class="flex-1 h-px bg-border"></span>
                            </div>
                        </div>
                        <div class="flex-1 pb-6">
                            <div class="relative">
                                @if ($canEditAboutUs || $canDeleteAboutUs)
                                    <div class="absolute -top-2.5 -left-2.5 z-20 flex items-center gap-1.5">
                                        @if ($canEditAboutUs)
                                            <a href="{{ route('admin.timeline.edit', $entry['id']) }}" aria-label="{{ __('Edit :title', ['title' => $entry['title']]) }}"
                                               class="flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($canDeleteAboutUs)
                                            <form method="POST" action="{{ route('admin.timeline.destroy', $entry['id']) }}" data-confirm="{{ __('Remove :title from the timeline?', ['title' => $entry['title']]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" aria-label="{{ __('Remove :title', ['title' => $entry['title']]) }}"
                                                        class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                                        <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                                <div class="border border-border rounded-lg bg-surface overflow-hidden flex flex-col sm:flex-row">
                                    <x-nakheel.ph :seed="$entry['image']" class="w-full h-52 sm:h-72 sm:w-1/2 shrink-0" />
                                    <div class="p-5">
                                        <x-nakheel.status-pill :label="$entry['status']" :color="$entry['color'] ?? 'emerald'" />
                                        <h3 class="font-serif font-semibold text-ink mb-1 mt-2">{{ $entry['title'] }}</h3>
                                        <p class="text-xs text-ink/40 mb-2">{{ $entry['meta'] }}</p>
                                        <p class="text-sm text-ink/60 leading-relaxed">{{ $entry['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div>
                    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('CATEGORIES') }}</p>
                    <h2 class="font-serif text-2xl font-semibold text-ink">{{ __('Real Estate Categories') }}</h2>
                </div>
                @if ($canCreateAboutUs)
                    <a href="{{ route('admin.categories.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                        + {{ __('ADD CATEGORY') }}
                    </a>
                @endif
            </div>
            <p class="text-ink/60 max-w-2xl mb-8">
                {{ __('Nakheel operates across three distinct real estate verticals, each with its own design language, market positioning, and development philosophy.') }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($categories as $category)
                    <div class="relative h-full">
                        @if ($canEditAboutUs || $canDeleteAboutUs)
                            <div class="absolute -top-2.5 -left-2.5 z-20 flex items-center gap-1.5">
                                @if ($canEditAboutUs)
                                    <a href="{{ route('admin.categories.edit', $category['id']) }}" aria-label="{{ __('Edit :title', ['title' => $category['title']]) }}"
                                       class="flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                            <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                                        </svg>
                                    </a>
                                @endif
                                @if ($canDeleteAboutUs)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category['id']) }}" data-confirm="{{ __('Remove :title?', ['title' => $category['title']]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="{{ __('Remove :title', ['title' => $category['title']]) }}"
                                                class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                                <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                        <div class="h-full border border-border rounded-lg overflow-hidden flex flex-col">
                            <x-nakheel.ph :seed="$category['image']" class="h-40 w-full shrink-0" />
                            <div class="p-4">
                                <h3 class="font-serif font-semibold text-ink mb-2">{{ $category['title'] }}</h3>
                                <p class="text-sm text-ink/55 leading-relaxed">{{ $category['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative mt-14 bg-brand rounded-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">
            @if ($canEditAboutUs)
                <a href="{{ route('admin.page-content.edit', 'future_vision') }}" aria-label="{{ __('Edit our future vision') }}"
                   class="absolute top-3 right-3 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-gold text-ink shadow hover:bg-gold/90">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                    </svg>
                </a>
            @endif
            <div class="p-8 md:p-10 flex flex-col justify-center">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('A GROWING VISION') }}</p>
                <h2 class="font-serif text-2xl font-semibold text-white mb-4">{{ $futureVision['heading'] }}</h2>
                @foreach ($futureVisionParagraphs as $paragraph)
                    <p class="text-white/60 leading-relaxed mb-6">{{ $paragraph }}</p>
                @endforeach
                <a href="{{ route('future-projects') }}" class="inline-flex items-center gap-2 w-fit rounded-md bg-gold px-4 py-2.5 text-xs font-semibold text-ink hover:bg-gold/90">
                    {{ __('EXPLORE FUTURE PROJECTS') }} &rarr;
                </a>
            </div>
            <x-nakheel.ph :seed="$futureVision['image'] ?: 'nakheel-future-vision'" class="h-56 md:h-full w-full" />
        </div>

    </div>
@endsection
