<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which icons from the trusted App\Support\WhyInvestIcons catalog the Main
 * Admin has enabled for the Why-Invest icon picker. Only ever stores a
 * `key` that already exists in that catalog (enforced in
 * App\Support\WhyInvestIcons::addAvailable() and validated in
 * AdminWhyInvestIconController) — never arbitrary icon markup.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_why_invest_icons', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_why_invest_icons');
    }
};
