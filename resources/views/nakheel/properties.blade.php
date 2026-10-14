@extends('layouts.nakheel')

@section('title', 'Available Properties — Nakheel Properties')

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12">

        <div class="flex items-start justify-between gap-4 mb-3">
            <div>
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('PORTFOLIO') }}</p>
                <h1 class="font-serif text-4xl font-semibold text-ink">{{ __('Available Properties') }}</h1>
            </div>
            @if (auth()->user()?->hasPermission('properties', 'create'))
                <a href="{{ route('admin.properties.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                    + {{ __('ADD PROPERTY') }}
                </a>
            @endif
        </div>
        <p class="text-ink/60 max-w-2xl mb-8">
            {{ __('Browse our currently available real-estate opportunities across Tripoli, the Mediterranean Coast, and Benghazi.') }}
        </p>

        @if ($canManageSoldVisibility)
            <div class="bg-surface border border-border rounded-lg p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold tracking-wider text-ink/50 mb-1">{{ __('SOLD PROPERTIES VISIBILITY') }}</p>
                    <p class="text-sm text-ink/70">{{ __('Show Sold properties to normal users') }}</p>
                </div>
                @php
                    $btnOn = 'inline-flex items-center gap-2.5 rounded-full border border-border px-1 py-1 text-xs font-semibold transition-colors bg-emerald-500/10 text-emerald-700';
                    $btnOff = 'inline-flex items-center gap-2.5 rounded-full border border-border px-1 py-1 text-xs font-semibold transition-colors bg-brand/5 text-ink/50';
                    $trackOn = 'relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-emerald-500';
                    $trackOff = 'relative inline-flex h-5 w-9 items-center rounded-full transition-colors bg-brand/20 dark:bg-white/20';
                    $thumbOn = 'inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform translate-x-[18px]';
                    $thumbOff = 'inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform translate-x-1';
                @endphp
                <button type="button" data-sold-toggle
                        data-endpoint="{{ route('admin.settings.sold-visibility') }}"
                        data-value="{{ $showSoldProperties ? '1' : '0' }}"
                        data-on-button-class="{{ $btnOn }}" data-off-button-class="{{ $btnOff }}"
                        data-on-track-class="{{ $trackOn }}" data-off-track-class="{{ $trackOff }}"
                        data-on-thumb-class="{{ $thumbOn }}" data-off-thumb-class="{{ $thumbOff }}"
                        data-on-label="{{ __('ON') }}" data-off-label="{{ __('OFF') }}"
                        aria-pressed="{{ $showSoldProperties ? 'true' : 'false' }}"
                        class="{{ $showSoldProperties ? $btnOn : $btnOff }}">
                    <span data-sold-toggle-track class="{{ $showSoldProperties ? $trackOn : $trackOff }}">
                        <span data-sold-toggle-thumb class="{{ $showSoldProperties ? $thumbOn : $thumbOff }}"></span>
                    </span>
                    <span data-sold-toggle-label>{{ $showSoldProperties ? __('ON') : __('OFF') }}</span>
                </button>
            </div>
        @endif

        <form method="GET" action="{{ route('properties.index') }}" data-current-sort="{{ $sort }}" class="bg-surface border border-border rounded-lg p-5 mb-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $typeOptions = collect([['value' => 'All Types', 'label' => __('All Types')]])
                        ->concat(collect($types)->map(fn ($type) => ['value' => $type, 'label' => $typeLabels[$type] ?? $type]));
                    $projectOptions = collect([['value' => 'All Projects', 'label' => __('All Projects')]])
                        ->concat(collect($projects)->map(fn ($project) => ['value' => $project, 'label' => $projectLabels[$project] ?? $project]));
                    $locationOptions = collect([['value' => 'All Locations', 'label' => __('All Locations')]])
                        ->concat(collect($locations)->map(fn ($location) => ['value' => $location, 'label' => $locationLabels[$location] ?? $location]));
                    $availabilityOptions = collect([['value' => 'All', 'label' => __('All')]])
                        ->concat(collect($statuses)->map(fn ($status) => ['value' => $status, 'label' => $statusLabels[$status] ?? $status]));
                @endphp

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('PROPERTY TYPE') }}</span>
                    <x-nakheel.filter-select name="type" :value="request('type', 'All Types')" :options="$typeOptions" />
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('PROJECT') }}</span>
                    <x-nakheel.filter-select name="project" :value="request('project', 'All Projects')" :options="$projectOptions" />
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('LOCATION') }}</span>
                    <x-nakheel.filter-select name="location" :value="request('location', 'All Locations')" :options="$locationOptions" />
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('AVAILABILITY') }}</span>
                    <x-nakheel.filter-select name="availability" :value="request('availability', 'All')" :options="$availabilityOptions" />
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('MIN PRICE (L.D. K)') }}</span>
                    <input type="number" name="min" value="{{ request('min', 100) }}" class="rounded-md border border-border bg-cream/40 px-2.5 py-2 text-sm text-ink">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('MAX PRICE (L.D. K)') }}</span>
                    <input type="number" name="max" value="{{ request('max', 700) }}" class="rounded-md border border-border bg-cream/40 px-2.5 py-2 text-sm text-ink">
                </label>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-semibold tracking-wider text-ink/50 me-1">{{ __('SORT:') }}</span>
                    @foreach (['newest' => __('Newest'), 'price_asc' => __('Price: Low to High'), 'price_desc' => __('Price: High to Low')] as $value => $label)
                        <button type="submit" name="sort" value="{{ $value }}"
                            class="text-xs font-medium px-3 py-1.5 rounded-md border {{ $sort === $value ? 'bg-brand text-white border-ink' : 'border-border text-ink/60 hover:text-ink' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 text-xs text-ink/50">
                    <span><span class="font-semibold text-ink">{{ $properties->count() }}</span> {{ __('properties') }}</span>
                    <a href="{{ route('properties.index') }}" class="font-medium text-gold hover:underline">{{ __('RESET FILTERS') }}</a>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($properties as $property)
                <x-nakheel.property-card :property="$property" />
            @empty
                <p class="col-span-full text-center text-ink/50 py-16">{{ __('No properties match your filters.') }}</p>
            @endforelse
        </div>
    </div>
@endsection
