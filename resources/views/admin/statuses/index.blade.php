@extends('layouts.admin')

@section('title', 'Property Statuses')

@php
    $canCreateStatuses = auth()->user()?->hasPermission('statuses', 'create');
    $canEditStatuses = auth()->user()?->hasPermission('statuses', 'edit');
    $canDeleteStatuses = auth()->user()?->hasPermission('statuses', 'delete');
@endphp

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
            <h1 class="font-serif text-3xl font-semibold text-ink">{{ __('Property Statuses') }}</h1>
            <p class="text-sm text-ink/50 mt-2 max-w-xl">{{ __('Controls the Availability filter on the Properties page, the Status field on the property form, and the colored dot on every property card.') }}</p>
        </div>
        @if ($canCreateStatuses)
            <a href="{{ route('admin.statuses.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                + {{ __('NEW STATUS') }}
            </a>
        @endif
    </div>

    @if (empty($statuses))
        <p class="border border-border rounded-lg bg-surface px-5 py-10 text-center text-ink/40">{{ __('No statuses yet.') }}</p>
    @else
        <p class="text-xs text-ink/40 mb-4">{{ __('Click a status to edit it. Use the − badge to delete.') }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($statuses as $status)
                <div class="relative border border-border rounded-lg bg-surface p-5 hover:border-gold transition-colors">
                    @if ($canEditStatuses)
                        <a href="{{ route('admin.statuses.edit', $status['id']) }}" class="absolute inset-0 rounded-lg z-10" aria-label="{{ __('Edit :name', ['name' => $status['name']]) }}"></a>
                    @endif

                    @if ($canDeleteStatuses)
                        <form method="POST" action="{{ route('admin.statuses.destroy', $status['id']) }}"
                              class="absolute -top-2.5 -right-2.5 z-20"
                              data-confirm="{{ __('Delete the :name status?', ['name' => $status['name']]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="{{ __('Delete :name', ['name' => $status['name']]) }}"
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    @endif

                    <div class="flex items-center gap-2.5">
                        <span class="h-2.5 w-2.5 rounded-full {{ \App\Services\NakheelPropertyStatusService::colorClass($status['color']) }}"></span>
                        <p class="font-serif font-semibold text-ink">{{ $status['name'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
