{{--
    Shared form for admin/categories/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $category (array|null).
--}}
@php
    $category ??= [];
    $old = fn (string $key, $default = '') => old($key, $category[$key] ?? $default);
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
        <div class="grid grid-cols-1 gap-5">
            <div>
                <x-nakheel.bilingual-field label="{{ __('Title') }}" name-en="title" name-ar="title_ar"
                    :value-en="$category['title'] ?? null" :value-ar="$category['title_ar'] ?? null" :required="true" />
            </div>

            <div>
                <x-nakheel.bilingual-field label="{{ __('Description') }}" type="textarea" name-en="description" name-ar="description_ar"
                    :value-en="$category['description'] ?? null" :value-ar="$category['description_ar'] ?? null" />
            </div>

            <div>
                <x-nakheel.image-field name="image" :label="__('Photo')" :current="$category['image'] ?? null" resource="categories" :id="$category['id'] ?? null" />
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
            {{ __('SAVE CATEGORY') }}
        </button>
        <a href="{{ route('about') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
