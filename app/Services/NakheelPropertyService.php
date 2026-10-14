<?php

namespace App\Services;

use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/**
 * NakheelPropertyService — business logic + data access for property
 * listings (units that belong to a NakheelProject).
 *
 * baseQuery() LEFT JOINs nakheel_projects so every row carries the parent
 * project's name/slug in one query.
 */
class NakheelPropertyService
{
    use ResolvesTranslations;

    private string $table = 'nakheel_properties';

    /** Statuses (canonical name — same matching convention as the Availability filter) the calendar applies to. */
    private const CALENDAR_STATUSES = ['Rentable', 'Booked'];

    public function __construct(
        private NakheelPropertyAvailabilityService $availability,
    ) {}

    /**
     * Display-shaped rows (specs exploded to an array) for the public site + admin listing.
     *
     * @param bool $includeSold Whether rows whose status is flagged is_sold should be
     *     included. Admins always pass true (see NakheelController); normal users pass
     *     the global "show_sold_properties" setting — see NakheelSettingService.
     */
    public function allForDisplay(bool $includeSold = true): array
    {
        return $this->baseQuery($includeSold)
            ->orderBy('nakheel_properties.id', 'desc')
            ->get()
            ->map(fn ($row) => $this->toDisplayShape($row))
            ->all();
    }

    /** Display-shaped rows belonging to one project, for its "available units" section. */
    public function forProject(int $projectId, bool $includeSold = true): array
    {
        return $this->baseQuery($includeSold)
            ->where('nakheel_properties.nakheel_project_id', $projectId)
            ->orderBy('nakheel_properties.id')
            ->get()
            ->map(fn ($row) => $this->toDisplayShape($row))
            ->all();
    }

    /** Raw row for the admin edit form. */
    public function find(int $id): ?array
    {
        $row = DB::table($this->table)->find($id);

        return $row ? (array) $row : null;
    }

