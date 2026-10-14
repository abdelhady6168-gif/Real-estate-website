@extends('layouts.admin')

@section('title', 'Edit ' . $content['heading'])

@php
    $backRoute = $content['key'] === 'office_contact' ? 'future-projects' : 'about';
@endphp

@section('content')
    <p class="text-xs font-semibold tracking-[0.15em] text-gold mb-2">{{ __('ADMIN') }}</p>
    <h1 class="font-serif text-3xl font-semibold text-ink mb-8">{{ __('Edit :heading', ['heading' => $content['heading']]) }}</h1>

    <form method="POST" action="{{ route('admin.page-content.update', $content['key']) }}" class="space-y-8" enctype="multipart/form-data">
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
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <x-nakheel.bilingual-field label="{{ __('Heading') }}" name-en="heading" name-ar="heading_ar"
                        :value-en="$content['heading']" :value-ar="$content['heading_ar'] ?? null" :required="true" />
                </div>

                <div>
                    <x-nakheel.bilingual-field label="{{ __('Body') }}" :hint="__('(leave a blank line between paragraphs)')" type="textarea" :rows="8"
                        name-en="body" name-ar="body_ar" :value-en="$content['body']" :value-ar="$content['body_ar'] ?? null" />
                </div>

                <div>
                    <x-nakheel.image-field name="image" :label="__('Photo')" :current="$content['image'] ?? null" resource="page-content" :id="$content['key']" />
                </div>
            </div>
        </div>

        @if ($content['key'] === 'office_contact')
            <div class="border border-border rounded-lg bg-surface p-6">
                <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('CONTACT DETAILS — shown in the "Contact Our Office" pop-up') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $content['phone'] ?? '') }}"
                               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('WhatsApp') }}</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $content['whatsapp'] ?? '') }}"
                               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Telegram') }}</label>
                        <input type="text" name="telegram" value="{{ old('telegram', $content['telegram'] ?? '') }}"
                               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $content['email'] ?? '') }}"
                               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <x-nakheel.bilingual-field label="{{ __('Office address') }}" name-en="office_address" name-ar="office_address_ar"
                            :value-en="$content['office_address'] ?? null" :value-ar="$content['office_address_ar'] ?? null" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-nakheel.office-hours-fields
                            :from-day="$content['office_hours_from_day'] ?? null" :to-day="$content['office_hours_to_day'] ?? null"
                            :from-time="$content['office_hours_from_time'] ?? null" :to-time="$content['office_hours_to_time'] ?? null" />
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                {{ __('SAVE CONTENT') }}
            </button>
            <a href="{{ route($backRoute) }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
