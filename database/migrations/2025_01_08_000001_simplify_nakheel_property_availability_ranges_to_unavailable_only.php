<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Simplifies nakheel_property_availability_ranges to store ONLY unavailable
 * periods. A property is available by default; only exceptions (booked/
 * blocked date ranges) are stored, and "available" segments are computed
 * on the fly from the gaps between them — see
 * NakheelPropertyAvailabilityService::calculatedRanges().
 *
 * This replaces the previous design, which required the admin to also
 * create explicit "Available" rows (is_available = true) alongside
 * "Unavailable" ones — confusing to manage and an unnecessary second
 * source of truth. Existing rows already flagged is_available = true were
 * only ever standing in for "the rest of the calendar is open", which is
 * now the implicit default, so they're removed here; rows flagged
 * is_available = false (real booked/blocked periods) are preserved as-is,
 * just without the now-redundant column.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('nakheel_property_availability_ranges')->where('is_available', true)->delete();

        Schema::table('nakheel_property_availability_ranges', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_property_availability_ranges', function (Blueprint $table) {
            $table->boolean('is_available')->default(false)->after('end_date');
        });
    }
};
