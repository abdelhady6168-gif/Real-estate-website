<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->string('status_ar')->nullable()->after('status');
            $table->string('title_ar')->nullable()->after('title');
            $table->string('meta_ar')->nullable()->after('meta');
            $table->text('description_ar')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_timeline_entries', function (Blueprint $table) {
            $table->dropColumn(['status_ar', 'title_ar', 'meta_ar', 'description_ar']);
        });
    }
};
