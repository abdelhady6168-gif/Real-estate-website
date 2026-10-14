@props(['property'])

@php
    $images = $property['images'] ?? [];
    $baseSeed = $images[0] ?? $property['title'];
    $dot = $property['statusColor'] ?? 'bg-gray-400';
    $hasProject = filled($property['projectSlug'] ?? null);
    $canEditProperty = auth()->user()?->hasPermission('properties', 'edit');
    $canDeleteProperty = auth()->user()?->hasPermission('properties', 'delete');
    $modalId = 'property-modal-' . $property['id'];
    $contactModalId = 'contact-modal-' . $property['id'];
    $galleryId = 'property-gallery-' . $property['id'];
    $floorPlanGalleryId = 'property-floorplans-' . $property['id'];

    $specsLine = collect($property['specs'] ?? [])
        ->map(fn ($spec) => trim(($spec['value'] ?? '').' '.($spec['label'] ?? '')))
        ->filter()
        ->implode(' · ');

    // A property's type is one free-text field; admins list several types
    // in it separated by commas/slashes (e.g. "Villas, Apartments, Townhouses").
    // Presentation only: below desktop width (lg) a long multi-type badge
    // would run across the photo into the status pill, so there it becomes a
    // compact list in the card body instead — always on mobile, and on
    // tablet only past three types (fewer just wrap inside a narrower badge).
    // Desktop keeps the original single badge exactly as before.
    $types = preg_split('/\s*[,،\/]\s*/u', (string) $property['typeLabel'], -1, PREG_SPLIT_NO_EMPTY);
    $multiType = count($types) > 1;
    $manyTypes = count($types) > 3;
    $typeBadgeResponsive = match (true) {
        ! $multiType => '',
        $manyTypes => 'max-lg:hidden',
        default => 'max-sm:hidden sm:max-lg:max-w-[55%] sm:max-lg:break-words',
    };
    $typeListResponsive = $manyTypes ? 'lg:hidden' : 'sm:hidden';
@endphp

