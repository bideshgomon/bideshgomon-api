<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // <-- 1. IMPORT THE STRING HELPER

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Use firstOrCreate to create roles with slugs
        
        Role::firstOrCreate(
            ['slug' => 'admin'], // Check if slug exists
            ['name' => 'admin']  // Create with name if not
        );

        Role::firstOrCreate(
            ['slug' => 'user'],  // Check if slug exists
            ['name' => 'user']   // Create with name if not
        );
    }
}