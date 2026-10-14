{{--
    Shared form for admin/upcoming/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $item (array|null).
--}}
@php
    $item ??= [];
    $old = fn (string $key, $default = '') => old($key, $item[$key] ?? $default);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8" enctype="multipart/form-data">
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
                <x-nakheel.bilingual-field label="{{ __('Badge') }}" :hint="__('(e.g. Pre-Development, Planned)')" name-en="badge" name-ar="badge_ar"
                    :value-en="$item['badge'] ?? null" :value-ar="$item['badge_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Launch') }}" :hint="__('(e.g. Expected launch: 2026)')" name-en="launch" name-ar="launch_ar"
                    :value-en="$item['launch'] ?? null" :value-ar="$item['launch_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Title') }}" name-en="title" name-ar="title_ar"
                    :value-en="$item['title'] ?? null" :value-ar="$item['title_ar'] ?? null" :required="true" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Description') }}" type="textarea" name-en="description" name-ar="description_ar"
                    :value-en="$item['description'] ?? null" :value-ar="$item['description_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.image-field name="image" :label="__('Photo')" :current="$item['image'] ?? null" resource="upcoming" :id="$item['id'] ?? null" />
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
            {{ __('SAVE UPCOMING PROJECT') }}
        </button>
        <a href="{{ route('future-projects') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
