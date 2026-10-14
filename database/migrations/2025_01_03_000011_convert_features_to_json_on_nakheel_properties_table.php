<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Features move from a comma-separated string (e.g. "Private Pool,Garden
 * Terrace") to a JSON array of {value, value_ar} pairs, so each feature chip
 * can carry its own independent Arabic translation. Same technique as the
 * earlier specs CSV->JSON conversion — no column-type change needed, only
 * the encoding of existing values changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('nakheel_properties')->select('id', 'features')->orderBy('id')->each(function ($property) {
            if (blank($property->features) || str_starts_with(trim($property->features), '[')) {
                return;
            }

            $items = collect(explode(',', $property->features))
                ->map(fn ($feature) => trim($feature))
                ->filter()
                ->map(fn ($feature) => ['value' => $feature, 'value_ar' => null])
                ->values()
                ->all();

            DB::table('nakheel_properties')->where('id', $property->id)->update([
                'features' => json_encode($items),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('nakheel_properties')->select('id', 'features')->orderBy('id')->each(function ($property) {
            $items = json_decode($property->features ?? '[]', true) ?: [];

            $csv = collect($items)
                ->map(fn ($item) => trim($item['value'] ?? ''))
                ->filter()
                ->implode(',');

            DB::table('nakheel_properties')->where('id', $property->id)->update(['features' => $csv]);
        });
    }
};
