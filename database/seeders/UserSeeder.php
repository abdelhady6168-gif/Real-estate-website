<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the login accounts for the app.
 *
 * Kept separate from the main DatabaseSeeder so you can (re)create just the
 * users on their own:
 *
 *     php artisan app:seed-auth          (friendly wrapper)
 *     php artisan db:seed --class=UserSeeder
 *
 * It is also called by DatabaseSeeder, so `migrate:fresh --seed` creates
 * these accounts too.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            ['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password', 'is_admin' => true],
            ['name' => 'Demo User', 'email' => 'user@example.com', 'password' => 'password', 'is_admin' => false],
        ];

        foreach ($users as $user) {
            // updateOrInsert keeps this re-runnable without duplicate emails.
            // role is set explicitly (rather than relying on the
            // add_role_and_permissions migration's one-time backfill, which
            // only touches rows that already existed at migration time) so
            // a fresh install's admin@example.com is a real Main Admin —
            // not an is_admin=true user stuck with zero permissions.
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'is_admin' => $user['is_admin'],
                    'role' => $user['is_admin'] ? 'main' : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
