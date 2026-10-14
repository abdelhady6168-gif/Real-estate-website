<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sign in') }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    <div class="min-h-dvh bg-ink lg:grid lg:grid-cols-2">

        <div class="relative hidden lg:block">
            <x-nakheel.ph :seed="$featuredProperty['images'][0] ?? 'nakheel-auth-login'" class="absolute inset-0 h-full w-full" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/40"></div>

            <a href="{{ route('home') }}" class="absolute top-8 left-8 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-gold text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 1-1.06 1.06l-.97-.97V19.5a1.5 1.5 0 0 1-1.5 1.5h-3a.75.75 0 0 1-.75-.75V15a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v5.25a.75.75 0 0 1-.75.75h-3a1.5 1.5 0 0 1-1.5-1.5v-6.88l-.97.97a.75.75 0 1 1-1.06-1.06l8.69-8.69Z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold tracking-[0.15em] text-white">NAKHEEL PROPERTIES</span>
            </a>

            <div class="absolute inset-x-0 bottom-0 p-10">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('FEATURED PROPERTY') }}</p>
                @if ($featuredProperty)
                    <h2 class="font-serif italic text-4xl text-white mb-3">{{ $featuredProperty['title'] }}{{ $featuredProperty['project'] !== 'Unassigned' ? ', '.$featuredProperty['project'] : '' }}</h2>
                    <p class="flex items-center gap-3 text-sm text-white/80 mb-4">
                        @foreach ($featuredProperty['specs'] as $spec)
                            @if (!$loop->first)
                                <span class="text-white/30">&middot;</span>
                            @endif
                            <span>{{ $spec['value'] }} {{ $spec['label'] }}</span>
                        @endforeach
                        <span class="text-white/30">&middot;</span>
                        <span class="text-gold font-semibold"><x-nakheel.price :amount="$featuredProperty['price']" /></span>
                    </p>
                @else
                    <h2 class="font-serif italic text-4xl text-white mb-3">{{ __('Nakheel Properties') }}</h2>
                @endif
                <p class="text-sm text-white/60 max-w-sm leading-relaxed">
                    {{ __('Sign in to access exclusive listings, schedule private viewings, and connect with your dedicated advisor.') }}
                </p>
            </div>
        </div>

        <div class="flex items-center justify-center bg-ink px-6 py-16 lg:py-0">
            <div class="w-full max-w-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold tracking-[0.15em] text-gold">{{ __('CLIENT PORTAL') }}</p>
                    <div class="flex items-center gap-2 text-xs">
                        <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-white font-semibold' : 'text-white/40 hover:text-white' }}">English</a>
                        <span class="text-white/20">/</span>
                        <a href="{{ route('language.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'text-white font-semibold' : 'text-white/40 hover:text-white' }}">العربية</a>
                    </div>
                </div>
                <h1 class="font-serif italic text-4xl text-white mb-2">{{ __('Welcome back') }}</h1>
                <p class="text-sm text-white/50 mb-8">{{ __('Access your personalized property dashboard.') }}</p>

                @if (session('status'))
                    <div class="mb-6 rounded-md border border-emerald-500/30 bg-emerald-500/10 px-4 py-2.5 text-sm text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-[10px] font-semibold tracking-[0.15em] text-white/40 mb-2">{{ __('EMAIL ADDRESS') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus
                               class="w-full bg-transparent border-b border-white/15 pb-2 text-white placeholder-white/25 focus:border-gold focus:outline-none">
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] font-semibold tracking-[0.15em] text-white/40 mb-2">{{ __('PASSWORD') }}</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   autocomplete="off" data-password-input
                                   class="w-full bg-transparent border-b border-white/15 pb-2 pr-8 text-white placeholder-white/25 focus:border-gold focus:outline-none">
                            <button type="button" data-password-toggle="password" tabindex="-1" aria-label="{{ __('Show password') }}"
                                    class="absolute right-0 bottom-2 text-white/40 hover:text-white/70">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                    <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-white/60">
                            <input type="checkbox" name="remember" value="1" class="rounded border-white/20 bg-transparent text-gold focus:ring-0 focus:ring-offset-0">
                            {{ __('Remember me') }}
                        </label>
                        <a href="{{ route('password.request') }}" class="text-gold hover:underline">{{ __('Forgot password?') }}</a>
                    </div>

                    <button type="submit" class="w-full rounded-md bg-gold py-3 text-xs font-semibold tracking-[0.15em] text-ink hover:bg-gold/90">
                        {{ __('SIGN IN') }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-white/50">
                    {{ __('No account?') }} <a href="{{ route('register') }}" class="text-gold hover:underline">{{ __('Sign Up') }}</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>
