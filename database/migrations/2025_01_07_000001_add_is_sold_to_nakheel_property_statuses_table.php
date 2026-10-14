<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Flags which status(es) represent "sold" for the global Sold Visibility
 * setting (see nakheel_settings). A dedicated flag — rather than matching
 * the literal name "Sold" — because `name` is admin-editable and the site
 * is bilingual, so the flag stays correct even if a status is renamed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_property_statuses', function (Blueprint $table) {
            $table->boolean('is_sold')->default(false)->after('color');
        });

        // One-time backfill for existing data: today's "Sold" row (seeded by
        // NakheelSeeder) is flagged so current installs keep working exactly
        // as before without any manual admin step.
        DB::table('nakheel_property_statuses')->where('name', 'Sold')->update(['is_sold' => true]);
    }

    public function down(): void
    {
        Schema::table('nakheel_property_statuses', function (Blueprint $table) {
            $table->dropColumn('is_sold');
        });
    }
};
