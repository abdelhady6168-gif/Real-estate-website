<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_timeline_entries', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('status');
            $table->string('title');
            $table->string('meta')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_timeline_entries');
    }
};
