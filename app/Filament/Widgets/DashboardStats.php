<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class DashboardStats extends BaseWidget
{
    // Kategori 1: Konfigurasi Widget
    protected static ?string $pollingInterval = '10s'; // Polling setiap 10 detik untuk data real-time
    protected static bool $isLazy = false; // Memastikan widget dimuat segera

    // Kategori 2: Pengambilan Data Statistik
    protected function getStats(): array
    {
        // Bersihkan cache spesifik untuk widget ini
        Cache::forget('dashboard_stats_' . auth()->id());

        // Tentukan query berdasarkan role pengguna
        $requestQuery = auth()->user()->hasRole('staff')
            ? Request::where('user_id', auth()->id()) // Staff hanya lihat permintaan mereka
            : Request::query(); // Admin dan pimpinan lihat semua permintaan

        return [
            // Statistik 1: Total Permintaan Barang
            Stat::make('Total Permintaan Barang', $requestQuery->count())
                ->description('Jumlah total permintaan barang yang diajukan')
                ->color('primary'),

            // Statistik 2: Permintaan Pending
            Stat::make('Permintaan Pending', $requestQuery->where('status', 'pending')->count())
                ->description('Permintaan yang menunggu persetujuan')
                ->color('warning'),

            // Statistik 3: Permintaan Disetujui
            Stat::make('Permintaan Disetujui', $requestQuery->where('status', 'approved')->count())
                ->description('Permintaan yang telah disetujui')
                ->color('success'),

            // Statistik 4: Item Tersedia
            Stat::make('Item Tersedia', Item::count())
                ->description('Jumlah item yang tersedia di stok')
                ->color('success'),

            // Statistik 5: Stok Rendah
            Stat::make('Stok Rendah', Item::where('stock', '<', 10)->count())
                ->description('Item dengan stok di bawah 10 unit')
                ->color('danger'),
        ];
    }
}