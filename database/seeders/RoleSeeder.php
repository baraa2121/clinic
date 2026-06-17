<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Roles
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'user'
        ]);

        // Doctor Roles
        Role::firstOrCreate([
            'name' => 'doctor',
            'guard_name' => 'user'
        ]);

        // User Roles (patients / normal users)
        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'user'
        ]);

        // optional if you want explicit patient role
        Role::firstOrCreate([
            'name' => 'patient',
            'guard_name' => 'user'
        ]);
    }
}