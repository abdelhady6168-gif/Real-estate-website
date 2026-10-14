{{--
    Shared form for admin/properties/create.blade.php and edit.blade.php.
    Expects: $action, $method ('POST'|'PUT'), $property (array|null), $projectOptions ([id => name]).
--}}
@php
    $property ??= [];
    $old = fn (string $key, $default = '') => old($key, $property[$key] ?? $default);
    $selectedProjectId = $old('nakheel_project_id');
    $selectedStatus = $old('status');
    $specsItems = old('specs') ?? (json_decode($property['specs'] ?? '', true) ?: []);
    $featuresItems = old('features') !== null
        ? (json_decode(old('features'), true) ?: [])
        : (json_decode($property['features'] ?? '', true) ?: []);
    $imagesItems = collect(explode(',', $property['images'] ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all();
    $floorPlansItems = collect(explode(',', $property['floor_plans'] ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all();
    // Whether the Availability section (below, outside this <form> — see the
    // note near it) renders at all: an existing Rentable/Booked property,
    // AND this admin was granted at least one action on the separate
    // "Manage Property Availability" permission (independent of their
    // "properties" permission — see AdminPermissions::sections()). Without
    // it the whole management block (view, add, edit, delete) is hidden;
    // the 3 AJAX routes it calls are gated the same way server-side, so
    // hiding it here is a UX nicety, not the actual enforcement.
    // When it does render, "SAVE PROPERTY" moves to sit underneath it
    // instead of its usual spot, per request — still submits this same
    // form via the form="property-form" attribute, since a button can't
    // sit inside two forms at once and the availability section has its own.
    $canManageAvailability = (bool) auth()->user()?->hasSectionAccess('property_availability');
    $showsAvailability = $canManageAvailability
        && ($property['id'] ?? null)
        && in_array($selectedStatus ?: ($property['status'] ?? null), ['Rentable', 'Booked'], true);
@endphp

<form id="property-form" method="POST" action="{{ $action }}" class="space-y-8" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
        {{-- Version of the property this form was opened with — see GuardsConcurrentEdits. --}}
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Title') }}" name-en="title" name-ar="title_ar"
                    :value-en="$property['title'] ?? null" :value-ar="$property['title_ar'] ?? null" :required="true" />
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Project') }}</label>
                <select name="nakheel_project_id" class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    <option value="">&mdash; {{ __('Unassigned') }} &mdash;</option>
                    @foreach ($projectOptions as $id => $name)
                        <option value="{{ $id }}" {{ (string) $selectedProjectId === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Type') }}" :hint="__('(e.g. Villa, Apartment, Summer House)')" name-en="type" name-ar="type_ar"
                    :value-en="$property['type'] ?? null" :value-ar="$property['type_ar'] ?? null" :required="true" />
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Status') }}</label>
                <select name="status" required class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
                    {{-- The property's current status may no longer exist (its status
                         was deleted from Property Statuses elsewhere) — without this,
                         nothing below would match and the browser would just default
                         to the first real option, silently reassigning the property
                         to it the moment this form is saved. Keeping its real value
                         selected here means saving without touching this field is
                         correctly rejected instead (status must be one of the current
                         options), so nothing changes until an admin deliberately
                         picks a real one. --}}
                    @if ($selectedStatus && !in_array($selectedStatus, $statusOptions, true))
                        <option value="{{ $selectedStatus }}" selected>{{ __('Unassigned') }}</option>
                    @endif
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" {{ $selectedStatus === $status ? 'selected' : '' }}>{{ $statusLabels[$status] ?? $status }}</option>
                    @endforeach
                </select>
                @if ($selectedStatus && !in_array($selectedStatus, $statusOptions, true))
                    <p class="text-xs text-amber-600 mt-1">{{ __('This property\'s status (":name") was deleted from Property Statuses — choose a new one.', ['name' => $selectedStatus]) }}</p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Price (L.D.)') }}</label>
                <input type="number" min="0" name="price" value="{{ $old('price', 0) }}" required
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:border-gold focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Location') }}" name-en="location" name-ar="location_ar"
                    :value-en="$property['location'] ?? null" :value-ar="$property['location_ar'] ?? null" :required="true" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.repeater name="specs" mode="fields" :label="__('Specifications')" :hint="__('(values are numbers; add a unit to the name for decimals, e.g. Area (m²))')" :items="$specsItems" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.image-list-field name="images" :label="__('Listing Images')" :current="$imagesItems"
                    resource="properties" :id="$property['id'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.image-list-field name="floor_plans" :label="__('Blueprints / Floor Plans')" :current="$floorPlansItems"
                    resource="properties" :id="$property['id'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.bilingual-field label="{{ __('Description') }}" :hint="__('(shown in the property\'s detail pop-up)')" type="textarea"
                    name-en="description" name-ar="description_ar"
                    :value-en="$property['description'] ?? null" :value-ar="$property['description_ar'] ?? null" />
            </div>

            <div class="sm:col-span-2">
                <x-nakheel.repeater name="features" mode="tags" :label="__('Features')" :items="$featuresItems" />
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm text-ink/70">
                    <input type="checkbox" name="featured_on_login" value="1" {{ $old('featured_on_login', $property['featured_on_login'] ?? false) ? 'checked' : '' }}
                           class="rounded border-border text-gold focus:ring-0 focus:ring-offset-0">
                    {{ __('Feature this property on the login page') }}
                </label>
            </div>

            @if ($canManageAvailability && !($property['id'] ?? null) && in_array($selectedStatus, ['Rentable', 'Booked'], true))
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-ink/60 mb-1.5">{{ __('Availability Calendar') }}</label>
                    <p class="text-xs text-ink/50">{{ __('Save this property first, then manage its availability calendar from the edit page.') }}</p>
                </div>
            @endif
        </div>
    </div>

    @unless ($showsAvailability)
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
                {{ __('SAVE PROPERTY') }}
            </button>
            <a href="{{ route('properties.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
        </div>
    @endunless
</form>

{{--
    Deliberately OUTSIDE the <form> above: <x-nakheel.availability-manager>
    renders its own <form> internally, and a <form> nested inside another
    <form> is invalid HTML — browsers silently drop the inner tag when
    parsing it, which made its Add/Edit button submit the outer property
    form instead (redirecting to the properties list without ever calling
    the availability endpoint). Keeping it as a sibling section avoids that
    entirely; it's already a self-contained AJAX widget that saves on its
    own, independent of "SAVE PROPERTY" below.
--}}
@if ($showsAvailability)
    <div class="border border-border rounded-lg bg-surface p-6 mt-8">
        <x-nakheel.availability-manager :property-id="$property['id']" :ranges="$availabilityRanges ?? []"
            :can-create="auth()->user()?->hasPermission('property_availability', 'create')"
            :can-edit="auth()->user()?->hasPermission('property_availability', 'edit')"
            :can-delete="auth()->user()?->hasPermission('property_availability', 'delete')" />
    </div>

    {{-- Submits the form above via form="property-form" — a button can live outside its form and still submit it. --}}
    <div class="flex items-center gap-3 mt-8">
        <button type="submit" form="property-form" class="rounded-md bg-brand px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand/90">
            {{ __('SAVE PROPERTY') }}
        </button>
        <a href="{{ route('properties.index') }}" class="text-sm text-ink/50 hover:text-ink">{{ __('Cancel') }}</a>
    </div>
@endif
