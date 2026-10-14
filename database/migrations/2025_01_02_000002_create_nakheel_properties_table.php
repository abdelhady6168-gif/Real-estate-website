<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nakheel_project_id')->nullable()->constrained('nakheel_projects')->nullOnDelete();
            $table->string('title');
            $table->string('type');
            $table->string('status');
            $table->unsignedInteger('price');
            $table->string('location');
            $table->string('specs')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_properties');
    }
};
