<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan pemeriksaan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Reset permissions dan roles
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // Aktifkan kembali pemeriksaan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat permission
        $permissions = [
            'manage-items',
            'manage-requests',
            'view-reports',
            'approve-requests',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat role
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);
        $pimpinanRole = Role::create(['name' => 'pimpinan']);

        // Tetapkan permission ke role
        $adminRole->givePermissionTo(Permission::all());
        $staffRole->givePermissionTo(['manage-items', 'manage-requests']);
        $pimpinanRole->givePermissionTo(['view-reports', 'approve-requests']);
    }
}