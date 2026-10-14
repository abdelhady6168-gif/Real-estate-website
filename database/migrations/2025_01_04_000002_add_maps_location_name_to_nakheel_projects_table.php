<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('maps_location_name')->nullable()->after('maps_url');
            $table->string('maps_location_name_ar')->nullable()->after('maps_location_name');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn(['maps_location_name', 'maps_location_name_ar']);
        });
    }
};
