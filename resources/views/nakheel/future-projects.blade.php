@extends('layouts.nakheel')

@section('title', 'Future Projects & Investment Opportunities — Nakheel Properties')

@section('content')

    @php
        $canCreateFutureProjects = auth()->user()?->hasPermission('future_projects', 'create');
        $canEditFutureProjects = auth()->user()?->hasPermission('future_projects', 'edit');
        $canDeleteFutureProjects = auth()->user()?->hasPermission('future_projects', 'delete');
        $canCreateWhyInvest = auth()->user()?->hasPermission('why_invest', 'create');
        $canEditWhyInvest = auth()->user()?->hasPermission('why_invest', 'edit');
        $canDeleteWhyInvest = auth()->user()?->hasPermission('why_invest', 'delete');
        $canEditContactInfo = auth()->user()?->hasPermission('contact_info', 'edit');
    @endphp

    <div class="relative h-[280px] md:h-[320px] overflow-hidden">
        <x-nakheel.ph :seed="$hero['image'] ?: 'nakheel-future-hero'" class="absolute inset-0 h-full w-full" />
        <div class="absolute inset-0 bg-black/60"></div>

        @if ($canEditFutureProjects)
            <a href="{{ route('admin.page-content.edit', 'future_projects_hero') }}" aria-label="{{ __('Edit hero section') }}"
               class="absolute top-4 right-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                    <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                </svg>
            </a>
        @endif

        <div class="absolute inset-x-0 bottom-0 px-6 pb-10">
            <div class="mx-auto max-w-7xl">
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('INVESTMENT') }}</p>
                <h1 class="font-serif text-3xl md:text-4xl font-semibold text-white mb-3 max-w-xl">{{ $hero['heading'] }}</h1>
                <p class="text-white/75 max-w-xl mb-5">
                    {{ $hero['body'] }}
                </p>
                <button type="button" data-dialog-open="office-contact-modal"
                        class="rounded-md bg-gold px-4 py-2.5 text-xs font-semibold text-ink hover:bg-gold/90">
                    {{ __('CONTACT OUR OFFICE') }}
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-14">

        @php
            $officeContactData = [
                'name' => $officeContact['heading'] ?? __('Nakheel Properties'),
                'phone' => $officeContact['phone'] ?? null,
                'whatsapp' => $officeContact['whatsapp'] ?? null,
                'telegram' => $officeContact['telegram'] ?? null,
                'email' => $officeContact['email'] ?? null,
                'address' => $officeContact['office_address'] ?? null,
                'hours' => $officeContact['office_hours'] ?? null,
            ];
        @endphp

        <div class="flex items-start justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('PIPELINE') }}</p>
                <h2 class="font-serif text-2xl font-semibold text-ink">{{ __('Upcoming Projects') }}</h2>
            </div>
            @if ($canCreateFutureProjects)
                <a href="{{ route('admin.upcoming.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                    + {{ __('ADD UPCOMING PROJECT') }}
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-16">
            @foreach ($upcoming as $item)
                <div class="relative">
                    @if ($canEditFutureProjects || $canDeleteFutureProjects)
                        <div class="absolute -top-2.5 -right-2.5 z-20 flex items-center gap-1.5">
                            @if ($canEditFutureProjects)
                                <a href="{{ route('admin.upcoming.edit', $item['id']) }}" aria-label="{{ __('Edit :title', ['title' => $item['title']]) }}"
                                   class="flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                        <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($canDeleteFutureProjects)
                                <form method="POST" action="{{ route('admin.upcoming.destroy', $item['id']) }}" data-confirm="{{ __('Remove :title?', ['title' => $item['title']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="{{ __('Remove :title', ['title' => $item['title']]) }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                            <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                    <div class="border border-border rounded-lg overflow-hidden flex flex-col sm:flex-row">
                        <div class="relative sm:w-48 shrink-0">
                            <x-nakheel.ph :seed="$item['image']" class="h-40 sm:h-full w-full" />
                            <span class="absolute top-2.5 left-2.5 bg-surface/95 text-ink text-[10px] font-semibold tracking-wider uppercase px-2 py-1 rounded">
                                {{ $item['badge'] }}
                            </span>
                        </div>
                        <div class="p-4 flex flex-col">
                            <h3 class="font-serif font-semibold text-ink mb-1.5">{{ $item['title'] }}</h3>
                            <p class="text-sm text-ink/55 leading-relaxed mb-3">{{ $item['description'] }}</p>
                            <p class="mt-auto flex items-center gap-1.5 text-xs font-medium text-gold">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" /></svg>
                                {{ $item['launch'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-start justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('PARTNER WITH US') }}</p>
                <h2 class="font-serif text-2xl font-semibold text-ink">{{ __('Why Invest With Nakheel') }}</h2>
            </div>
            @if ($canCreateWhyInvest)
                <a href="{{ route('admin.why-invest.create') }}" class="shrink-0 rounded-md bg-brand px-4 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                    + {{ __('ADD REASON') }}
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-16">
            @foreach ($reasons as $i => $reason)
                <div class="relative h-full">
                    @if ($canEditWhyInvest || $canDeleteWhyInvest)
                        <div class="absolute -top-2.5 -left-2.5 z-20 flex items-center gap-1.5">
                            @if ($canEditWhyInvest)
                                <a href="{{ route('admin.why-invest.edit', $reason['id']) }}" aria-label="{{ __('Edit :title', ['title' => $reason['title']]) }}"
                                   class="flex h-7 w-7 items-center justify-center rounded-full bg-gold text-ink ring-2 ring-cream shadow hover:bg-gold/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                        <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($canDeleteWhyInvest)
                                <form method="POST" action="{{ route('admin.why-invest.destroy', $reason['id']) }}" data-confirm="{{ __('Remove :title?', ['title' => $reason['title']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="{{ __('Remove :title', ['title' => $reason['title']]) }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-cream shadow hover:bg-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                            <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                <div class="h-full border border-border rounded-lg p-5 flex gap-4">
                    <x-nakheel.why-invest-icon :icon="$reason['icon'] ?? null" :legacy-image="$reason['image'] ?? null" :tint="$i % 2 === 0" />
                    <div>
                        <h3 class="font-semibold text-ink mb-1.5">{{ $reason['title'] }}</h3>
                        <p class="text-sm text-ink/55 leading-relaxed">{{ $reason['description'] }}</p>
                    </div>
                </div>
                </div>
            @endforeach
        </div>

        <div class="relative border border-border rounded-lg p-10 text-center">
            @if ($canEditContactInfo)
                <a href="{{ route('admin.page-content.edit', 'office_contact') }}" aria-label="{{ __('Edit contact details') }}"
                   class="absolute top-3 right-3 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-gold text-ink shadow hover:bg-gold/90">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
                    </svg>
                </a>
            @endif
            <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-3">{{ __('GET IN TOUCH') }}</p>
            <h2 class="font-serif text-2xl font-semibold text-ink mb-3">{{ __('Interested in Investing or Partnering With Us?') }}</h2>
            <p class="text-ink/60 max-w-xl mx-auto mb-6">
                {{ __('Contact our office to discuss available investment opportunities, upcoming projects, and potential business partnerships. Our investment team is available to arrange private briefings.') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 mb-6">
                <button type="button" data-dialog-open="office-contact-modal"
                        class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                    {{ __('CONTACT OUR OFFICE') }}
                </button>
                <a href="mailto:invest@nakheel-ly.com" class="rounded-md border border-ink px-5 py-2.5 text-xs font-semibold text-ink hover:bg-brand/5">
                    invest@nakheel-ly.com
                </a>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-ink/45">
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                    {{ __('Tower Jamahiriya, Tripoli') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-.464 1.467l-.746.606a11.03 11.03 0 0 0 5.91 5.91l.606-.746a1.5 1.5 0 0 1 1.467-.464l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-7.18 0-13-5.82-13-13v-1Z" clip-rule="evenodd" /></svg>
                    +218 21 444 5678
                </span>
            </div>
        </div>

        <x-nakheel.contact-modal id="office-contact-modal" :contact="$officeContactData" :label="__('CONTACT OUR OFFICE')" />

    </div>
@endsection
