<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('office_hours_from_day')->nullable()->after('office_hours_ar');
            $table->string('office_hours_to_day')->nullable()->after('office_hours_from_day');
            $table->string('office_hours_from_time')->nullable()->after('office_hours_to_day');
            $table->string('office_hours_to_time')->nullable()->after('office_hours_from_time');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn([
                'office_hours_from_day', 'office_hours_to_day',
                'office_hours_from_time', 'office_hours_to_time',
            ]);
        });
    }
};