    public function create(array $data): void
    {
        DB::table($this->table)->insert([
            ...$this->columns($data),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $data): void
    {
        DB::table($this->table)->where('id', $id)->update([
            ...$this->columns($data),
            'updated_at' => now(),
        ]);
    }

    public function delete(int $id): void
    {
        DB::table($this->table)->delete($id);
    }

    /** The one property (if any) flagged to appear on the login page's "featured property" teaser. */
    public function featuredForLogin(): ?array
    {
        $row = $this->baseQuery()->where('nakheel_properties.featured_on_login', true)->first();

        return $row ? $this->toDisplayShape($row) : null;
    }

    /** Unsets the login-featured flag on every property — called before setting a new one, since only one may be featured at a time. */
    public function clearFeaturedOnLogin(): void
    {
        DB::table($this->table)->update(['featured_on_login' => false]);
    }

    private function columns(array $data): array
    {
        return [
            'nakheel_project_id' => $data['nakheel_project_id'] ?: null,
            'title' => $data['title'],
            'title_ar' => $data['title_ar'] ?? null,
            'type' => $data['type'],
            'type_ar' => $data['type_ar'] ?? null,
            'status' => $data['status'],
            'price' => $data['price'],
            'location' => $data['location'],
            'location_ar' => $data['location_ar'] ?? null,
            'specs' => $data['specs'] ?? null,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'features' => $data['features'] ?? null,
            'images' => $data['images'] ?? null,
            'floor_plans' => $data['floor_plans'] ?? null,
            'featured_on_login' => $data['featured_on_login'] ?? false,
        ];
    }

    private function baseQuery(bool $includeSold = true)
    {
        return DB::table('nakheel_properties')
            ->leftJoin('nakheel_projects', 'nakheel_properties.nakheel_project_id', '=', 'nakheel_projects.id')
            ->leftJoin('nakheel_property_statuses', 'nakheel_properties.status', '=', 'nakheel_property_statuses.name')
            ->when(!$includeSold, fn ($q) => $q->where(fn ($q2) => $q2->where('nakheel_property_statuses.is_sold', false)->orWhereNull('nakheel_property_statuses.is_sold')))
            ->select(
                'nakheel_properties.*',
                'nakheel_projects.name as project_name',
                'nakheel_projects.name_ar as project_name_ar',
                'nakheel_projects.slug as project_slug',
                'nakheel_projects.manager_name as manager_name',
                'nakheel_projects.manager_role as manager_role',
                'nakheel_projects.manager_role_ar as manager_role_ar',
                'nakheel_projects.manager_phone as manager_phone',
                'nakheel_projects.manager_whatsapp as manager_whatsapp',
                'nakheel_projects.manager_email as manager_email',
                'nakheel_projects.office_name as office_name',
                'nakheel_projects.office_name_ar as office_name_ar',
                'nakheel_projects.office_address as office_address',
                'nakheel_projects.office_address_ar as office_address_ar',
                'nakheel_projects.office_hours as office_hours',
                'nakheel_projects.office_hours_ar as office_hours_ar',
                'nakheel_projects.maps_url as maps_url',
                'nakheel_property_statuses.color as status_color',
                'nakheel_property_statuses.name_ar as status_name_ar',
                'nakheel_property_statuses.is_sold as status_is_sold',
            );
    }

    private function toDisplayShape(object $row): array
    {
        // A property's status is stored as a plain string (nakheel_properties.status),
        // matched by name against nakheel_property_statuses — not a foreign key. If an
        // admin deletes that status row later, baseQuery()'s LEFT JOIN simply stops
        // matching (status_color, the one column every real status row always has,
        // comes back null) rather than the property breaking. Falling back to the
        // same "Unassigned" label/treatment already used above for a missing project
        // keeps this consistent and self-explanatory instead of silently showing a
        // stale English string with no color and no Arabic translation.
        $statusExists = !is_null($row->status_color);

        return [
            'id' => $row->id,
            'title' => $this->tr($row->title, $row->title_ar),
            'type' => $row->type,
            'typeLabel' => $this->tr($row->type, $row->type_ar),
            'status' => $row->status,
            'statusLabel' => $statusExists ? $this->tr($row->status, $row->status_name_ar) : __('Unassigned'),
            'statusIsOrphaned' => !$statusExists,
            'price' => (int) $row->price,
            'project' => $row->project_name ?? 'Unassigned',
            'projectLabel' => $row->project_name ? $this->tr($row->project_name, $row->project_name_ar) : __('Unassigned'),
            'projectSlug' => $row->project_slug,
            'location' => $row->location,
            'locationLabel' => $this->tr($row->location, $row->location_ar),
            'specs' => collect(json_decode($row->specs ?? '', true) ?: [])->map(fn ($spec) => [
                'label' => $this->tr($spec['label'] ?? null, $spec['label_ar'] ?? null),
                'value' => $this->tr($spec['value'] ?? null, $spec['value_ar'] ?? null),
            ])->all(),
            'description' => $this->tr($row->description, $row->description_ar),
            'features' => collect(json_decode($row->features ?? '', true) ?: [])->map(fn ($item) => $this->tr($item['value'] ?? null, $item['value_ar'] ?? null))->filter()->all(),
            'images' => collect(explode(',', $row->images ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all(),
            'floorPlans' => collect(explode(',', $row->floor_plans ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all(),
            'featuredOnLogin' => (bool) $row->featured_on_login,
            'isSold' => (bool) ($row->status_is_sold ?? false),
            'hasAvailabilityCalendar' => $hasCalendar = in_array($row->status, self::CALENDAR_STATUSES, true),
            // Raw unavailable periods (for the JS month grid) and the computed
            // available/unavailable timeline (for the static list under it) —
            // both derived from the same stored periods, see
            // NakheelPropertyAvailabilityService::calculatedRanges().
            'unavailablePeriods' => $hasCalendar ? $this->availability->forProperty($row->id) : [],
            'availabilityRanges' => $hasCalendar ? $this->availability->calculatedRanges($row->id) : [],
            'statusColor' => NakheelPropertyStatusService::colorClass($row->status_color ?? null),
            'contact' => $this->contactShape($row),
        ];
    }

    /** Contact card for the "Contact Owner" pop-up — the property's project manager, or a general Nakheel Properties fallback for unassigned units. */
    private function contactShape(object $row): array
    {
        if (filled($row->manager_name ?? null)) {
            return [
                'name' => $row->manager_name,
                'role' => $this->tr($row->manager_role, $row->manager_role_ar),
                'phone' => $row->manager_phone,
                'whatsapp' => $row->manager_whatsapp,
                'email' => $row->manager_email,
                'office' => $this->tr($row->office_name, $row->office_name_ar),
                'address' => $this->tr($row->office_address, $row->office_address_ar),
                'hours' => $this->tr($row->office_hours, $row->office_hours_ar),
                'mapsUrl' => $row->maps_url,
            ];
        }

        return [
            'name' => 'Nakheel Properties',
            'role' => __('Sales Team'),
            'phone' => '+218 91 000 0000',
            'whatsapp' => '+218 91 000 0000',
            'email' => 'sales@nakheel.ly',
            'office' => __('Nakheel Properties — Head Office'),
            'address' => __('Tripoli, Libya'),
            'hours' => __('Sun–Thu: 9:00 AM – 5:00 PM'),
            'mapsUrl' => null,
        ];
    }
}
