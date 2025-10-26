<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to ensure roles are only created once
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'admin']);
        Role::firstOrCreate(['slug' => 'agency'], ['name' => 'agency']); // <-- ADDED
        Role::firstOrCreate(['slug' => 'consultant'], ['name' => 'consultant']); // <-- ADDED
        Role::firstOrCreate(['slug' => 'user'], ['name' => 'user']);
    }
}