{{--
    Admin unavailable-period manager — property edit form only (needs a
    saved property id). A property is available by default; the admin only
    ever defines the periods it ISN'T (booked/blocked) — see
    NakheelPropertyAvailabilityService. AJAX add/edit/delete, no full-form
    save, mirroring <x-nakheel.image-list-field>'s AJAX mode. See
    resources/js/availability.js and AdminPropertyAvailabilityController.

    Every row (both the ones rendered here on page load and the ones
    resources/js/availability.js appends after a create/update) carries
    data-start/data-end so "Edit" can repopulate the form from either
    source — the two must stay in sync.

    canCreate/canEdit/canDelete come from the "Manage Property Availability"
    permission (see AdminPermissions::sections()) — the parent only renders
    this component at all if at least one is true (see _form.blade.php), so
    within here each just hides the one control it doesn't cover. This is a
    UX nicety only: AdminPropertyAvailabilityController's routes carry the
    same permission check server-side, so a hidden control can't be bypassed
    by calling the endpoint directly.
--}}
@props(['propertyId', 'ranges' => [], 'canCreate' => true, 'canEdit' => true, 'canDelete' => true])

<div data-availability-manager
     data-store-endpoint="{{ route('admin.properties.availability.store', $propertyId) }}"
     data-base-endpoint="{{ url('admin/properties/'.$propertyId.'/availability') }}"
     data-can-edit="{{ $canEdit ? '1' : '0' }}"
     data-can-delete="{{ $canDelete ? '1' : '0' }}"
     data-label-edit="{{ __('Edit') }}"
     data-label-delete="{{ __('Delete') }}"
     data-label-error="{{ __('Something went wrong — please try again.') }}"
     data-label-invalid-date="{{ __('Please choose both a start and an end date.') }}"
     data-empty-label="{{ __('No unavailable periods yet — this property is available year-round.') }}"
     data-confirm-delete="{{ __('Remove this unavailable period? Those dates will become available again.') }}">
    <label class="block text-xs font-medium text-ink/60 mb-2">{{ __('Unavailable Periods') }}</label>
    <p class="text-xs text-ink/40 mb-2">{{ __('This property is available by default. Only add the periods it is NOT available.') }}</p>

    <div data-availability-error class="hidden rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 mb-3 dark:border-red-900 dark:bg-red-950 dark:text-red-300"></div>

    <div class="border border-border rounded-lg overflow-hidden bg-surface">
        <div class="grid grid-cols-[1fr_1fr_auto] gap-3 px-4 py-2.5 bg-cream text-[10px] font-semibold tracking-wider text-ink/50 border-b border-border">
            <span>{{ __('From') }}</span>
            <span>{{ __('To') }}</span>
            <span>{{ __('Actions') }}</span>
        </div>

        <div data-availability-list class="divide-y divide-border">
            @forelse ($ranges as $range)
                <div data-availability-row data-id="{{ $range['id'] }}"
                     data-start="{{ $range['start_date'] }}" data-end="{{ $range['end_date'] }}"
                     class="grid grid-cols-[1fr_1fr_auto] gap-3 items-center px-4 py-2.5 text-sm">
                    <span data-availability-start-cell class="text-ink/70">{{ \Illuminate\Support\Carbon::parse($range['start_date'])->format('d/m/Y') }}</span>
                    <span data-availability-end-cell class="text-ink/70">{{ \Illuminate\Support\Carbon::parse($range['end_date'])->format('d/m/Y') }}</span>
                    <span class="flex items-center gap-3 text-xs font-medium">
                        @if ($canEdit)
                            <button type="button" data-availability-edit class="text-gold hover:underline">{{ __('Edit') }}</button>
                        @endif
                        @if ($canDelete)
                            <button type="button" data-availability-delete class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                        @endif
                    </span>
                </div>
            @empty
                <p data-availability-empty class="px-4 py-6 text-center text-sm text-ink/40">
                    {{ __('No unavailable periods yet — this property is available year-round.') }}
                </p>
            @endforelse
        </div>
    </div>

    {{-- Shared by both "Add" and "Edit" (clicking a row's Edit repopulates
         this same form) — rendered whenever either is allowed, since Edit
         depends on it existing even for an admin without Create. --}}
    @if ($canCreate || $canEdit)
        <form data-availability-form data-add-label="{{ __('Add Unavailable Period') }}" data-save-label="{{ __('Save Unavailable Period') }}"
              class="mt-4 flex flex-wrap items-end gap-3 rounded-lg border border-border bg-surface p-4">
            <input type="hidden" data-availability-editing-id value="">

            <label class="flex flex-col gap-1">
                <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('Start date') }}</span>
                <x-nakheel.date-picker-field name="availability_start" data-availability-start="1" />
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-[10px] font-semibold tracking-wider text-ink/50">{{ __('End date') }}</span>
                <x-nakheel.date-picker-field name="availability_end" data-availability-end="1" />
            </label>

            <button type="submit" data-availability-submit class="rounded-md bg-brand px-4 py-2 text-xs font-semibold text-white hover:bg-brand/90">
                {{ __('Add Unavailable Period') }}
            </button>
            <button type="button" data-availability-cancel-edit class="hidden text-xs text-ink/50 hover:text-ink">
                {{ __('Cancel') }}
            </button>
        </form>
    @endif
</div>
