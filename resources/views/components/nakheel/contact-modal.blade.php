@props(['id', 'contact', 'label' => null])

@php
    $rows = collect([
        ['label' => __('Phone'), 'value' => $contact['phone'] ?? null, 'bg' => 'bg-rose-50 text-rose-500', 'icon' => 'phone'],
        ['label' => __('WhatsApp'), 'value' => $contact['whatsapp'] ?? null, 'bg' => 'bg-emerald-50 text-emerald-500', 'icon' => 'chat'],
        ['label' => __('Telegram'), 'value' => $contact['telegram'] ?? null, 'bg' => 'bg-sky-50 text-sky-500', 'icon' => 'telegram'],
        ['label' => __('Email'), 'value' => $contact['email'] ?? null, 'bg' => 'bg-violet-50 text-violet-500', 'icon' => 'mail'],
        ['label' => __('Office'), 'value' => $contact['office'] ?? null, 'bg' => 'bg-sky-50 text-sky-500', 'icon' => 'building'],
        ['label' => __('Address'), 'value' => $contact['address'] ?? null, 'bg' => 'bg-rose-50 text-rose-500', 'icon' => 'pin'],
        ['label' => __('Hours'), 'value' => $contact['hours'] ?? null, 'bg' => 'bg-amber-50 text-amber-500', 'icon' => 'clock'],
    ])->filter(fn ($row) => filled($row['value']));

    $telHref = fn ($v) => 'tel:' . preg_replace('/[^\d+]/', '', (string) $v);
    $waHref = fn ($v) => 'https://wa.me/' . preg_replace('/[^\d]/', '', (string) $v);
@endphp

