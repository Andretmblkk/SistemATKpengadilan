<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_gudang']);
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'pimpinan']);
    }
}
