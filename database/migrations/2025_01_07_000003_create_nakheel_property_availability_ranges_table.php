<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Date ranges backing the availability calendar shown on Rentable/Booked
 * properties (see resources/views/components/nakheel/availability-calendar.blade.php)
 * and managed from the property edit form (AdminPropertyAvailabilityController).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nakheel_property_availability_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nakheel_property_id')->constrained('nakheel_properties')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index('nakheel_property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nakheel_property_availability_ranges');
    }
};
