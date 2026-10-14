<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->string('color')->nullable()->after('status');
        });

        DB::table('nakheel_timeline_entries')->whereNull('color')->update(['color' => 'emerald']);
    }

    public function down(): void
    {
        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
