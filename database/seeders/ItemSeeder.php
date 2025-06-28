<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Kategori: Alat Tulis
            [
                'name' => 'Pulpen Biru (Buah)',
                'description' => 'Pulpen tinta warna biru, merek Pilot',
                'stock' => 150,
                'reorder_point' => 30,
                'price' => 3500,
                'category' => 'alat_tulis',
            ],
            [
                'name' => 'Pulpen Hitam (Buah)',
                'description' => 'Pulpen tinta warna hitam, merek Pilot',
                'stock' => 150,
                'reorder_point' => 30,
                'price' => 3500,
                'category' => 'alat_tulis',
            ],
            [
                'name' => 'Pensil 2B (Buah)',
                'description' => 'Pensil kayu 2B, merek Faber-Castell',
                'stock' => 100,
                'reorder_point' => 20,
                'price' => 3000,
                'category' => 'alat_tulis',
            ],
            [
                'name' => 'Penghapus (Buah)',
                'description' => 'Penghapus karet lembut, merek Staedtler',
                'stock' => 80,
                'reorder_point' => 15,
                'price' => 2500,
                'category' => 'alat_tulis',
            ],
            [
                'name' => 'Spidol Boardmarker (Buah)',
                'description' => 'Spidol untuk whiteboard, merek Snowman',
                'stock' => 50,
                'reorder_point' => 10,
                'price' => 8000,
                'category' => 'alat_tulis',
            ],
            [
                'name' => 'Tipe-X (Botol)',
                'description' => 'Cairan koreksi Tipe-X, merek Kenko',
                'stock' => 40,
                'reorder_point' => 10,
                'price' => 6000,
                'category' => 'alat_tulis',
            ],

            // Kategori: Perlengkapan Kantor
            [
                'name' => 'Kertas HVS A4 (Rim)',
                'description' => 'Kertas HVS ukuran A4 80gsm, merek PaperOne, 1 rim = 500 lembar',
                'stock' => 30,
                'reorder_point' => 5,
                'price' => 60000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Kertas HVS F4 (Rim)',
                'description' => 'Kertas HVS ukuran F4 80gsm, merek PaperOne, 1 rim = 500 lembar',
                'stock' => 25,
                'reorder_point' => 5,
                'price' => 65000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Map Snelhecter (Buah)',
                'description' => 'Map snelhecter plastik, warna assorted',
                'stock' => 60,
                'reorder_point' => 15,
                'price' => 2000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Ordner (Buah)',
                'description' => 'Ordner besar untuk arsip dokumen',
                'stock' => 40,
                'reorder_point' => 10,
                'price' => 15000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Lakban Bening (Roll)',
                'description' => 'Lakban bening ukuran 48mm',
                'stock' => 30,
                'reorder_point' => 5,
                'price' => 10000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Stempel Otomatis (Buah)',
                'description' => 'Stempel otomatis untuk keperluan resmi',
                'stock' => 15,
                'reorder_point' => 3,
                'price' => 30000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Stopmap Kertas (Buah)',
                'description' => 'Stopmap kertas untuk dokumen resmi',
                'stock' => 50,
                'reorder_point' => 10,
                'price' => 2500,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Amplop Coklat A4 (Buah)',
                'description' => 'Amplop coklat ukuran A4 untuk surat resmi',
                'stock' => 40,
                'reorder_point' => 10,
                'price' => 1500,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Binder Clip 19mm (Pack)',
                'description' => 'Binder clip ukuran kecil, 1 pack = 12 buah',
                'stock' => 60,
                'reorder_point' => 15,
                'price' => 2000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Binder Clip 32mm (Pack)',
                'description' => 'Binder clip ukuran besar, 1 pack = 12 buah',
                'stock' => 40,
                'reorder_point' => 10,
                'price' => 3000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Tinta Printer Hitam (Botol)',
                'description' => 'Tinta printer hitam untuk printer inkjet',
                'stock' => 15,
                'reorder_point' => 3,
                'price' => 90000,
                'category' => 'perlengkapan_kantor',
            ],
            [
                'name' => 'Kertas Label Stiker (Pack)',
                'description' => 'Kertas label stiker A4 untuk printer, 1 pack = 50 lembar',
                'stock' => 20,
                'reorder_point' => 5,
                'price' => 15000,
                'category' => 'perlengkapan_kantor',
            ],

            // Kategori: Dokumen
            [
                'name' => 'Buku Agenda A5 (Buah)',
                'description' => 'Buku agenda harian ukuran A5',
                'stock' => 25,
                'reorder_point' => 5,
                'price' => 20000,
                'category' => 'dokumen',
            ],
            [
                'name' => 'Buku Ekspedisi Surat (Buah)',
                'description' => 'Buku ekspedisi untuk pencatatan surat masuk/keluar',
                'stock' => 15,
                'reorder_point' => 3,
                'price' => 18000,
                'category' => 'dokumen',
            ],
            [
                'name' => 'Buku Register Perkara (Buah)',
                'description' => 'Buku register untuk pencatatan perkara Pengadilan Agama',
                'stock' => 10,
                'reorder_point' => 2,
                'price' => 25000,
                'category' => 'dokumen',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}