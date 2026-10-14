<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_page_content', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('heading')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_page_content');
    }
};
