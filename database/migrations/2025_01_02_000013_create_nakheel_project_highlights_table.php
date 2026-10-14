<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_project_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nakheel_project_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('nakheel_projects')->select('id', 'highlights')->orderBy('id')->each(function ($project) use ($now) {
            collect(explode(',', $project->highlights ?? ''))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->values()
                ->each(function ($label, $index) use ($project, $now) {
                    DB::table('nakheel_project_highlights')->insert([
                        'nakheel_project_id' => $project->id,
                        'label' => $label,
                        'image' => null,
                        'sort_order' => $index,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
        });

        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->dropColumn('highlights');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_projects', function (Blueprint $table) {
            $table->text('highlights')->nullable();
        });

        DB::table('nakheel_projects')->select('id')->orderBy('id')->each(function ($project) {
            $labels = DB::table('nakheel_project_highlights')
                ->where('nakheel_project_id', $project->id)
                ->orderBy('sort_order')
                ->pluck('label')
                ->implode(',');

            DB::table('nakheel_projects')->where('id', $project->id)->update(['highlights' => $labels]);
        });

        Schema::dropIfExists('nakheel_project_highlights');
    }
};
