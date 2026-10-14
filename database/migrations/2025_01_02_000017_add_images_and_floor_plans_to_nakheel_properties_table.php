<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->text('images')->nullable()->after('image');
            $table->text('floor_plans')->nullable()->after('images');
        });

        DB::table('nakheel_properties')->whereNotNull('image')->orderBy('id')->each(function ($property) {
            DB::table('nakheel_properties')->where('id', $property->id)->update([
                'images' => $property->image,
            ]);
        });

        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->string('image')->nullable()->after('floor_plans');
        });

        DB::table('nakheel_properties')->whereNotNull('images')->orderBy('id')->each(function ($property) {
            DB::table('nakheel_properties')->where('id', $property->id)->update([
                'image' => explode(',', $property->images)[0] ?? null,
            ]);
        });

        Schema::table('nakheel_properties', function (Blueprint $table) {
            $table->dropColumn(['images', 'floor_plans']);
        });
    }
};
