<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_upcoming_projects', function (Blueprint $table) {
            $table->string('badge_ar')->nullable()->after('badge');
            $table->string('title_ar')->nullable()->after('title');
            $table->text('description_ar')->nullable()->after('description');
            $table->string('launch_ar')->nullable()->after('launch');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_upcoming_projects', function (Blueprint $table) {
            $table->dropColumn(['badge_ar', 'title_ar', 'description_ar', 'launch_ar']);
        });
    }
};
