<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * NakheelPropertyAvailabilityService — UNAVAILABLE date periods for the
 * availability calendar (Rentable/Booked properties only — see
 * NakheelPropertyService::toDisplayShape()).
 *
 * A property is available by default. Only exceptions — periods it is
 * NOT available — are stored here; "available" is never persisted, it's
 * whatever isn't covered by an unavailable period. See calculatedRanges()
 * for where that gap-filling happens. Same DB::table() service style as
 * every other Nakheel service in this app.
 */
class NakheelPropertyAvailabilityService
{
    private string $table = 'nakheel_property_availability_ranges';

    /** Raw unavailable periods for one property, earliest first — this is the single source of truth the calendar, the range list, and the admin manager all read from. */
    public function forProperty(int $propertyId): array
    {
        return DB::table($this->table)
            ->where('nakheel_property_id', $propertyId)
            ->orderBy('start_date')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    public function find(int $id): ?array
    {
        $row = DB::table($this->table)->find($id);

        return $row ? (array) $row : null;
    }

    public function create(int $propertyId, array $data): void
    {
        DB::table($this->table)->insert([
            'nakheel_property_id' => $propertyId,
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

    /**
     * Runs $callback (an overlaps() check followed by a create()/update())
     * as one step per property, so two saves arriving together — e.g. a
     * double-clicked Save — can't both pass the overlap check before either
     * has written, and end up storing overlapping periods. Locks the parent
     * property row (MySQL/Postgres); on SQLite the loser of the write lock
     * gets "database is locked", which DB::transaction() retries, re-running
     * the check against the committed data.
     */
    public function exclusivelyForProperty(int $propertyId, \Closure $callback): mixed
    {
        return DB::transaction(function () use ($propertyId, $callback) {
            DB::table('nakheel_properties')->where('id', $propertyId)->lockForUpdate()->first();

            return $callback();
        }, 5);
    }

    /** True if [start, end] overlaps any existing unavailable period for this property, other than $exceptId (used when editing one). */
    public function overlaps(int $propertyId, string $start, string $end, ?int $exceptId = null): bool
    {
        return DB::table($this->table)
            ->where('nakheel_property_id', $propertyId)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->exists();
    }

    /**
     * The full available/unavailable timeline for display under the calendar
     * (see <x-nakheel.availability-calendar>) — computed from the stored
     * unavailable periods, clipped to a one-year forward-looking window
     * (today → +1 year) since "available" has no natural start/end of its
     * own to anchor on otherwise.
     *
     * @return array<int, array{start_date: string, end_date: string, is_available: bool}>
     */
    public function calculatedRanges(int $propertyId, ?string $from = null, ?string $to = null): array
    {
        $windowStart = Carbon::parse($from ?? now()->toDateString());
        $windowEnd = Carbon::parse($to ?? now()->addYear()->toDateString());

        $unavailable = collect($this->forProperty($propertyId))
            ->map(fn ($row) => [
                'start' => Carbon::parse($row['start_date'])->max($windowStart),
                'end' => Carbon::parse($row['end_date'])->min($windowEnd),
            ])
            ->filter(fn ($row) => $row['start']->lte($row['end']))
            ->sortBy('start')
            ->values();

        $segments = [];
        $cursor = $windowStart->copy();

        foreach ($unavailable as $period) {
            if ($period['start']->gt($cursor)) {
                $segments[] = $this->segment($cursor, $period['start']->copy()->subDay(), true);
            }

            $segments[] = $this->segment($period['start'], $period['end'], false);
            $cursor = $period['end']->copy()->addDay();

            if ($cursor->gt($windowEnd)) {
                break;
            }
        }

        if ($cursor->lte($windowEnd)) {
            $segments[] = $this->segment($cursor, $windowEnd, true);
        }

        return $segments;
    }

    private function segment(Carbon $start, Carbon $end, bool $isAvailable): array
    {
        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'is_available' => $isAvailable,
        ];
    }

    private function columns(array $data): array
    {
        return [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
    }
}
