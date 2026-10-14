<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every "required" bilingual field (English/Arabic pair) only needs ONE of
 * the two languages filled in — the controllers now enforce that via
 * matching `required_without` rules (see ValidatesBilingualFields) instead
 * of hard-requiring the English column. But the English columns below were
 * still defined NOT NULL at the database level, so an Arabic-only save
 * would pass validation and then fail with a DB integrity error. This
 * makes just those English columns nullable to match; nothing is dropped,
 * renamed, or backfilled, and every existing row's data is untouched.
 *
 * nakheel_property_statuses.name is deliberately NOT included — it's used
 * as a canonical matching key elsewhere in the app (e.g. the Rentable/
 * Booked availability calendar, the is_sold backfill), not just a display
 * label, so it stays required in English.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->string('location')->nullable()->change();
        });

        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->string('location')->nullable()->change();
        });

        Schema::table('nakheel_stats', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
            $table->string('label')->nullable()->change();
        });

        Schema::table('nakheel_categories', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
            $table->string('title')->nullable()->change();
        });

        Schema::table('nakheel_upcoming_projects', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
        });

        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
        });

        Schema::table('nakheel_stats', function (Blueprint $table) {
            $table->string('value')->nullable(false)->change();
            $table->string('label')->nullable(false)->change();
        });

        Schema::table('nakheel_categories', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
        });

        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->string('status')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
        });

        Schema::table('nakheel_upcoming_projects', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
        });
    }
};
