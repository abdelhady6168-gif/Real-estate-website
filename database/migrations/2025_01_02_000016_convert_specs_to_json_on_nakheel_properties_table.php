<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Specs move from a comma-separated "value label" string (e.g. "4 Bedrooms")
 * to a JSON array of {label, value} pairs, edited via the admin repeater.
 * No column-type change needed — SQLite is dynamically typed and the column
 * already holds arbitrary text — only the encoding of existing values changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('nakheel_properties')->select('id', 'specs')->orderBy('id')->each(function ($property) {
            if (blank($property->specs) || str_starts_with(trim($property->specs), '[')) {
                return;
            }

            $pairs = collect(explode(',', $property->specs))
                ->map(fn ($spec) => trim($spec))
                ->filter()
                ->map(function ($spec) {
                    $parts = preg_split('/\s+/', $spec, 2);

                    return isset($parts[1])
                        ? ['label' => $parts[1], 'value' => $parts[0]]
                        : ['label' => $parts[0], 'value' => ''];
                })
                ->values()
                ->all();

            DB::table('nakheel_properties')->where('id', $property->id)->update([
                'specs' => json_encode($pairs),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('nakheel_properties')->select('id', 'specs')->orderBy('id')->each(function ($property) {
            $pairs = json_decode($property->specs ?? '[]', true) ?: [];

            $csv = collect($pairs)
                ->map(fn ($pair) => trim(($pair['value'] ?? '').' '.($pair['label'] ?? '')))
                ->filter()
                ->implode(',');

            DB::table('nakheel_properties')->where('id', $property->id)->update(['specs' => $csv]);
        });
    }
};
