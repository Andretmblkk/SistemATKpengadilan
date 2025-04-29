<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {

        User::where('email', 'admin1@gmail.com')->delete();

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        $user->assignRole('super_admin');
    }
}