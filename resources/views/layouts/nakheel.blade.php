<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nakheel Properties')</title>
    <script nonce="{{ Vite::cspNonce() }}">
        // Runs before paint (must stay inline, not bundled) so the page never
        // flashes the wrong theme. Mirrors the toggle logic in resources/js/theme.js.
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
<body class="bg-cream text-ink font-sans antialiased">

    <header class="sticky top-0 z-40 bg-surface border-b border-border">
        <div class="mx-auto max-w-7xl px-6 h-16 flex items-center justify-between gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5a1.5 1.5 0 0 1-1.5 1.5h-3a.75.75 0 0 1-.75-.75V15a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v5.25a.75.75 0 0 1-.75.75h-3a1.5 1.5 0 0 1-1.5-1.5v-6.88l-.97.97a.75.75 0 1 1-1.06-1.06l8.69-8.69Z" />
                    </svg>
                </span>
                <span class="flex items-baseline gap-1.5 leading-none">
                    <span class="font-serif text-lg font-semibold text-ink">Nakheel</span>
                    <span class="text-[10px] font-medium tracking-[0.15em] text-ink/70">PROPERTIES</span>
                </span>
            </a>

            @php
                // Shared by the desktop <nav> below AND the phone/tablet drawer
                // further down this file — one list, two responsive layouts.
                $navItems = [
                    ['label' => __('Properties'), 'route' => 'properties.index', 'active' => request()->routeIs('properties.index') || request()->routeIs('home')],
                    ['label' => __('Projects'), 'route' => 'projects.index', 'active' => request()->routeIs('projects.*')],
                    ['label' => __('About Us'), 'route' => 'about', 'active' => request()->routeIs('about')],
                    ['label' => __('Future Projects'), 'route' => 'future-projects', 'active' => request()->routeIs('future-projects')],
                ];
            @endphp

            {{-- lg and up only — below that, the drawer after </header> takes over. --}}
            <nav class="hidden lg:flex items-center gap-8 text-sm">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="relative pb-[22px] pt-[22px] font-medium transition-colors {{ $item['active'] ? 'text-ink' : 'text-ink/55 hover:text-ink' }}">
                        {{ $item['label'] }}
                        @if ($item['active'])
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gold"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- lg and up only — the drawer after </header> carries these same
                 controls (theme/language/account) for phone and tablet. --}}
            <div class="hidden lg:flex items-center gap-3 shrink-0">
                <button type="button" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-md border border-border text-ink/70 hover:text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 hidden dark:block">
                        <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 1 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM15.657 14.596l-1.06-1.06a.75.75 0 0 0-1.061 1.06l1.06 1.06a.75.75 0 0 0 1.061-1.06ZM6.464 5.404l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06a.75.75 0 0 0 1.06-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 dark:hidden">
                        <path d="M17.293 13.293A8 8 0 0 1 6.707 2.707a8.001 8.001 0 1 0 10.586 10.586Z" />
                    </svg>
                </button>

                <details class="relative">
                    <summary class="list-none cursor-pointer select-none rounded-md border border-border px-3 py-1.5 text-xs text-ink/70 flex items-center gap-1.5">
                        {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3 text-ink/40"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.293l3.71-4.06a.75.75 0 1 1 1.08 1.04l-4.25 4.65a.75.75 0 0 1-1.08 0l-4.25-4.65a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                    </summary>
                    <div class="absolute {{ app()->getLocale() === 'ar' ? 'start-0' : 'end-0' }} mt-2 w-32 rounded-md border border-border bg-surface py-1 text-sm shadow-lg">
                        <a href="{{ route('language.switch', 'en') }}" class="block w-full px-3 py-1.5 text-start hover:bg-cream {{ app()->getLocale() === 'en' ? 'font-semibold text-ink' : 'text-ink/80' }}">English</a>
                        <a href="{{ route('language.switch', 'ar') }}" class="block w-full px-3 py-1.5 text-start hover:bg-cream {{ app()->getLocale() === 'ar' ? 'font-semibold text-ink' : 'text-ink/80' }}">العربية</a>
                    </div>
                </details>

                <details class="relative">
                    <summary class="list-none cursor-pointer select-none flex items-center gap-1">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-xs font-semibold text-white">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'KA' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3 text-ink/40"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.293l3.71-4.06a.75.75 0 1 1 1.08 1.04l-4.25 4.65a.75.75 0 0 1-1.08 0l-4.25-4.65a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                    </summary>
                    <div class="absolute end-0 mt-2 w-44 rounded-md border border-border bg-surface py-1 text-sm shadow-lg">
                        @if (auth()->user()?->hasSectionAccess('projects'))
                            <a href="{{ route('admin.projects.index') }}" class="block px-3 py-1.5 text-start text-ink/80 hover:bg-cream">{{ __('Manage Listings') }}</a>
                        @endif
                        @if (auth()->check())
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-3 py-1.5 text-start text-ink/80 hover:bg-cream">{{ __('Sign out') }}</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-1.5 text-start text-ink/80 hover:bg-cream">{{ __('Sign in') }}</a>
                        @endif
                    </div>
                </details>
            </div>

            <button type="button" data-mobile-nav-toggle aria-label="{{ __('Open menu') }}" aria-expanded="false" aria-controls="mobile-nav-panel"
                    class="lg:hidden flex h-9 w-9 items-center justify-center rounded-md border border-border text-ink/70 hover:text-ink shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                    <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 10.5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </header>

    {{--
        Phone/tablet nav drawer — only reachable below `lg` (1024px, the
        same breakpoint the desktop <nav>/controls above hide behind), via
        the hamburger button in the header. Desktop is untouched: at `lg`
        and up this whole block stays hidden and inert.

        Backdrop + panel are plain siblings placed after </header> (not
        nested in it) so they paint above the sticky header regardless of
        z-index specifics, and so the panel's own {{ $navItems }} reuse
        stays simple. See resources/js/mobile-nav.js for the open/close
        logic (click, backdrop click, Escape) — this only toggles a
        data-state attribute; Tailwind's data-[state=...] variants (plus
        `rtl:` for which side it slides from) do the rest.
    --}}
    <div class="lg:hidden">
        <div data-mobile-nav-backdrop data-state="closed" aria-hidden="true"
             class="fixed inset-0 z-40 bg-brand/25 backdrop-blur-sm transition-opacity duration-300 ease-in-out
                    data-[state=closed]:opacity-0 data-[state=closed]:pointer-events-none data-[state=open]:opacity-100"></div>

        <div id="mobile-nav-panel" data-mobile-nav-panel data-state="closed" role="dialog" aria-modal="true" aria-label="{{ __('Navigation menu') }}"
             class="fixed inset-y-0 start-0 z-50 w-72 max-w-[85vw] bg-surface border-e border-border shadow-xl
                    flex flex-col overflow-y-auto
                    transition-transform duration-300 ease-in-out
                    data-[state=closed]:-translate-x-full data-[state=closed]:rtl:translate-x-full
                    data-[state=open]:translate-x-0">

            <div class="flex items-center justify-between gap-3 px-5 h-16 border-b border-border shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5a1.5 1.5 0 0 1-1.5 1.5h-3a.75.75 0 0 1-.75-.75V15a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v5.25a.75.75 0 0 1-.75.75h-3a1.5 1.5 0 0 1-1.5-1.5v-6.88l-.97.97a.75.75 0 1 1-1.06-1.06l8.69-8.69Z" />
                        </svg>
                    </span>
                    <span class="flex items-baseline gap-1.5 leading-none">
                        <span class="font-serif text-lg font-semibold text-ink">Nakheel</span>
                        <span class="text-[10px] font-medium tracking-[0.15em] text-ink/70">PROPERTIES</span>
                    </span>
                </a>
                <button type="button" data-mobile-nav-close aria-label="{{ __('Close') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-full text-ink/60 hover:bg-cream hover:text-ink shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>

            <nav class="flex flex-col py-2">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-5 py-3 text-sm font-medium border-s-2 {{ $item['active'] ? 'text-ink border-gold bg-cream/60' : 'text-ink/65 border-transparent hover:bg-cream/40 hover:text-ink' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto border-t border-border px-5 py-4 space-y-4">
                <button type="button" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}"
                        class="flex w-full items-center gap-2.5 rounded-md border border-border px-3 py-2 text-sm text-ink/70 hover:text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 hidden dark:block">
                        <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 1 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM15.657 14.596l-1.06-1.06a.75.75 0 0 0-1.061 1.06l1.06 1.06a.75.75 0 0 0 1.061-1.06ZM6.464 5.404l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06a.75.75 0 0 0 1.06-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 dark:hidden">
                        <path d="M17.293 13.293A8 8 0 0 1 6.707 2.707a8.001 8.001 0 1 0 10.586 10.586Z" />
                    </svg>
                    <span class="dark:hidden">{{ __('Dark mode') }}</span>
                    <span class="hidden dark:inline">{{ __('Light mode') }}</span>
                </button>

                <div class="flex items-center gap-2 text-sm">
                    <a href="{{ route('language.switch', 'en') }}" class="flex-1 rounded-md border border-border px-3 py-2 text-center {{ app()->getLocale() === 'en' ? 'font-semibold text-ink bg-cream/60' : 'text-ink/70' }}">English</a>
                    <a href="{{ route('language.switch', 'ar') }}" class="flex-1 rounded-md border border-border px-3 py-2 text-center {{ app()->getLocale() === 'ar' ? 'font-semibold text-ink bg-cream/60' : 'text-ink/70' }}">العربية</a>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-xs font-semibold text-white shrink-0">
                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'KA' }}
                    </span>
                    <div class="flex flex-col items-start gap-1 text-sm">
                        @if (auth()->user()?->hasSectionAccess('projects'))
                            <a href="{{ route('admin.projects.index') }}" class="text-ink/75 hover:text-ink">{{ __('Manage Listings') }}</a>
                        @endif
                        @if (auth()->check())
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-ink/75 hover:text-ink">{{ __('Sign out') }}</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-ink/75 hover:text-ink">{{ __('Sign in') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script nonce="{{ Vite::cspNonce() }}">
        // Same reasoning as the dark-mode script in <head>: this must run
        // synchronously, before the browser paints, or the drawer above —
        // hidden purely via CSS classes (data-[state=closed]:..., lg:hidden)
        // — can flash briefly at full, unstyled size on a fast navigation
        // if that CSS hasn't taken effect yet. Setting the same "closed"
        // look directly as inline styles here closes that gap; mobile-nav.js
        // clears these inline styles as soon as it loads and hands control
        // back to the CSS classes for the real open/close toggling.
        (function () {
            var panel = document.querySelector('[data-mobile-nav-panel]');
            var backdrop = document.querySelector('[data-mobile-nav-backdrop]');
            var rtl = document.documentElement.dir === 'rtl';
            if (panel) panel.style.transform = rtl ? 'translateX(100%)' : 'translateX(-100%)';
            if (backdrop) {
                backdrop.style.opacity = '0';
                backdrop.style.pointerEvents = 'none';
            }
        })();
    </script>

    <main>
        @if (session('success'))
            <div class="mx-auto max-w-7xl px-6 pt-6">
                <div data-flash-success class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto max-w-7xl px-6 pt-6">
                <div class="rounded-md border border-red-300 bg-red-50 px-4 py-2.5 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-brand text-white/70">
        <div class="mx-auto max-w-7xl px-6 py-12 text-sm">
            <span class="flex items-baseline gap-1.5 leading-none mb-3">
                <span class="font-serif text-lg font-semibold text-white">Nakheel</span>
                <span class="text-[10px] font-medium tracking-[0.15em] text-white/50">PROPERTIES</span>
            </span>
            <p class="text-white/50 leading-relaxed max-w-sm">{{ __("Building Libya's finest addresses since 2012.") }}</p>
        </div>
        <div class="border-t border-white/10 py-5 text-center text-xs text-white/40">
            © {{ date('Y') }} {{ __('Nakheel Properties. All rights reserved.') }}
        </div>
    </footer>

</body>
</html>
