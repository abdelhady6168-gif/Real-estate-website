<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Minimal key/value store for site-wide admin toggles. First (and currently
 * only) use: the global "show Sold properties to normal users" switch — see
 * App\Services\NakheelSettingService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed the default so the setting is defined for existing installs
        // without needing a fresh seed run (off by default — preserves
        // today's behavior of Sold properties being admin-only).
        DB::table('nakheel_settings')->insert([
            'key' => 'show_sold_properties',
            'value' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_settings');
    }
};
