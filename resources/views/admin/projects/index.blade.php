@extends('layouts.admin')

@section('title', 'Projects')

@php
    $canCreateProjects = auth()->user()?->hasPermission('projects', 'create');
    $canEditProjects = auth()->user()?->hasPermission('projects', 'edit');
    $canDeleteProjects = auth()->user()?->hasPermission('projects', 'delete');
@endphp

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
            <h1 class="font-serif text-3xl font-semibold text-ink">{{ __('Projects') }}</h1>
        </div>
        @if ($canCreateProjects)
            <a href="{{ route('admin.projects.create') }}" class="rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                + {{ __('NEW PROJECT') }}
            </a>
        @endif
    </div>

    @if (empty($projects))
        <p class="border border-border rounded-lg bg-surface px-5 py-10 text-center text-ink/40">{{ __('No projects yet.') }}</p>
    @else
        <p class="text-xs text-ink/40 mb-4">{{ __('Click a project to edit it. Use the − badge to delete.') }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($projects as $project)
                <div class="relative border border-border rounded-lg bg-surface p-5 hover:border-gold transition-colors">
                    @if ($canEditProjects)
                        <a href="{{ route('admin.projects.edit', $project['id']) }}" class="absolute inset-0 rounded-lg z-10" aria-label="{{ __('Edit :name', ['name' => $project['name']]) }}"></a>
                    @endif

                    @if ($canDeleteProjects)
                        <form method="POST" action="{{ route('admin.projects.destroy', $project['id']) }}"
                              class="absolute -top-2.5 -right-2.5 z-20"
                              data-confirm="{{ __('Delete :name? Properties in this project will become unassigned.', ['name' => $project['name']]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="{{ __('Delete :name', ['name' => $project['name']]) }}"
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    @endif

                    <div>
                        <p class="font-serif font-semibold text-ink mb-1">{{ $project['name'] }}</p>
                        <p class="text-xs text-ink/50 mb-3">{{ $project['type'] }} &middot; {{ $project['location'] }}</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-ink/60">{{ $project['status'] }}</span>
                            <span class="text-ink/60">{{ __(':available / :total units', ['available' => $project['available_units'], 'total' => $project['total_units']]) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
