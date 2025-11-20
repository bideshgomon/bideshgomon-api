<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Platform administrator with full access',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular platform user',
            ],
            [
                'name' => 'Agency',
                'slug' => 'agency',
                'description' => 'Service provider agency account',
            ],
            [
                'name' => 'Consultant',
                'slug' => 'consultant',
                'description' => 'Independent consultant account',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                ]
            );
        }

        $this->command->info('✅ Roles seeded: admin, user, agency, consultant');
    }
}
