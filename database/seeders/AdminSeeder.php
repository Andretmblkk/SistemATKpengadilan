<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Adminseeder extends Seeder
{
    public function run(): void
    {
        
        $users = [
            [
                'name' => 'Fatma Ainur Rosyidah',
                'email' => 'fatma@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Darodji',
                'email' => 'darodji@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ],
            [
                'name' => 'Hj. Surmiani',
                'email' => 'surmiani@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Mohammad Zulkifi Lubis',
                'email' => 'zulkifi@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Panuju Hidayat',
                'email' => 'panuju@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Haeruddin',
                'email' => 'haeruddin@pengadilan-agama.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
        ];

        // Buat pengguna dan assign role
        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
            ]);
            $user->assignRole($userData['role']);
        }
    }
}