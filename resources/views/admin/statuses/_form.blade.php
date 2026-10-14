{{--
    Shared form for admin/statuses/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $status (array|null).
--}}
@php
    $status ??= [];
    $old = fn (string $key, $default = '') => old($key, $status[$key] ?? $default);
    $selectedColor = $old('color', 'emerald');
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
                <x-nakheel.bilingual-field label="{{ __('Name') }}" :hint="__('(e.g. Purchasable — used internally, so English is required)')" name-en="name" name-ar="name_ar"
                    :value-en="$status['name'] ?? null" :value-ar="$status['name_ar'] ?? null" :required-en="true" />
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Color') }}</label>
                <select name="color" required class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    @foreach (\App\Services\NakheelPropertyStatusService::COLORS as $key => $class)
                        <option value="{{ $key }}" {{ $selectedColor === $key ? 'selected' : '' }}>{{ __(ucfirst($key)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Sort order') }} <span class="text-ink/30">{{ __('(lower shows first)') }}</span></label>
                <input type="number" min="0" name="sort_order" value="{{ $old('sort_order', 0) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm text-ink/70">
                    <input type="checkbox" name="is_sold" value="1" {{ old('is_sold', $status['is_sold'] ?? false) ? 'checked' : '' }}
                           class="rounded border-border text-gold focus:ring-0 focus:ring-offset-0">
                    {{ __('Treat this status as "Sold"') }}
                </label>
                <p class="text-[11px] text-ink/40 mt-1">{{ __('Controls the global Sold Properties Visibility setting on the Properties page — properties with this status are hidden from normal users when it is off.') }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            {{ __('SAVE STATUS') }}
        </button>
        <a href="{{ route('admin.statuses.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
