<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class, // <-- ADDED THIS CALL
            // DocumentTypeSeeder::class, // Call this if it exists and is needed now
            // Add other essential seeders like CountrySeeder later when created
        ]);

        // Removed the User::factory() call for admin here,
        // as UserSeeder now handles creating all default users using firstOrCreate.
    }
}
