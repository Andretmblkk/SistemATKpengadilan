<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Kategori: Alat Tulis (20 item)
            ['name' => 'Pulpen Hitam', 'description' => 'Pulpen tinta hitam untuk keperluan kantor', 'stock' => 100, 'price' => 5000, 'category' => 'alat_tulis'],
            ['name' => 'Pulpen Biru', 'description' => 'Pulpen tinta biru standar', 'stock' => 80, 'price' => 5000, 'category' => 'alat_tulis'],
            ['name' => 'Pulpen Merah', 'description' => 'Pulpen tinta merah untuk koreksi', 'stock' => 60, 'price' => 5000, 'category' => 'alat_tulis'],
            ['name' => 'Pencil 2B', 'description' => 'Pensil 2B untuk ujian', 'stock' => 50, 'price' => 2000, 'category' => 'alat_tulis'],
            ['name' => 'Penghapus Karet', 'description' => 'Penghapus karet untuk pensil', 'stock' => 60, 'price' => 1000, 'category' => 'alat_tulis'],
            ['name' => 'Penggaris 30cm', 'description' => 'Penggaris plastik 30cm', 'stock' => 40, 'price' => 3000, 'category' => 'alat_tulis'],
            ['name' => 'Spidol Hitam', 'description' => 'Spidol papan tulis hitam', 'stock' => 30, 'price' => 10000, 'category' => 'alat_tulis'],
            ['name' => 'Spidol Merah', 'description' => 'Spidol papan tulis merah', 'stock' => 25, 'price' => 10000, 'category' => 'alat_tulis'],
            ['name' => 'Spidol Biru', 'description' => 'Spidol papan tulis biru', 'stock' => 25, 'price' => 10000, 'category' => 'alat_tulis'],
            ['name' => 'Buku Catatan A5', 'description' => 'Buku catatan ukuran A5', 'stock' => 70, 'price' => 15000, 'category' => 'alat_tulis'],
            ['name' => 'Buku Catatan A4', 'description' => 'Buku catatan ukuran A4', 'stock' => 60, 'price' => 20000, 'category' => 'alat_tulis'],
            ['name' => 'Kertas HVS A4', 'description' => 'Kertas HVS ukuran A4 (rim)', 'stock' => 20, 'price' => 50000, 'category' => 'alat_tulis'],
            ['name' => 'Kertas HVS F4', 'description' => 'Kertas HVS ukuran F4 (rim)', 'stock' => 15, 'price' => 55000, 'category' => 'alat_tulis'],
            ['name' => 'Stapler Kecil', 'description' => 'Stapler ukuran kecil', 'stock' => 25, 'price' => 15000, 'category' => 'alat_tulis'],
            ['name' => 'Isi Stapler', 'description' => 'Isi ulang stapler', 'stock' => 100, 'price' => 5000, 'category' => 'alat_tulis'],
            ['name' => 'Pita Koreksi', 'description' => 'Pita koreksi untuk pulpen', 'stock' => 40, 'price' => 8000, 'category' => 'alat_tulis'],
            ['name' => 'Highlighter Kuning', 'description' => 'Spidol highlighter warna kuning', 'stock' => 30, 'price' => 7000, 'category' => 'alat_tulis'],
            ['name' => 'Highlighter Hijau', 'description' => 'Spidol highlighter warna hijau', 'stock' => 30, 'price' => 7000, 'category' => 'alat_tulis'],
            ['name' => 'Penjepit Kertas', 'description' => 'Penjepit kertas kecil', 'stock' => 50, 'price' => 2000, 'category' => 'alat_tulis'],
            ['name' => 'Tinta Printer', 'description' => 'Tinta printer warna hitam', 'stock' => 10, 'price' => 100000, 'category' => 'alat_tulis'],

            // Kategori: Perlengkapan Kantor (20 item)
            ['name' => 'Meja Kantor Kayu', 'description' => 'Meja kayu untuk pegawai', 'stock' => 5, 'price' => 1500000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Kursi Kantor Ergonomis', 'description' => 'Kursi ergonomis untuk kantor', 'stock' => 10, 'price' => 800000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Lemari Arsip Besi', 'description' => 'Lemari arsip besi tahan lama', 'stock' => 3, 'price' => 2000000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Rak Dokumen Logam', 'description' => 'Rak untuk menyimpan dokumen', 'stock' => 5, 'price' => 500000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Printer Inkjet', 'description' => 'Printer inkjet multifungsi', 'stock' => 2, 'price' => 2500000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Scanner A4', 'description' => 'Scanner untuk dokumen A4', 'stock' => 2, 'price' => 1500000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Mesin Fotokopi', 'description' => 'Mesin fotokopi A3/A4', 'stock' => 1, 'price' => 5000000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Proyektor Presentasi', 'description' => 'Proyektor untuk rapat', 'stock' => 2, 'price' => 3000000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Papan Tulis Besar', 'description' => 'Papan tulis ukuran 120x240 cm', 'stock' => 3, 'price' => 400000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Penghapus Papan Tulis', 'description' => 'Penghapus untuk papan tulis', 'stock' => 10, 'price' => 15000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Kipas Angin Dinding', 'description' => 'Kipas angin untuk ruang kantor', 'stock' => 5, 'price' => 300000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'AC Split 1 PK', 'description' => 'AC split untuk ruangan kecil', 'stock' => 2, 'price' => 3500000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Lampu Meja LED', 'description' => 'Lampu meja hemat energi', 'stock' => 10, 'price' => 100000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Jam Dinding Analog', 'description' => 'Jam dinding untuk kantor', 'stock' => 5, 'price' => 50000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Tempat Sampah Plastik', 'description' => 'Tempat sampah untuk ruang kantor', 'stock' => 10, 'price' => 25000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Mesin Penghancur Kertas', 'description' => 'Mesin penghancur kertas kecil', 'stock' => 2, 'price' => 1200000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Kabel HDMI 2m', 'description' => 'Kabel HDMI untuk presentasi', 'stock' => 10, 'price' => 50000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Kabel LAN 5m', 'description' => 'Kabel LAN untuk jaringan', 'stock' => 15, 'price' => 30000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'Stop Kontak 4 Lubang', 'description' => 'Stop kontak untuk perangkat', 'stock' => 10, 'price' => 75000, 'category' => 'perlengkapan_kantor'],
            ['name' => 'UPS 1000VA', 'description' => 'UPS untuk cadangan listrik', 'stock' => 3, 'price' => 1500000, 'category' => 'perlengkapan_kantor'],

            // Kategori: Dokumen (20 item)
            ['name' => 'Map Snelhecter Plastik', 'description' => 'Map snelhecter untuk dokumen', 'stock' => 50, 'price' => 5000, 'category' => 'dokumen'],
            ['name' => 'Map Folder A4', 'description' => 'Map folder untuk dokumen A4', 'stock' => 40, 'price' => 4000, 'category' => 'dokumen'],
            ['name' => 'Buku Agenda A5', 'description' => 'Buku agenda untuk catatan harian', 'stock' => 30, 'price' => 25000, 'category' => 'dokumen'],
            ['name' => 'Buku Register', 'description' => 'Buku register untuk arsip', 'stock' => 20, 'price' => 30000, 'category' => 'dokumen'],
            ['name' => 'Ordner A4', 'description' => 'Ordner untuk arsip dokumen A4', 'stock' => 25, 'price' => 35000, 'category' => 'dokumen'],
            ['name' => 'Ordner F4', 'description' => 'Ordner untuk arsip dokumen F4', 'stock' => 20, 'price' => 40000, 'category' => 'dokumen'],
            ['name' => 'Box Arsip Karton', 'description' => 'Box untuk menyimpan arsip', 'stock' => 15, 'price' => 20000, 'category' => 'dokumen'],
            ['name' => 'Label Dokumen', 'description' => 'Label stiker untuk dokumen', 'stock' => 100, 'price' => 1000, 'category' => 'dokumen'],
            ['name' => 'Kertas Karbon', 'description' => 'Kertas karbon untuk salinan', 'stock' => 50, 'price' => 3000, 'category' => 'dokumen'],
            ['name' => 'Amplop Cokelat A4', 'description' => 'Amplop cokelat ukuran A4', 'stock' => 100, 'price' => 1500, 'category' => 'dokumen'],
            ['name' => 'Amplop Putih Kecil', 'description' => 'Amplop putih untuk surat', 'stock' => 100, 'price' => 1000, 'category' => 'dokumen'],
            ['name' => 'Stempel Tanggal', 'description' => 'Stempel dengan tanggal', 'stock' => 5, 'price' => 50000, 'category' => 'dokumen'],
            ['name' => 'Tinta Stempel Biru', 'description' => 'Tinta stempel warna biru', 'stock' => 10, 'price' => 15000, 'category' => 'dokumen'],
            ['name' => 'Buku Kwitansi', 'description' => 'Buku kwitansi untuk transaksi', 'stock' => 20, 'price' => 20000, 'category' => 'dokumen'],
            ['name' => 'Buku Surat Jalan', 'description' => 'Buku surat jalan untuk pengiriman', 'stock' => 15, 'price' => 25000, 'category' => 'dokumen'],
            ['name' => 'Map Laporan Resmi', 'description' => 'Map untuk laporan resmi', 'stock' => 30, 'price' => 6000, 'category' => 'dokumen'],
            ['name' => 'Divider Plastik', 'description' => 'Divider plastik untuk ordner', 'stock' => 50, 'price' => 2000, 'category' => 'dokumen'],
            ['name' => 'Kertas NCR', 'description' => 'Kertas NCR untuk dokumen ganda', 'stock' => 20, 'price' => 60000, 'category' => 'dokumen'],
            ['name' => 'Buku Nota Kecil', 'description' => 'Buku nota untuk catatan', 'stock' => 30, 'price' => 10000, 'category' => 'dokumen'],
            ['name' => 'Folder Arsip', 'description' => 'Folder untuk arsip dokumen', 'stock' => 40, 'price' => 5000, 'category' => 'dokumen'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}