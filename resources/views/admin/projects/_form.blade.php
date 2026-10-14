{{--
    Shared form for admin/projects/create.blade.php and edit.blade.php.
    Expects: $action (form action URL), $method ('POST' or 'PUT'), $project (array|null).
--}}
@php
    $project ??= [];
    $old = fn (string $key, $default = '') => old($key, $project[$key] ?? $default);
    $heroImageCurrent = collect(explode(',', $project['hero_images'] ?? ''))->map(fn ($v) => trim($v))->filter()->first();
    $environmentImagesCurrent = collect(explode(',', $project['environment_images'] ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
        {{-- Version of the project this form was opened with — see GuardsConcurrentEdits. --}}
        <input type="hidden" name="edit_version" value="{{ old('edit_version', $editVersion ?? '') }}">
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
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('BASICS') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Name') }}" name-en="name" name-ar="name_ar"
                    :value-en="$project['name'] ?? null" :value-ar="$project['name_ar'] ?? null" :required="true" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Slug') }} <span class="text-ink/30">{{ __('(used in the URL, e.g. lotus-compound)') }}</span></label>
                <input type="text" name="slug" value="{{ $old('slug') }}" required pattern="[a-z0-9\-]+"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Type') }}" :hint="__('(e.g. Residential Compound)')" name-en="type" name-ar="type_ar"
                    :value-en="$project['type'] ?? null" :value-ar="$project['type_ar'] ?? null" :required="true" />
            </div>
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Location') }}" name-en="location" name-ar="location_ar"
                    :value-en="$project['location'] ?? null" :value-ar="$project['location_ar'] ?? null" :required="true" />
            </div>
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Tagline') }}" name-en="tagline" name-ar="tagline_ar"
                    :value-en="$project['tagline'] ?? null" :value-ar="$project['tagline_ar'] ?? null" />
            </div>
        </div>
        <div class="mt-5">
            <x-nakheel.bilingual-field label="{{ __('Description') }}" type="textarea" :rows="4" name-en="description" name-ar="description_ar"
                :value-en="$project['description'] ?? null" :value-ar="$project['description_ar'] ?? null" />
        </div>
        <div class="mt-5">
            <x-nakheel.image-field name="hero_images" :label="__('Hero Image')"
                :hint="__('(the single banner image shown at the top of the project page — for best quality, upload at least 1600×440px)')"
                :current="$heroImageCurrent" resource="projects" :id="$project['id'] ?? null" />
        </div>
    </div>

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('DETAILS') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <x-nakheel.bilingual-field label="{{ __('Developer') }}" name-en="developer" name-ar="developer_ar"
                    :value-en="$project['developer'] ?? 'Nakheel Properties'" :value-ar="$project['developer_ar'] ?? null" />
            </div>
            <div>
                <x-nakheel.bilingual-field label="{{ __('Status') }}" name-en="status" name-ar="status_ar"
                    :value-en="$project['status'] ?? 'Active'" :value-ar="$project['status_ar'] ?? null" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Completion year') }}</label>
                <input type="text" name="completion_year" value="{{ $old('completion_year') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
        </div>
    </div>

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('STATS — shown as the 4 boxes on the project page') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Total units') }}</label>
                <input type="number" min="0" name="total_units" value="{{ $old('total_units', 0) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Available units') }}</label>
                <input type="number" min="0" name="available_units" value="{{ $old('available_units', 0) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <x-nakheel.bilingual-field label="{{ __('Development area') }}" :hint="__('(e.g. 24 hectares)')" name-en="development_area" name-ar="development_area_ar"
                    :value-en="$project['development_area'] ?? null" :value-ar="$project['development_area_ar'] ?? null" />
            </div>
            <div>
                <x-nakheel.bilingual-field label="{{ __('Property types') }}" :hint="__('(e.g. Villas, Apartments)')" name-en="property_types" name-ar="property_types_ar"
                    :value-en="$project['property_types'] ?? null" :value-ar="$project['property_types_ar'] ?? null" />
            </div>
        </div>
    </div>

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('ENVIRONMENT') }}</p>
        <x-nakheel.image-list-field name="environment_images" :label="__('Environment images')"
            :hint="__('(3 recommended — for best quality, upload at least 1600×450px)')"
            :current="$environmentImagesCurrent" resource="projects" :id="$project['id'] ?? null" />
    </div>

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('PROJECT HIGHLIGHTS') }}</p>

        <div data-repeater="fields" data-name="highlights">
            <div data-repeater-rows class="space-y-3">
                @foreach ($highlights ?? [] as $highlight)
                    <div class="flex items-start gap-3 border border-border rounded-md p-3" data-repeater-row>
                        <input type="hidden" name="highlights[{{ $highlight['id'] }}][id]" value="{{ $highlight['id'] }}">
                        <div class="flex-1 space-y-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" name="highlights[{{ $highlight['id'] }}][label]" value="{{ $highlight['label'] }}" placeholder="{{ __('Label (English)') }}"
                                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                                <input type="text" name="highlights[{{ $highlight['id'] }}][label_ar]" dir="rtl" value="{{ $highlight['label_ar'] ?? '' }}" placeholder="{{ __('Label (Arabic)') }}"
                                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                            </div>
                            <x-nakheel.image-field name="image" :label="__('Image (optional)')" :current="$highlight['image']"
                                resource="highlights" :id="$highlight['id']" />
                        </div>
                        <button type="button" data-repeater-remove aria-label="{{ __('Remove') }}"
                                class="shrink-0 flex h-8 w-8 items-center justify-center rounded-md text-red-500 hover:bg-red-50">&times;</button>
                    </div>
                @endforeach
            </div>

            <template data-repeater-template>
                <div class="flex items-start gap-3 border border-border rounded-md p-3" data-repeater-row>
                    <div class="flex-1 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="highlights[__KEY__][label]" placeholder="{{ __('Label (English)') }}"
                                   class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                            <input type="text" name="highlights[__KEY__][label_ar]" dir="rtl" placeholder="{{ __('Label (Arabic)') }}"
                                   class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Image (optional)') }}</label>
                            <input type="file" name="highlights[__KEY__][image]" accept="image/*"
                                   class="block w-full text-sm text-ink/70 file:me-3 file:rounded-md file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand/90">
                            <p class="text-[11px] text-ink/40 mt-1">{{ __('This image uploads when you save the project below.') }}</p>
                        </div>
                    </div>
                    <button type="button" data-repeater-remove aria-label="{{ __('Remove') }}"
                            class="shrink-0 flex h-8 w-8 items-center justify-center rounded-md text-red-500 hover:bg-red-50">&times;</button>
                </div>
            </template>

            <button type="button" data-repeater-add
                    class="mt-3 rounded-md border border-dashed border-border px-3 py-1.5 text-xs font-semibold text-ink/60 hover:text-ink hover:border-ink">
                + {{ __('Add Highlight') }}
            </button>
        </div>
    </div>

    <div class="border border-border rounded-lg bg-surface p-6">
        <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('PROJECT MANAGER & CONTACT — shown in the "Contact Owner" pop-up') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Name') }}</label>
                <input type="text" name="manager_name" value="{{ $old('manager_name') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <x-nakheel.bilingual-field label="{{ __('Role') }}" name-en="manager_role" name-ar="manager_role_ar"
                    :value-en="$project['manager_role'] ?? null" :value-ar="$project['manager_role_ar'] ?? null" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Phone') }}</label>
                <input type="text" name="manager_phone" value="{{ $old('manager_phone') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('WhatsApp') }}</label>
                <input type="text" name="manager_whatsapp" value="{{ $old('manager_whatsapp') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Email') }}</label>
                <input type="email" name="manager_email" value="{{ $old('manager_email') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>
            <div>
                <x-nakheel.bilingual-field label="{{ __('Office name') }}" name-en="office_name" name-ar="office_name_ar"
                    :value-en="$project['office_name'] ?? null" :value-ar="$project['office_name_ar'] ?? null" />
            </div>
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Office address') }}" name-en="office_address" name-ar="office_address_ar"
                    :value-en="$project['office_address'] ?? null" :value-ar="$project['office_address_ar'] ?? null" />
            </div>
            <div class="sm:col-span-2">
                <x-nakheel.office-hours-fields
                    :from-day="$project['office_hours_from_day'] ?? null" :to-day="$project['office_hours_to_day'] ?? null"
                    :from-time="$project['office_hours_from_time'] ?? null" :to-time="$project['office_hours_to_time'] ?? null" />
            </div>
        </div>

        <div class="mt-5 border-t border-border pt-5">
            <p class="text-[10px] font-semibold tracking-wider text-ink/40 mb-4">{{ __('GOOGLE MAPS — shown as "View on Google Maps" in About This Project') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <x-nakheel.bilingual-field label="{{ __('Location Name') }}" :hint="__('(the name shown to visitors, e.g. Lotus Compound)')"
                        name-en="maps_location_name" name-ar="maps_location_name_ar"
                        :value-en="$project['maps_location_name'] ?? null" :value-ar="$project['maps_location_name_ar'] ?? null" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Google Maps URL') }} <span class="text-ink/30">{{ __('(the link opened when a visitor clicks "View on Google Maps")') }}</span></label>
                    <input type="text" name="maps_url" value="{{ $old('maps_url') }}" placeholder="https://maps.google.com/..."
                           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            {{ __('SAVE PROJECT') }}
        </button>
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
</form>
