{{--
    Rendered automatically by Laravel for any abort(403) (no route wiring
    needed — see EnsureUserHasPermission / EnsureUserIsMainAdmin / EnsureUserIsAdmin).
    Kept intentionally minimal and self-contained (no shared layout) so it
    never depends on data that itself could be part of what's unauthorized.
--}}
@php
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Access Denied') }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-ink font-sans antialiased min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-sm text-center">
        <span class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-7 w-7">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
        </span>

        <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('403') }}</p>
        <h1 class="font-serif text-3xl font-semibold text-ink mb-3">{{ __('Access Denied') }}</h1>
        <p class="text-sm text-ink/55 mb-8">
            {{ __("You don't have permission to view this page.") }}
        </p>

        <a href="{{ $user ? route('properties.index') : route('login') }}"
           class="inline-block rounded-md bg-ink px-5 py-2.5 text-xs font-semibold text-white hover:bg-ink/90">
            {{ $user ? __('RETURN TO WEBSITE') : __('GO TO LOGIN') }}
        </a>
    </div>

</body>
</html>
