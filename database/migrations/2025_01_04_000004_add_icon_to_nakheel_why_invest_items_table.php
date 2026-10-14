<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_why_invest_items', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_why_invest_items', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
