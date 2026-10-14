<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the Main Admin / Sub Admin role system on top of the existing
     * `is_admin` boolean (left untouched — it still just means "can enter
     * the admin panel at all"). `role` distinguishes Main from Sub; existing
     * is_admin=true accounts are migrated to Main Admin below so the current
     * admin@example.com keeps full access without recreating anything.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('is_admin');
            $table->json('permissions')->nullable()->after('role');
            $table->timestamp('disabled_at')->nullable()->after('permissions');
        });

        DB::table('users')->where('is_admin', true)->update(['role' => 'main']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'permissions', 'disabled_at']);
        });
    }
};
