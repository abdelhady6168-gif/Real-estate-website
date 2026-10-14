<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('type_ar')->nullable()->after('type');
            $table->string('location_ar')->nullable()->after('location');
            $table->string('tagline_ar')->nullable()->after('tagline');
            $table->text('description_ar')->nullable()->after('description');
            $table->string('developer_ar')->nullable()->after('developer');
            $table->string('status_ar')->nullable()->after('status');
            $table->string('development_area_ar')->nullable()->after('development_area');
            $table->string('property_types_ar')->nullable()->after('property_types');
            $table->string('manager_role_ar')->nullable()->after('manager_role');
            $table->string('office_name_ar')->nullable()->after('office_name');
            $table->string('office_address_ar')->nullable()->after('office_address');
            $table->string('office_hours_ar')->nullable()->after('office_hours');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'type_ar', 'location_ar', 'tagline_ar', 'description_ar',
                'developer_ar', 'status_ar', 'development_area_ar', 'property_types_ar',
                'manager_role_ar', 'office_name_ar', 'office_address_ar', 'office_hours_ar',
            ]);
        });
    }
};
