{{--
    Shared form for admin/why-invest/create.blade.php and edit.blade.php.
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
        <div class="grid grid-cols-1 gap-5">
            <div>
                <x-nakheel.bilingual-field label="{{ __('Title') }}" name-en="title" name-ar="title_ar"
                    :value-en="$item['title'] ?? null" :value-ar="$item['title_ar'] ?? null" :required="true" />
            </div>

            <div>
                <x-nakheel.bilingual-field label="{{ __('Description') }}" type="textarea" name-en="description" name-ar="description_ar"
                    :value-en="$item['description'] ?? null" :value-ar="$item['description_ar'] ?? null" />
            </div>

            <div data-icon-search-form>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Icon') }} <span class="text-ink/30">{{ __('(the checkmark icon is used when none is chosen)') }}</span></label>
                <input type="text" data-icon-search placeholder="{{ __('Search icons…') }}"
                       class="mb-2 w-full max-w-xs rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                <div class="grid grid-cols-5 sm:grid-cols-10 gap-2" data-icon-grid>
                    @foreach (\App\Support\WhyInvestIcons::available() as $key => $definition)
                        <label data-icon-option data-icon-name="{{ strtolower(__($definition['label'])) }}"
                               class="relative flex h-12 w-12 cursor-pointer items-center justify-center rounded-md border border-border text-ink/60 has-[:checked]:border-gold has-[:checked]:bg-gold/10 has-[:checked]:text-gold" title="{{ __($definition['label']) }}">
                            <input type="radio" name="icon" value="{{ $key }}" class="sr-only" {{ old('icon', $item['icon'] ?? null) === $key ? 'checked' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                {!! implode('', $definition['paths']) !!}
                            </svg>
                        </label>
                    @endforeach
                </div>
                <p data-icon-no-results class="hidden mt-2 text-sm text-ink/40">{{ __('No icons match your search.') }}</p>
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
            {{ __('SAVE REASON') }}
        </button>
        <a href="{{ route('future-projects') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
