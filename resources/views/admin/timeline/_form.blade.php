{{--
    Shared form for admin/timeline/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $entry (array|null).
--}}
@php
    $entry ??= [];
    $old = fn (string $key, $default = '') => old($key, $entry[$key] ?? $default);
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
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Year') }}</label>
                <input type="text" name="year" value="{{ $old('year') }}" required
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Status') }}" :hint="__('(e.g. Completed)')" name-en="status" name-ar="status_ar"
                    :value-en="$entry['status'] ?? 'Completed'" :value-ar="$entry['status_ar'] ?? null" :required="true" />
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Status color') }}</label>
                <select name="color" id="timeline-color-select" required class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    @foreach (\App\Http\Controllers\AdminTimelineEntryController::COLORS as $colorKey)
                        <option value="{{ $colorKey }}" {{ $old('color', $entry['color'] ?? 'emerald') === $colorKey ? 'selected' : '' }}>
                            {{ __(ucfirst($colorKey)) }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[10px] text-ink/40">{{ __('Preview:') }}</span>
                    @foreach (\App\Http\Controllers\AdminTimelineEntryController::COLORS as $colorKey)
                        <span data-color-preview="{{ $colorKey }}" class="{{ $old('color', $entry['color'] ?? 'emerald') === $colorKey ? '' : 'hidden' }}">
                            <x-nakheel.status-pill :label="$old('status', $entry['status'] ?? 'Completed')" :color="$colorKey" />
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Title') }}" name-en="title" name-ar="title_ar"
                    :value-en="$entry['title'] ?? null" :value-ar="$entry['title_ar'] ?? null" :required="true" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Meta') }}" :hint="__('(e.g. Tripoli, Libya · Residential Buildings)')" name-en="meta" name-ar="meta_ar"
                    :value-en="$entry['meta'] ?? null" :value-ar="$entry['meta_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Description') }}" type="textarea" name-en="description" name-ar="description_ar"
                    :value-en="$entry['description'] ?? null" :value-ar="$entry['description_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.image-field name="image" :label="__('Photo')" :current="$entry['image'] ?? null" resource="timeline" :id="$entry['id'] ?? null" />
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
            {{ __('SAVE TIMELINE ENTRY') }}
        </button>
        <a href="{{ route('about') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>

<script nonce="{{ Vite::cspNonce() }}">
    document.getElementById('timeline-color-select').addEventListener('change', (event) => {
        document.querySelectorAll('[data-color-preview]').forEach((preview) => {
            preview.classList.toggle('hidden', preview.dataset.colorPreview !== event.target.value);
        });
    });
</script>
