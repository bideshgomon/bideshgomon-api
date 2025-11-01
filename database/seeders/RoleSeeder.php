<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // <-- This import is now used

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- [PATCH START] ---
        // Define roles with proper names
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Agency'],
            ['name' => 'Consultant'],
            ['name' => 'User'],
        ];

        foreach ($roles as $roleData) {
            // Use firstOrCreate, checking by slug and creating with name
            Role::firstOrCreate(
                // Generate the slug from the name
                ['slug' => Str::slug($roleData['name'])],
                // Set the name
                ['name' => $roleData['name']]
            );
        }
        // --- [PATCH END] ---
    }
}
