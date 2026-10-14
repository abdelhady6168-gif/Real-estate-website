<?php

namespace App\Services;

use App\Services\Concerns\ResolvesTranslations;
use Illuminate\Support\Facades\DB;

/**
 * NakheelPropertyStatusService — the admin-editable list of property
 * availability statuses (Purchasable/Booked/Sold/... by default) shown in
 * the Properties page filter, the property create/edit form, and the
 * colored dot on every property card.
 *
 * Colors are a fixed named palette (not free text) so the literal Tailwind
 * class strings live in source for Tailwind's content scanner to pick up —
 * see COLORS below.
 *
 * `name` is also the value stored on nakheel_properties.status and matched
 * by the public availability filter — it must stay canonical English. See
 * namesWithLabels() for the display-only Arabic label lookup.
 */
class NakheelPropertyStatusService
{
    use ResolvesTranslations;

    private string $table = 'nakheel_property_statuses';

    public const COLORS = [
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'gray' => 'bg-gray-400',
        'sky' => 'bg-sky-500',
        'rose' => 'bg-rose-500',
        'violet' => 'bg-violet-500',
        'blue' => 'bg-blue-500',
        'red' => 'bg-red-500',
    ];

    public function all(): array
    {
        return DB::table($this->table)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /** Status names only, for the filter dropdown / form select / validation. Always canonical English — this is the stored/matched value. */
    public function names(): array
    {
        return DB::table($this->table)->orderBy('sort_order')->pluck('name')->all();
    }

    /** [english name => display label] for translating filter/select option text without changing the stored/matched value. */
    public function namesWithLabels(): array
    {
        return DB::table($this->table)
            ->orderBy('sort_order')
            ->get(['name', 'name_ar'])
            ->mapWithKeys(fn ($row) => [$row->name => $this->tr($row->name, $row->name_ar)])
            ->all();
    }

    /** Canonical names flagged is_sold — the set the global Sold Visibility setting hides from normal users. */
    public function soldNames(): array
    {
        return DB::table($this->table)->where('is_sold', true)->pluck('name')->all();
    }

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

    public static function colorClass(?string $color): string
    {
        return self::COLORS[$color] ?? self::COLORS['gray'];
    }

    private function columns(array $data): array
    {
        return [
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'color' => $data['color'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_sold' => $data['is_sold'] ?? false,
        ];
    }
}
