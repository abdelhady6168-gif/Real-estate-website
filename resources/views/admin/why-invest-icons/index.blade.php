@extends('layouts.admin')

@section('title', 'Why Invest Icons')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-2">{{ __('Why Invest Icons') }}</h1>
    <p class="text-sm text-ink/55 mb-8 max-w-2xl">
        {{ __('Icons available for "Why Invest With Nakheel" reasons. Add more from the trusted icon library below, or remove ones you no longer want — icons already used by an existing reason can\'t be removed.') }}
    </p>

    @php
        $canAddIcons = auth()->user()?->hasPermission('why_invest_icons', 'create');
        $canRemoveIcons = auth()->user()?->hasPermission('why_invest_icons', 'delete');
    @endphp

    <div class="border border-border rounded-lg bg-surface p-6 mb-8">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('AVAILABLE ICONS') }} ({{ count($available) }})</p>

        @if (empty($available))
            <p class="text-sm text-ink/40">{{ __('No icons available yet.') }}</p>
        @else
            <div class="grid grid-cols-4 sm:grid-cols-8 gap-3">
                @foreach ($available as $key => $definition)
                    @php($inUse = $usage[$key] ?? 0)
                    <div class="relative">
                        <span class="flex h-14 w-14 items-center justify-center rounded-md border border-border bg-cream/40 text-ink/60" title="{{ __($definition['label']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                {!! implode('', $definition['paths']) !!}
                            </svg>
                        </span>
                        <p class="mt-1 text-center text-[10px] text-ink/45 truncate" title="{{ __($definition['label']) }}">{{ __($definition['label']) }}</p>

                        @if ($inUse > 0)
                            <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand/70 text-[9px] font-semibold text-white ring-2 ring-cream" title="{{ __('Used by :count reason(s)', ['count' => $inUse]) }}">
                                {{ $inUse }}
                            </span>
                        @elseif ($canRemoveIcons)
                            <form method="POST" action="{{ route('admin.why-invest-icons.destroy', $key) }}"
                                  data-confirm="{{ __('Remove this icon from the available library?') }}"
                                  class="absolute -top-1.5 -right-1.5">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="{{ __('Remove :icon', ['icon' => __($definition['label'])]) }}"
                                        class="flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600 text-[10px] leading-none">
                                    &times;
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($canAddIcons)
        <div class="border border-border rounded-lg bg-surface p-6">
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('ADD MORE ICONS') }}</p>

            @if (empty($more))
                <p class="text-sm text-ink/40">{{ __('Every icon in the library is already available.') }}</p>
            @else
                <form method="POST" action="{{ route('admin.why-invest-icons.store') }}" data-icon-search-form>
                    @csrf

                    <input type="text" data-icon-search placeholder="{{ __('Search icons…') }}"
                           class="mb-4 w-full max-w-xs rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">

                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-2" data-icon-grid>
                        @foreach ($more as $key => $definition)
                            <label data-icon-option data-icon-name="{{ strtolower(__($definition['label'])) }}"
                                   class="relative flex h-12 w-12 cursor-pointer items-center justify-center rounded-md border border-border text-ink/60 has-[:checked]:border-gold has-[:checked]:bg-gold/10 has-[:checked]:text-gold"
                                   title="{{ __($definition['label']) }}">
                                <input type="radio" name="key" value="{{ $key }}" class="sr-only" required>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    {!! implode('', $definition['paths']) !!}
                                </svg>
                            </label>
                        @endforeach
                    </div>

                    <p data-icon-no-results class="hidden mt-3 text-sm text-ink/40">{{ __('No icons match your search.') }}</p>

                    <button type="submit" class="mt-5 rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                        + {{ __('ADD SELECTED ICON') }}
                    </button>
                </form>
            @endif
        </div>
    @endif
@endsection
