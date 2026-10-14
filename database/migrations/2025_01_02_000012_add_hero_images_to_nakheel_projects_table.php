<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->text('hero_images')->nullable()->after('hero_image');
        });

        DB::table('nakheel_projects')->whereNotNull('hero_image')->orderBy('id')->each(function ($project) {
            DB::table('nakheel_projects')->where('id', $project->id)->update([
                'hero_images' => $project->hero_image,
            ]);
        });

        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('hero_images');
        });

        DB::table('nakheel_projects')->whereNotNull('hero_images')->orderBy('id')->each(function ($project) {
            DB::table('nakheel_projects')->where('id', $project->id)->update([
                'hero_image' => explode(',', $project->hero_images)[0] ?? null,
            ]);
        });

        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn('hero_images');
        });
    }
};
