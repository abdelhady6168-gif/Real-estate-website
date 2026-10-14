<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->boolean('featured_on_login')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->dropColumn('featured_on_login');
        });
    }
};
