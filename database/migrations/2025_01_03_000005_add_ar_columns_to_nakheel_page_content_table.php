<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_page_content', function (Blueprint $table) {
            $table->string('heading_ar')->nullable()->after('heading');
            $table->text('body_ar')->nullable()->after('body');
            $table->string('office_address_ar')->nullable()->after('office_address');
            $table->string('office_hours_ar')->nullable()->after('office_hours');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_page_content', function (Blueprint $table) {
            $table->dropColumn(['heading_ar', 'body_ar', 'office_address_ar', 'office_hours_ar']);
        });
    }
};
