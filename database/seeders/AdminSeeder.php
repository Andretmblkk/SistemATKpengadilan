<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Pastikan role sudah dibuat sebelumnya
        
        // Buat pengguna admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@atkpta.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Buat pengguna staff
        $staff = User::updateOrCreate(
            ['email' => 'staff@atkpta.test'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole('staff');

        // Buat pengguna pimpinan
        $pimpinan = User::updateOrCreate(
            ['email' => 'pimpinan@atkpta.test'],
            [
                'name' => 'Pimpinan User',
                'password' => Hash::make('password'),
            ]
        );
        $pimpinan->assignRole('pimpinan');
    }
}