<div class="relative">
    @if ($canEditProperty || $canDeleteProperty)
        <div class="absolute -top-2.5 -left-2.5 z-20 flex items-center gap-1.5">
            @if ($canEditProperty)
                <a href="{{ route('admin.properties.edit', $property['id']) }}" aria-label="{{ __('Edit :title', ['title' => $property['title']]) }}"
                   class="flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                        <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                    </svg>
                </a>
            @endif
            @if ($canDeleteProperty)
                <form method="POST" action="{{ route('admin.properties.destroy', $property['id']) }}"
                      data-confirm="{{ __('Remove :title from the listings?', ['title' => $property['title']]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" aria-label="{{ __('Remove :title', ['title' => $property['title']]) }}"
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                            <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <div class="bg-surface border border-border rounded-lg overflow-hidden flex flex-col">
        <div class="relative aspect-[4/3]">
            <x-nakheel.ph :seed="$baseSeed" class="h-full w-full" />
            <span class="absolute top-2.5 left-2.5 bg-brand text-white text-[10px] font-semibold tracking-wider uppercase px-2 py-1 rounded {{ $typeBadgeResponsive }}">
                {{ $property['typeLabel'] }}
            </span>
            <span class="absolute top-2.5 right-2.5 flex items-center gap-1.5 bg-surface/95 text-ink text-[10px] font-medium px-2 py-1 rounded-full">
                <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                {{ $property['statusLabel'] }}
            </span>
        </div>

        <div class="p-4 flex flex-col gap-2 flex-1">
            <div class="flex items-start justify-between gap-2">
                <h3 class="font-serif font-semibold text-ink leading-snug">{{ $property['title'] }}</h3>
                <span class="font-serif font-semibold text-gold whitespace-nowrap"><x-nakheel.price :amount="$property['price']" /></span>
            </div>

            @if ($hasProject)
                <a href="{{ route('projects.show', $property['projectSlug']) }}" class="text-xs font-medium text-gold hover:underline w-fit">
                    {{ $property['projectLabel'] }} &rarr;
                </a>
            @else
                <span class="text-xs font-medium text-ink/40 w-fit">{{ $property['projectLabel'] }}</span>
            @endif

            <p class="flex items-center gap-1 text-xs text-ink/50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                {{ $property['locationLabel'] }}
            </p>

            @if ($multiType)
                <div class="{{ $typeListResponsive }}">
                    <p class="text-[10px] font-semibold tracking-wider uppercase text-ink/40 mb-1">{{ __('Property Types') }}</p>
                    <ul class="grid grid-cols-2 gap-x-3 gap-y-1">
                        @foreach ($types as $type)
                            <li class="flex items-start gap-1.5 min-w-0 text-xs text-ink/60">
                                <span class="mt-[5px] h-1 w-1 shrink-0 rounded-full bg-gold"></span>
                                <span class="min-w-0 break-words">{{ $type }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-xs text-ink/50">
                {{ $specsLine }}
            </p>

            <div class="mt-auto pt-2 grid grid-cols-2 gap-2">
                <button type="button" data-dialog-open="{{ $modalId }}"
                        class="text-center text-xs font-semibold uppercase tracking-wide bg-brand text-white rounded-md py-2 hover:bg-brand/90">
                    {{ __('View Details') }}
                </button>
                <button type="button" data-dialog-open="{{ $contactModalId }}"
                        class="text-center text-xs font-semibold uppercase tracking-wide border border-ink text-ink rounded-md py-2 hover:bg-brand/5">
                    {{ __('Contact Owner') }}
                </button>
            </div>
        </div>
    </div>

    @php $hasCalendar = !empty($property['hasAvailabilityCalendar']); @endphp
    <dialog id="{{ $modalId }}" data-dialog-backdrop-close
            class="m-auto p-0 border-0 rounded-lg bg-cream {{ $hasCalendar ? 'max-w-5xl' : 'max-w-3xl' }} w-[92vw] max-h-[88vh] overflow-y-auto backdrop:bg-brand/70">
        <div class="relative grid grid-cols-1 md:grid-cols-2 {{ $hasCalendar ? 'lg:grid-cols-3' : '' }}">
            <button type="button" data-dialog-close aria-label="{{ __('Close') }}"
                    class="absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-surface/90 text-ink shadow hover:bg-surface">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
            </button>

            <div class="p-1 space-y-3">
                <x-nakheel.gallery :images="$images" :id="$galleryId" :badge="$property['typeLabel']" :fallback-seed="$baseSeed" :label="__('Property Photos')" />
                <x-nakheel.gallery :images="$property['floorPlans'] ?? []" :id="$floorPlanGalleryId" :label="__('BLUEPRINT / FLOOR PLAN')" :placeholder="true" />
            </div>

            @if ($hasCalendar)
                <div class="p-1">
                    <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-2 px-1">{{ __('AVAILABILITY') }}</p>
                    <x-nakheel.availability-calendar :ranges="$property['unavailablePeriods'] ?? []" :display-ranges="$property['availabilityRanges'] ?? []" />
                </div>
            @endif

            <div class="p-6 flex flex-col {{ $hasCalendar ? 'md:col-span-2 lg:col-span-1' : '' }}">
                <span class="inline-flex items-center gap-1.5 w-fit bg-surface text-ink text-[10px] font-medium px-2 py-1 rounded-full border border-border mb-3">
                    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                    {{ $property['statusLabel'] }}
                </span>

                <h2 class="font-serif text-2xl font-semibold text-ink mb-1">{{ $property['title'] }}</h2>
                <p class="font-serif text-xl font-semibold text-gold mb-3"><x-nakheel.price :amount="$property['price']" /></p>

                <p class="flex items-center gap-1.5 text-sm text-ink/60 mb-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                    {{ $property['locationLabel'] }}
                </p>

                @if ($hasProject)
                    <a href="{{ route('projects.show', $property['projectSlug']) }}" class="flex items-center gap-1.5 text-sm font-medium text-gold hover:underline w-fit mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Z" clip-rule="evenodd" /></svg>
                        {{ __('Project: :name', ['name' => $property['projectLabel']]) }} &rarr;
                    </a>
                @endif

                @if (!empty($property['specs']))
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach ($property['specs'] as $stat)
                            <div class="border border-border rounded-md bg-surface py-2.5 text-center">
                                <p class="font-serif font-semibold text-ink">{{ $stat['value'] }}</p>
                                <p class="text-[10px] text-ink/50">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (filled($property['description'] ?? null))
                    <p class="text-sm text-ink/60 leading-relaxed mb-4">{{ $property['description'] }}</p>
                @endif

                @if (!empty($property['features']))
                    <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-2">{{ __('FEATURES') }}</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($property['features'] as $feature)
                            <span class="text-xs border border-border rounded-md px-3 py-1.5 text-ink/70">{{ $feature }}</span>
                        @endforeach
                    </div>
                @endif

                <button type="button" data-dialog-open="{{ $contactModalId }}"
                        class="mt-auto w-full text-center text-xs font-semibold uppercase tracking-wide bg-brand text-white rounded-md py-2.5 hover:bg-brand/90">
                    {{ __('Contact Owner') }}
                </button>
            </div>
        </div>
    </dialog>

    <x-nakheel.contact-modal :id="$contactModalId" :contact="$property['contact'] ?? []" />
</div>
