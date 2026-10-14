@extends('layouts.admin')

@section('title', 'Reset Password')

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Reset Password') }}</h1>

    <form method="POST" action="{{ route('admin.users.password.update', $admin) }}" class="space-y-8">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="border border-border rounded-lg bg-surface p-6">
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-1">{{ __('SUB ADMIN') }}</p>
            <p class="font-serif text-lg font-semibold text-ink mb-5">{{ $admin->email }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @include('admin.users._password-fields', ['passwordLabel' => 'New Password', 'confirmLabel' => 'Confirm New Password'])
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                {{ __('UPDATE PASSWORD') }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
