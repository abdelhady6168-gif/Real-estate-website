{{--
    Single-image upload field for admin forms.

    Two modes:
    - AJAX mode (pass :resource and :id for an EXISTING record): picking a
      file uploads and previews it immediately; "Remove" deletes it
      immediately. No full-form save required, other fields untouched.
    - Legacy mode (no :resource/:id — used on "create" forms, where the
      record doesn't exist yet to attach an upload to): falls back to a
      plain file input + "remove" checkbox resolved on form submit via
      HandlesImageUploads::resolveImagePath(). The parent <form> must have
      enctype="multipart/form-data" for this mode.
--}}
@props(['name', 'label', 'hint' => null, 'current' => null, 'resource' => null, 'id' => null])

@php
    $ajax = filled($resource) && filled($id);
@endphp

@if ($ajax)
    <div data-image-field
         data-endpoint="{{ route('admin.images.store', [$resource, $id]) }}"
         data-delete-endpoint="{{ route('admin.images.destroy', [$resource, $id]) }}"
         data-field="{{ $name }}">
        <label class="block text-xs font-medium text-ink/60 mb-1.5">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </label>
        <div class="flex items-center gap-4">
            <div data-image-preview class="h-28 w-28 rounded-md shrink-0 overflow-hidden">
                <x-nakheel.ph :seed="$current ?: 'nakheel'" class="h-full w-full" />
            </div>
            <template data-image-placeholder><x-nakheel.ph seed="nakheel" class="h-full w-full" /></template>
            <div class="flex-1 space-y-1.5">
                <input type="file" accept="image/*" data-image-pick
                       class="block w-full text-sm text-ink/70 file:me-3 file:rounded-md file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand/90">
                <p data-image-status class="text-xs text-ink/40 min-h-[1em]"></p>
                <button type="button" data-image-remove data-path="{{ $current }}"
                        class="text-xs text-red-600 hover:underline {{ filled($current) ? '' : 'hidden' }}">
                    {{ __('Remove current image') }}
                </button>
            </div>
        </div>
    </div>
@else
    <div>
        <label class="block text-xs font-medium text-ink/60 mb-1.5">
            {{ $label }}
            @if ($hint)
                <span class="text-ink/30">{{ $hint }}</span>
            @endif
        </label>
        <div class="flex items-center gap-4">
            <div data-legacy-image-preview class="h-28 w-28 rounded-md shrink-0 overflow-hidden">
                <x-nakheel.ph :seed="$current ?: 'nakheel'" class="h-full w-full" />
            </div>
            <div class="flex-1 space-y-1.5">
                <input type="file" name="{{ $name }}" accept="image/*" data-legacy-image-pick
                       class="block w-full text-sm text-ink/70 file:me-3 file:rounded-md file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand/90">
                @if (filled($current))
                    <label class="flex items-center gap-1.5 text-xs text-ink/50">
                        <input type="checkbox" name="{{ $name }}_remove" value="1" class="rounded border-border text-red-500 focus:ring-0 focus:ring-offset-0">
                        {{ __('Remove current image') }}
                    </label>
                @endif
            </div>
        </div>
    </div>
@endif
