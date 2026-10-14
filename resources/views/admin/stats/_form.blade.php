{{--
    Shared form for admin/stats/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $stat (array|null).
--}}
@php
    $stat ??= [];
    $old = fn (string $key, $default = '') => old($key, $stat[$key] ?? $default);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Value') }}" :hint="__('(e.g. 12+)')" name-en="value" name-ar="value_ar"
                    :value-en="$stat['value'] ?? null" :value-ar="$stat['value_ar'] ?? null" :required="true" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Label') }}" :hint="__('(e.g. Years of Experience)')" name-en="label" name-ar="label_ar"
                    :value-en="$stat['label'] ?? null" :value-ar="$stat['label_ar'] ?? null" :required="true" />
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Sort order') }} <span class="text-ink/30">{{ __('(lower shows first)') }}</span></label>
                <input type="number" min="0" name="sort_order" value="{{ $old('sort_order', 0) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            {{ __('SAVE STAT') }}
        </button>
        <a href="{{ route('about') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
