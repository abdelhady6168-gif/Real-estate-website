<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nakheel_page_content', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('image');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('telegram')->nullable()->after('whatsapp');
            $table->string('email')->nullable()->after('telegram');
            $table->string('office_address')->nullable()->after('email');
            $table->string('office_hours')->nullable()->after('office_address');
        });
    }

    public function down(): void
    {
        Schema::table('nakheel_page_content', function (Blueprint $table) {
            $table->dropColumn(['phone', 'whatsapp', 'telegram', 'email', 'office_address', 'office_hours']);
        });
    }
};
