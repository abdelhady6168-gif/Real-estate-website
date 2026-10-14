@extends('layouts.nakheel')

@section('title', $project['name'] . ' — Nakheel Properties')

@section('content')

    {{--
        data-project-content is the whole swappable region for this page —
        resources/js/project-nav.js intercepts clicks on the prev/next
        arrows below, fetches the target project's page, and replaces just
        this div's contents (plus the <title>) instead of a full browser
        navigation. Every other page keeps navigating normally; this is
        scoped to this one view. Existing site JS (galleries, modals, the
        mobile nav drawer, etc.) all listens on `document` via delegation
        already, so it keeps working on the swapped-in content with no
        re-initialization needed.
    --}}
    <div data-project-content>

    <div class="relative h-[380px] md:h-[440px] overflow-hidden">
        <x-nakheel.ph :seed="$project['heroImage'] ?: $project['slug']" class="absolute inset-0 h-full w-full" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-black/10 pointer-events-none"></div>

        @if ($previousProjectSlug)
            <a href="{{ route('projects.show', $previousProjectSlug) }}" data-project-nav aria-label="{{ __('Previous project') }}"
               class="absolute left-4 top-1/2 -translate-y-1/2 z-20 flex h-9 w-9 items-center justify-center rounded-md bg-surface/15 text-white hover:bg-surface/25">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" /></svg>
            </a>
            <a href="{{ route('projects.show', $nextProjectSlug) }}" data-project-nav aria-label="{{ __('Next project') }}"
               class="absolute right-4 top-1/2 -translate-y-1/2 z-20 flex h-9 w-9 items-center justify-center rounded-md bg-surface/15 text-white hover:bg-surface/25">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </a>
        @endif

        <div class="absolute inset-x-0 bottom-0 z-20 px-6 pb-8 md:px-10 md:pb-10 pointer-events-none">
            <div class="mx-auto max-w-7xl">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3 uppercase">{{ $project['type'] }}</p>
                <h1 class="font-serif text-4xl md:text-5xl font-semibold text-white mb-3">{{ $project['name'] }}</h1>
                <p class="flex items-center gap-1.5 text-white/80 text-sm mb-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                    {{ $project['location'] }}
                </p>
                @if (filled($project['tagline']))
                    <p class="italic text-white/70 text-sm">&ldquo;{{ $project['tagline'] }}&rdquo;</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="lg:col-span-2">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('ABOUT THIS PROJECT') }}</p>
                <h2 class="font-serif text-2xl font-semibold text-ink mb-4">{{ $project['name'] }}</h2>
                <p class="text-ink/60 leading-relaxed mb-6">{{ $project['description'] }}</p>

                <div class="grid grid-cols-2 gap-y-4 mb-6">
                    <div>
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('DEVELOPER') }}</p>
                        <p class="font-semibold text-ink text-sm">{{ $project['developer'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('PROJECT TYPE') }}</p>
                        <p class="font-semibold text-ink text-sm">{{ $project['type'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('STATUS') }}</p>
                        <p class="font-semibold text-ink text-sm">{{ $project['status'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('COMPLETION') }}</p>
                        <p class="font-semibold text-ink text-sm">{{ $project['completion'] }}</p>
                    </div>
                </div>

                <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('LOCATION') }}</p>
                <p class="font-semibold text-ink text-sm mb-3">{{ $project['aboutMaps']['locationName'] }}</p>
                <a href="{{ $project['aboutMaps']['url'] }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-md border border-ink px-4 py-2 text-xs font-semibold text-ink hover:bg-brand/5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                    {{ __('View on Google Maps') }}
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-1 gap-4">
                @foreach ($project['stats'] as $stat)
                    @php
                        // Property Types is a free-text, comma/slash-separated
                        // list (e.g. "Villas, Apartments"). In the narrow
                        // two-column tiles below desktop width, at this size a
                        // single word like "Apartments" is already wider than
                        // the tile, so there it becomes a compact list instead —
                        // always on mobile, and on tablet only past three types.
                        // Desktop (single wide column) keeps the original value.
                        // Only this stat: Total Units contains commas too ("1,200").
                        $types = $stat['label'] === __('Property Types')
                            ? preg_split('/\s*[,،\/]\s*/u', (string) $stat['value'], -1, PREG_SPLIT_NO_EMPTY)
                            : [];
                        $multiType = count($types) > 1;
                        $manyTypes = count($types) > 3;
                    @endphp
                    <div class="border border-border rounded-lg p-4 min-w-0">
                        {{-- A single type stays on one line and shrinks to fit its
                             tile if needed (resources/js/fit-text.js) rather than
                             breaking mid-word. --}}
                        <p @if (count($types) === 1) data-fit-text @endif
                           class="font-serif text-2xl font-semibold text-ink {{ $multiType ? ($manyTypes ? 'max-lg:hidden' : 'max-sm:hidden') : '' }}">{{ $stat['value'] }}</p>
                        @if ($multiType)
                            <ul class="space-y-1 {{ $manyTypes ? 'lg:hidden' : 'sm:hidden' }}">
                                @foreach ($types as $type)
                                    <li class="flex items-center gap-2 min-w-0 font-serif text-base font-semibold leading-snug text-ink">
                                        <span class="h-1 w-1 shrink-0 rounded-full bg-gold"></span>
                                        <span data-fit-text class="min-w-0 flex-1">{{ $type }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mt-1">{{ mb_strtoupper($stat['label']) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-12">
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-4">{{ __('PROJECT HIGHLIGHTS') }}</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($project['highlights'] as $highlight)
                    <div class="border border-border rounded-lg overflow-hidden bg-surface">
                        <div class="h-28 flex items-center justify-center bg-cream">
                            @if ($highlight['image'])
                                <x-nakheel.ph :seed="$highlight['image']" class="h-full w-full" />
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 text-gold"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                            @endif
                        </div>
                        <div class="border-t border-border px-2 py-3 text-center">
                            <p data-fit-text class="text-xs font-medium text-ink">{{ $highlight['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-12">
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-4">{{ __('PROJECT ENVIRONMENT') }}</p>
            @php
                $environmentImages = collect($project['environment'])->filter()->take(3)->values();
                $environmentCols = match ($environmentImages->count()) {
                    0, 1 => 'sm:grid-cols-1',
                    2 => 'sm:grid-cols-2',
                    default => 'sm:grid-cols-3',
                };
            @endphp
            <div class="grid grid-cols-1 {{ $environmentCols }} gap-4">
                @forelse ($environmentImages as $seed)
                    <x-nakheel.ph :seed="$seed" class="h-56 md:h-72 w-full rounded-lg overflow-hidden" />
                @empty
                    <x-nakheel.ph :seed="$project['slug']" class="h-56 md:h-72 w-full rounded-lg overflow-hidden" />
                @endforelse
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border border-border rounded-lg p-5">
            <div>
                <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('PROJECT MANAGER') }}</p>
                <p class="font-semibold text-ink">{{ $project['manager']['name'] }}</p>
                <p class="text-xs text-ink/50">{{ $project['manager']['role'] }}</p>
            </div>
            <button type="button" data-dialog-open="project-contact-modal"
                    class="rounded-md border border-ink px-4 py-2 text-xs font-semibold text-ink hover:bg-brand/5">
                {{ __('CONTACT MANAGER') }}
            </button>
        </div>

        <x-nakheel.contact-modal id="project-contact-modal" :contact="$project['manager']" />

        <div class="mt-16">
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2 text-center">{{ __('MORE UNITS') }}</p>
            <h2 class="font-serif text-2xl font-semibold text-ink mb-8 text-center">{{ __('Available Properties in :name', ['name' => $project['name']]) }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse ($related as $property)
                    <x-nakheel.property-card :property="$property" />
                @empty
                    <p class="col-span-full text-center text-ink/50 py-8">{{ __('No units currently listed for this project.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    </div>
@endsection
