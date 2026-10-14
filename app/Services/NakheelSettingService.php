<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * NakheelSettingService — tiny key/value accessor for nakheel_settings.
 * Currently backs a single global toggle (show_sold_properties); written
 * generically so a future site-wide setting doesn't need its own table.
 */
class NakheelSettingService
{
    private string $table = 'nakheel_settings';

    public function get(string $key, bool $default = false): bool
    {
        $value = DB::table($this->table)->where('key', $key)->value('value');

        return $value === null ? $default : (bool) (int) $value;
    }

    public function set(string $key, bool $value): void
    {
        $exists = DB::table($this->table)->where('key', $key)->exists();

        if ($exists) {
            DB::table($this->table)->where('key', $key)->update(['value' => $value ? '1' : '0', 'updated_at' => now()]);

            return;
        }

        DB::table($this->table)->insert([
            'key' => $key,
            'value' => $value ? '1' : '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
