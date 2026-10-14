<?php

namespace App\Services;

use App\Services\Concerns\FormatsOfficeHours;
use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/**
 * NakheelProjectService — business logic + data access for real-estate
 * projects (developments like "Lotus Compound").
 *
 * Same DB::table + array shape as the other Nakheel services. Returns plain
 * associative arrays (rather than DTOs) so the
 * public Nakheel views — written against that array shape — didn't need
 * to change when this module moved from hardcoded arrays to the database.
 */
class NakheelProjectService
{
    use ResolvesTranslations;
    use FormatsOfficeHours;

    private string $table = 'nakheel_projects';

    /** Raw rows for the admin listing screen. */
    public function all(): array
    {
        return DB::table($this->table)
            ->orderBy('name')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /** [id => name] for the property form's project dropdown. */
    public function options(): array
    {
        return DB::table($this->table)
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar'])
            ->mapWithKeys(fn ($row) => [$row->id => $this->tr($row->name, $row->name_ar)])
            ->all();
    }

    /** Raw row for the admin edit form. */
    public function find(int $id): ?array
    {
        $row = DB::table($this->table)->find($id);

        return $row ? (array) $row : null;
    }

    /** Public shape for the project-detail page (stats/highlights/manager nested). */
    public function findBySlugForDisplay(string $slug): ?array
    {
        $row = DB::table($this->table)->where('slug', $slug)->first();

        return $row ? $this->toDisplayShape($row) : null;
    }

    /** The first project, for the bare `/projects` route (no slug given). */
    public function firstForDisplay(): ?array
    {
        $row = DB::table($this->table)->orderBy('id')->first();

        return $row ? $this->toDisplayShape($row) : null;
    }

    public function create(array $data): int
    {
        return DB::table($this->table)->insertGetId([
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

    private function columns(array $data): array
    {
        return [
            'slug' => $data['slug'],
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'type' => $data['type'],
            'type_ar' => $data['type_ar'] ?? null,
            'location' => $data['location'],
            'location_ar' => $data['location_ar'] ?? null,
            'tagline' => $data['tagline'] ?? null,
            'tagline_ar' => $data['tagline_ar'] ?? null,
            'hero_images' => $data['hero_images'] ?? null,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'developer' => $data['developer'] ?? null,
            'developer_ar' => $data['developer_ar'] ?? null,
            'status' => $data['status'] ?? null,
            'status_ar' => $data['status_ar'] ?? null,
            'completion_year' => $data['completion_year'] ?? null,
            'total_units' => $data['total_units'] ?? 0,
            'available_units' => $data['available_units'] ?? 0,
            'development_area' => $data['development_area'] ?? null,
            'development_area_ar' => $data['development_area_ar'] ?? null,
            'property_types' => $data['property_types'] ?? null,
            'property_types_ar' => $data['property_types_ar'] ?? null,
            'environment_images' => $data['environment_images'] ?? null,
            'manager_name' => $data['manager_name'] ?? null,
            'manager_role' => $data['manager_role'] ?? null,
            'manager_role_ar' => $data['manager_role_ar'] ?? null,
            'manager_phone' => $data['manager_phone'] ?? null,
            'manager_whatsapp' => $data['manager_whatsapp'] ?? null,
            'manager_email' => $data['manager_email'] ?? null,
            'office_name' => $data['office_name'] ?? null,
            'office_name_ar' => $data['office_name_ar'] ?? null,
            'office_address' => $data['office_address'] ?? null,
            'office_address_ar' => $data['office_address_ar'] ?? null,
            'office_hours' => $data['office_hours'] ?? null,
            'office_hours_ar' => $data['office_hours_ar'] ?? null,
            'office_hours_from_day' => $data['office_hours_from_day'] ?? null,
            'office_hours_to_day' => $data['office_hours_to_day'] ?? null,
            'office_hours_from_time' => $data['office_hours_from_time'] ?? null,
            'office_hours_to_time' => $data['office_hours_to_time'] ?? null,
            'maps_url' => $data['maps_url'] ?? null,
            'maps_location_name' => $data['maps_location_name'] ?? null,
            'maps_location_name_ar' => $data['maps_location_name_ar'] ?? null,
        ];
    }

    /** Reshape a flat DB row into the nested array the public views expect. */
    private function toDisplayShape(object $row): array
    {
        return [
            'id' => $row->id,
            'slug' => $row->slug,
            'name' => $this->tr($row->name, $row->name_ar),
            'type' => $this->tr($row->type, $row->type_ar),
            'location' => $this->tr($row->location, $row->location_ar),
            'tagline' => $this->tr($row->tagline, $row->tagline_ar),
            'heroImage' => $this->splitList($row->hero_images)->first(),
            'description' => $this->tr($row->description, $row->description_ar),
            'developer' => $this->tr($row->developer, $row->developer_ar),
            'status' => $this->tr($row->status, $row->status_ar),
            'completion' => $row->completion_year,
            'stats' => [
                ['value' => number_format((int) $row->total_units), 'label' => __('Total Units')],
                ['value' => number_format((int) $row->available_units), 'label' => __('Available Units')],
                ['value' => $this->tr($row->development_area, $row->development_area_ar), 'label' => __('Development Area')],
                ['value' => $this->tr($row->property_types, $row->property_types_ar), 'label' => __('Property Types')],
            ],
            'environment' => $this->splitList($row->environment_images)->all(),
            'manager' => [
                'name' => $row->manager_name,
                'role' => $this->tr($row->manager_role, $row->manager_role_ar),
                'phone' => $row->manager_phone,
                'whatsapp' => $row->manager_whatsapp,
                'email' => $row->manager_email,
                'office' => $this->tr($row->office_name, $row->office_name_ar),
                'address' => $this->tr($row->office_address, $row->office_address_ar),
                'hours' => $this->formatOfficeHours(
                    $row->office_hours_from_day, $row->office_hours_to_day,
                    $row->office_hours_from_time, $row->office_hours_to_time,
                    $row->office_hours, $row->office_hours_ar,
                ),
                'mapsUrl' => $row->maps_url,
            ],
            'aboutMaps' => [
                'locationName' => $this->tr($row->maps_location_name, $row->maps_location_name_ar)
                    ?: $this->tr($row->location, $row->location_ar),
                'url' => $row->maps_url ?: ('https://www.google.com/maps/search/'.urlencode($this->tr($row->location, $row->location_ar))),
            ],
        ];
    }

    private function splitList(?string $csv)
    {
        return collect(explode(',', $csv ?? ''))
            ->map(fn ($v) => trim($v))
            ->filter();
    }
}