<dialog id="{{ $id }}" data-dialog-backdrop-close
        class="m-auto p-0 border-0 rounded-lg bg-surface max-w-sm w-[90vw] max-h-[88vh] overflow-y-auto backdrop:bg-brand/70">
    <div class="relative p-6">
        <button type="button" data-dialog-close aria-label="{{ __('Close') }}"
                class="absolute top-5 right-5 flex h-7 w-7 items-center justify-center rounded-full text-ink/40 hover:bg-brand/5 hover:text-ink">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
        </button>

        <p class="text-[10px] font-semibold tracking-[0.15em] text-gold mb-2">{{ $label ?? __('PROJECT CONTACT') }}</p>
        <h3 class="font-serif text-xl font-semibold text-ink pr-8">{{ $contact['name'] ?? 'Nakheel Properties' }}</h3>
        @if (filled($contact['role'] ?? null))
            <p class="text-sm text-ink/50 mt-0.5">{{ $contact['role'] }}</p>
        @endif

        <div class="mt-5 border-t border-border pt-5 space-y-4">
            @foreach ($rows as $row)
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $row['bg'] }}">
                        @switch($row['icon'])
                            @case('phone')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-.826 1.68l-.94.47a10.523 10.523 0 0 0 4.918 4.918l.47-.94a1.5 1.5 0 0 1 1.68-.826l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd" /></svg>
                                @break
                            @case('chat')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 2c-4.42 0-8 3.134-8 7 0 1.87.845 3.567 2.235 4.83.11.1.176.24.176.39v2.03a.75.75 0 0 0 1.06.68l2.42-1.108A9.4 9.4 0 0 0 10 16c4.42 0 8-3.134 8-7s-3.58-7-8-7Z" clip-rule="evenodd" /></svg>
                                @break
                            @case('mail')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M3 4a2 2 0 0 0-2 2v.01L10 12l9-5.99V6a2 2 0 0 0-2-2H3Z" /><path d="M18 8.24 10.53 13a1 1 0 0 1-1.06 0L2 8.24V14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.24Z" /></svg>
                                @break
                            @case('building')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75v-2.5a1.5 1.5 0 0 0-3 0v2.5a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1 0-1.5H4Zm2.75-9.5a.75.75 0 0 0 0 1.5h.5a.75.75 0 0 0 0-1.5h-.5Zm3.5.75a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1-.75-.75Zm-3.5 3.25a.75.75 0 0 0 0 1.5h.5a.75.75 0 0 0 0-1.5h-.5Zm3.5.75a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                                @break
                            @case('pin')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                                @break
                            @case('clock')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .284.16.544.415.67l3.5 1.75a.75.75 0 0 0 .67-1.342L10.75 9.535V5Z" clip-rule="evenodd" /></svg>
                                @break
                            @case('telegram')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M18.384 2.11a1.03 1.03 0 0 0-1.107-.135L1.86 8.61a.99.99 0 0 0 .062 1.834l3.842 1.377 1.516 4.87a.99.99 0 0 0 1.66.38l2.15-2.163 3.734 2.75a.99.99 0 0 0 1.558-.573l2.98-13.9a1.02 1.02 0 0 0-.978-1.075Zm-3.31 3.4-6.36 5.77-.32 3.27-1.24-3.98 7.92-5.06Z" /></svg>
                                @break
                        @endswitch
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold tracking-wider text-ink/40 uppercase">{{ $row['label'] }}</p>
                        <p class="text-sm text-ink truncate">{{ $row['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 space-y-2.5">
            @if (filled($contact['phone'] ?? null))
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ $telHref($contact['phone']) }}"
                       class="flex items-center justify-center gap-1.5 rounded-md border border-ink px-3 py-2.5 text-xs font-semibold text-ink hover:bg-brand/5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-.826 1.68l-.94.47a10.523 10.523 0 0 0 4.918 4.918l.47-.94a1.5 1.5 0 0 1 1.68-.826l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd" /></svg>
                        {{ __('Call') }}
                    </a>
                    <a href="{{ $waHref($contact['whatsapp'] ?? $contact['phone']) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-1.5 rounded-md bg-brand px-3 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M10 2c-4.42 0-8 3.134-8 7 0 1.87.845 3.567 2.235 4.83.11.1.176.24.176.39v2.03a.75.75 0 0 0 1.06.68l2.42-1.108A9.4 9.4 0 0 0 10 16c4.42 0 8-3.134 8-7s-3.58-7-8-7Z" clip-rule="evenodd" /></svg>
                        WhatsApp
                    </a>
                </div>
            @endif

            @if (filled($contact['telegram'] ?? null))
                <a href="https://t.me/{{ ltrim($contact['telegram'], '@') }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-1.5 rounded-md border border-border px-3 py-2.5 text-xs font-semibold text-ink hover:bg-brand/5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M18.384 2.11a1.03 1.03 0 0 0-1.107-.135L1.86 8.61a.99.99 0 0 0 .062 1.834l3.842 1.377 1.516 4.87a.99.99 0 0 0 1.66.38l2.15-2.163 3.734 2.75a.99.99 0 0 0 1.558-.573l2.98-13.9a1.02 1.02 0 0 0-.978-1.075Zm-3.31 3.4-6.36 5.77-.32 3.27-1.24-3.98 7.92-5.06Z" /></svg>
                    {{ __('Message on Telegram') }}
                </a>
            @endif

            @if (filled($contact['email'] ?? null))
                <a href="mailto:{{ $contact['email'] }}"
                   class="flex items-center justify-center gap-1.5 rounded-md border border-border px-3 py-2.5 text-xs font-semibold text-ink hover:bg-brand/5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M3 4a2 2 0 0 0-2 2v.01L10 12l9-5.99V6a2 2 0 0 0-2-2H3Z" /><path d="M18 8.24 10.53 13a1 1 0 0 1-1.06 0L2 8.24V14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.24Z" /></svg>
                    {{ $contact['email'] }}
                </a>
            @endif

            @if (filled($contact['mapsUrl'] ?? null))
                <a href="{{ $contact['mapsUrl'] }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-1.5 rounded-md border border-border px-3 py-2.5 text-xs font-semibold text-gold hover:bg-gold/5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path d="M10 2a6.5 6.5 0 0 0-6.5 6.5c0 4.076 4.978 9.339 6.052 10.408a.634.634 0 0 0 .896 0C11.522 17.839 16.5 12.576 16.5 8.5A6.5 6.5 0 0 0 10 2Zm0 9a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" /></svg>
                    {{ __('View on Google Maps') }}
                </a>
            @endif
        </div>
    </div>
</dialog>
