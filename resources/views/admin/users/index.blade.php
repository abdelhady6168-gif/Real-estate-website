@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
            <h1 class="font-serif text-3xl font-semibold text-ink">{{ __('Admin Management') }}</h1>
            <p class="text-sm text-ink/50 mt-2 max-w-xl">{{ __('Create Sub Admin accounts and control exactly which sections of the site each one can manage.') }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            + {{ __('CREATE SUB ADMIN') }}
        </a>
    </div>

    @if ($admins->isEmpty())
        <p class="border border-border rounded-lg bg-surface px-5 py-10 text-center text-ink/40">{{ __('No Sub Admins yet.') }}</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach ($admins as $admin)
                @php
                    $sectionLabels = collect($admin->permissions ?? [])
                        ->keys()
                        ->map(fn ($key) => __(\App\Support\AdminPermissions::sections()[$key]['label'] ?? $key))
                        ->implode(', ');
                @endphp
                <div class="border border-border rounded-lg bg-surface p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="font-serif font-semibold text-ink">{{ $admin->email }}</p>
                            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mt-1">{{ __('SUB ADMIN') }}</p>
                        </div>
                        <x-nakheel.status-pill :label="$admin->isDisabled() ? __('Disabled') : __('Active')" :color="$admin->isDisabled() ? 'gray' : 'emerald'" />
                    </div>

                    <p class="text-xs text-ink/50 mb-4 min-h-[2.5em]">
                        {{ $sectionLabels !== '' ? $sectionLabels : __('No permissions granted yet.') }}
                    </p>

                    <div class="flex items-center gap-3 text-xs font-medium border-t border-border pt-3">
                        <a href="{{ route('admin.users.edit', $admin) }}" class="text-ink/60 hover:text-ink">{{ __('Edit') }}</a>
                        <a href="{{ route('admin.users.password.edit', $admin) }}" class="text-ink/60 hover:text-ink">{{ __('Reset Password') }}</a>
                        <form method="POST" action="{{ route('admin.users.toggle', $admin) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-ink/60 hover:text-ink">
                                {{ $admin->isDisabled() ? __('Enable') : __('Disable') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $admin) }}"
                              data-confirm="{{ __('Remove :email? This cannot be undone.', ['email' => $admin->email]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
