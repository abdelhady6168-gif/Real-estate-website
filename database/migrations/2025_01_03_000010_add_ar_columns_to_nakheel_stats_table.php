<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_stats', function (Blueprint $table) {
            $table->string('value_ar')->nullable()->after('value');
            $table->string('label_ar')->nullable()->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_stats', function (Blueprint $table) {
            $table->dropColumn(['value_ar', 'label_ar']);
        });
    }
};
