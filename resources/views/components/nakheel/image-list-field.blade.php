{{--
    Multi-image upload field (project hero/environment galleries, property
    photos, floor plans).

    Two modes:
    - AJAX mode (pass :resource and :id for an EXISTING record): each newly
      picked file uploads and appears immediately; each "×" removes just
      that one image immediately. No full-form save required.
    - Legacy mode (no :resource/:id — "create" forms, no record yet):
      existing images show as "keep" checkboxes and new files queue up,
      both resolved on submit via HandlesImageUploads::resolveImageList().
      The parent <form> must have enctype="multipart/form-data".
--}}
@props(['name', 'label', 'hint' => null, 'current' => [], 'resource' => null, 'id' => null])

@php
    $images = collect($current)->filter()->values();
    $ajax = filled($resource) && filled($id);
@endphp

@if ($ajax)
    <div data-image-list-field
         data-endpoint="{{ route('admin.images.store', [$resource, $id]) }}"
         data-delete-endpoint="{{ route('admin.images.destroy', [$resource, $id]) }}"
         data-field="{{ $name }}">
        <div data-image-list-items class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-2">
            @foreach ($images as $path)
                <div class="relative" data-image-list-item data-path="{{ $path }}">
                    <x-nakheel.ph :seed="$path" class="aspect-square rounded-md w-full" />
                    <button type="button" data-image-list-remove aria-label="{{ __('Remove') }}"
                            class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs shadow hover:bg-red-600">&times;</button>
                </div>
            @endforeach
        </div>

        <p class="text-center text-xs font-medium text-ink/60 mb-3">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </p>

        <input type="file" accept="image/*" multiple data-image-list-pick
               class="block w-full text-sm text-ink/70 file:me-3 file:rounded-md file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand/90">
    </div>
@else
    <div>
        <input type="hidden" name="{{ $name }}_keep_present" value="1">

        @if ($images->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                @foreach ($images as $path)
                    <label class="relative block cursor-pointer">
                        <x-nakheel.ph :seed="$path" class="aspect-square rounded-md w-full" />
                        <input type="checkbox" name="{{ $name }}_keep[]" value="{{ $path }}" checked
                               class="absolute top-1.5 left-1.5 h-4 w-4 rounded border-white text-emerald-500 focus:ring-0 focus:ring-offset-0">
                    </label>
                @endforeach
            </div>
            <p class="text-[11px] text-ink/40 mb-2">{{ __('Uncheck an image to remove it when you save.') }}</p>
        @endif

        <div data-legacy-new-previews class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-2 empty:hidden"></div>

        <p class="text-center text-xs font-medium text-ink/60 mb-3">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </p>

        <input type="file" name="{{ $name }}[]" accept="image/*" multiple data-legacy-image-list-pick
               class="block w-full text-sm text-ink/70 file:me-3 file:rounded-md file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand/90">
    </div>
@endif
