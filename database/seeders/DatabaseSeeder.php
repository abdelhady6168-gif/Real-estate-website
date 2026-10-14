<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sample-data seeder for the app.
 *
 * Seeds a handful of categories and items so the example module isn't
 * empty the first time you run the app. Uses only the Query Builder (no
 * Eloquent models), matching the Service + DTO pattern used everywhere
 * else in this project.
 *
 * Run with:  php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Login accounts live in their own seeder so they can be (re)created
        // on their own with `php artisan app:seed-auth`.
        $this->call(UserSeeder::class);

        // Nakheel Properties real-estate content (projects + properties).
        $this->call(NakheelSeeder::class);

        // Re-runnable: wipe in FK-safe order (items references categories).
        DB::table('items')->delete();
        DB::table('categories')->delete();

        $categories = ['General', 'Featured', 'Archived'];
        $categoryIds = [];

        foreach ($categories as $name) {
            $categoryIds[$name] = DB::table('categories')->insertGetId([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $items = [
            ['title' => 'First example item', 'description' => 'Created by the seeder.', 'category' => 'General'],
            ['title' => 'Second example item', 'description' => 'Belongs to Featured.', 'category' => 'Featured'],
            ['title' => 'Third example item', 'description' => null, 'category' => null],
        ];

        foreach ($items as $item) {
            DB::table('items')->insert([
                'title' => $item['title'],
                'description' => $item['description'],
                'category_id' => $item['category'] ? $categoryIds[$item['category']] : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($this->command) {
            $this->command->getOutput()->writeln('');
            $this->command->info(sprintf(
                'Seeded %d categories and %d items.',
                DB::table('categories')->count(),
                DB::table('items')->count(),
            ));
        }
    }
}
