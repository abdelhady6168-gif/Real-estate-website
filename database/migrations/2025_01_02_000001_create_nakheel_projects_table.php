<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('location');
            $table->string('tagline')->nullable();
            $table->string('hero_image')->nullable();
            $table->text('description')->nullable();
            $table->string('developer')->nullable();
            $table->string('status')->nullable();
            $table->string('completion_year')->nullable();
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('available_units')->default(0);
            $table->string('development_area')->nullable();
            $table->string('property_types')->nullable();
            $table->text('highlights')->nullable();
            $table->text('environment_images')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('manager_role')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_projects');
    }
};
