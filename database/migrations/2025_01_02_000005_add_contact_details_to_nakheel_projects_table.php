<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('manager_phone')->nullable()->after('manager_role');
            $table->string('manager_whatsapp')->nullable()->after('manager_phone');
            $table->string('manager_email')->nullable()->after('manager_whatsapp');
            $table->string('office_name')->nullable()->after('manager_email');
            $table->string('office_address')->nullable()->after('office_name');
            $table->string('office_hours')->nullable()->after('office_address');
            $table->string('maps_url')->nullable()->after('office_hours');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn([
                'manager_phone',
                'manager_whatsapp',
                'manager_email',
                'office_name',
                'office_address',
                'office_hours',
                'maps_url',
            ]);
        });
    }
};
