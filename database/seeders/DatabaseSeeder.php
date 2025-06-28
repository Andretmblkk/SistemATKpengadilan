<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Jalankan seeders secara berurutan
        $this->call([
            RoleSeeder::class,    // Wajib dijalankan pertama
            AdminSeeder::class,
            ItemSeeder::class,
           
        ]);
    }
}