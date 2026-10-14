<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Nakheel Properties</title>
    <script nonce="{{ Vite::cspNonce() }}">
        // Runs before paint (must stay inline, not bundled) so the page never
        // flashes the wrong theme. Mirrors the toggle logic in resources/js/theme.js
        // and the identical inline script in layouts/nakheel.blade.php — the two
        // layouts share the same `dark` class + localStorage convention, so the
        // theme picked on either side of the site carries over to the other.
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-ink font-sans antialiased min-h-screen">

    <header class="bg-brand text-white">
        <div class="mx-auto max-w-6xl px-6 h-16 flex items-center justify-between gap-8">
            <a href="{{ route('properties.index') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-gold text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5a1.5 1.5 0 0 1-1.5 1.5h-3a.75.75 0 0 1-.75-.75V15a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v5.25a.75.75 0 0 1-.75.75h-3a1.5 1.5 0 0 1-1.5-1.5v-6.88l-.97.97a.75.75 0 1 1-1.06-1.06l8.69-8.69Z" />
                    </svg>
                </span>
                <span class="flex items-baseline gap-1.5 leading-none">
                    <span class="font-serif text-lg font-semibold text-white">Nakheel</span>
                    <span class="text-[10px] font-medium tracking-[0.15em] text-white/50">ADMIN</span>
                </span>
            </a>

            <nav class="hidden sm:flex items-center gap-6 text-sm">
                @if (auth()->user()?->hasSectionAccess('projects'))
                    <a href="{{ route('admin.projects.index') }}" class="font-medium {{ request()->routeIs('admin.projects.*') ? 'text-white' : 'text-white/55 hover:text-white' }}">{{ __('Projects') }}</a>
                @endif
                @if (auth()->user()?->hasSectionAccess('properties'))
                    <a href="{{ route('properties.index') }}" class="font-medium {{ request()->routeIs('admin.properties.*') ? 'text-white' : 'text-white/55 hover:text-white' }}">{{ __('Properties') }}</a>
                @endif
                @if (auth()->user()?->hasSectionAccess('statuses'))
                    <a href="{{ route('admin.statuses.index') }}" class="font-medium {{ request()->routeIs('admin.statuses.*') ? 'text-white' : 'text-white/55 hover:text-white' }}">{{ __('Statuses') }}</a>
                @endif
                @if (auth()->user()?->hasSectionAccess('why_invest_icons'))
                    <a href="{{ route('admin.why-invest-icons.index') }}" class="font-medium {{ request()->routeIs('admin.why-invest-icons.*') ? 'text-white' : 'text-white/55 hover:text-white' }}">{{ __('Why Invest Icons') }}</a>
                @endif
                @if (auth()->user()?->isMainAdmin())
                    <a href="{{ route('admin.users.index') }}" class="font-medium {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-white/55 hover:text-white' }}">{{ __('Admin Management') }}</a>
                @endif
            </nav>

            <div class="flex items-center gap-4 shrink-0 text-sm">
                <button type="button" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-md border border-white/20 text-white/70 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 hidden dark:block">
                        <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 1 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM15.657 14.596l-1.06-1.06a.75.75 0 0 0-1.061 1.06l1.06 1.06a.75.75 0 0 0 1.061-1.06ZM6.464 5.404l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06a.75.75 0 0 0 1.06-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 dark:hidden">
                        <path d="M17.293 13.293A8 8 0 0 1 6.707 2.707a8.001 8.001 0 1 0 10.586 10.586Z" />
                    </svg>
                </button>

                <details class="relative">
                    <summary class="list-none cursor-pointer select-none rounded-md border border-white/20 px-3 py-1.5 text-xs text-white/70 flex items-center gap-1.5">
                        {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3 text-white/40"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.293l3.71-4.06a.75.75 0 1 1 1.08 1.04l-4.25 4.65a.75.75 0 0 1-1.08 0l-4.25-4.65a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                    </summary>
                    <div class="absolute {{ app()->getLocale() === 'ar' ? 'start-0' : 'end-0' }} mt-2 w-32 rounded-md border border-border bg-surface py-1 text-sm shadow-lg text-ink">
                        <a href="{{ route('language.switch', 'en') }}" class="block w-full px-3 py-1.5 text-start hover:bg-cream {{ app()->getLocale() === 'en' ? 'font-semibold text-ink' : 'text-ink/80' }}">English</a>
                        <a href="{{ route('language.switch', 'ar') }}" class="block w-full px-3 py-1.5 text-start hover:bg-cream {{ app()->getLocale() === 'ar' ? 'font-semibold text-ink' : 'text-ink/80' }}">العربية</a>
                    </div>
                </details>
                <a href="{{ route('home') }}" class="text-white/55 hover:text-white">{{ __('View site') }} &rarr;</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/55 hover:text-white">{{ __('Sign out') }}</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-10">
        @if (session('success'))
            <div data-flash-success class="mb-6 rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-md border border-red-300 bg-red-50 px-4 py-2.5 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
