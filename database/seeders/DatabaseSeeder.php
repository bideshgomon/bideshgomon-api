<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RoleSeeder first (it should already be here)
        $this->call(RoleSeeder::class);

        // --- ADD CALLS TO NEW SEEDERS ---
        $this->call([
            CountrySeeder::class,
            SkillSeeder::class,
            DocumentTypeSeeder::class,
            // Add other seeders here if needed
        ]);
        // ---------------------------------

        // Create the Admin User (it should already be here)
        // Adjust details if necessary
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@bideshgomon.com',
            'role_id' => \App\Models\Role::where('name', 'admin')->first()->id,
            // Add other default admin fields if needed
        ]);

        // Optional: Create factory data for testing
        // User::factory(10)->create();
    }
}