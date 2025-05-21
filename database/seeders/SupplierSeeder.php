<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Toko ATK Papua Jaya',
                'address' => 'Jl. Ahmad Yani No. 23, Abepura, Jayapura',
                'phone' => '081234567890',
                'email' => 'papuajaya.atk@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV Kertas Sentani',
                'address' => 'Jl. Sentani No. 15, Gurabesi, Jayapura Utara',
                'phone' => '082345678901',
                'email' => 'kertas.sentani@outlook.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UD Alat Tulis Harmoni',
                'address' => 'Jl. Percetakan No. 8, Entrop, Jayapura Selatan',
                'phone' => '083456789012',
                'email' => 'harmoni.atk@yahoo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko Serba Kantor Jayapura',
                'address' => 'Jl. Koti No. 12, Bayangkara, Jayapura',
                'phone' => '084567890123',
                'email' => 'serbakantor.jyp@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Stationery Papua',
                'address' => 'Jl. Sam Ratulangi No. 30, Argapura, Jayapura',
                'phone' => '085678901234',
                'email' => 'stationery.papua@proton.me',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('suppliers')->insert($suppliers);
    }
}