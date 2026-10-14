<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->string('type_ar')->nullable()->after('type');
            $table->string('location_ar')->nullable()->after('location');
            $table->text('description_ar')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'type_ar', 'location_ar', 'description_ar']);
        });
    }
};
