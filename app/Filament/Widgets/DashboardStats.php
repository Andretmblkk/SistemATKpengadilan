<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardStats extends BaseWidget
{
    // Kategori 1: Konfigurasi Widget
    protected static ?string $pollingInterval = '10s'; // Polling setiap 10 detik untuk data real-time
    protected static bool $isLazy = false; // Memuat segera untuk data terkini

    // Kategori 2: Pengambilan Data Statistik
    protected function getStats(): array
    {
        // Bersihkan semua cache terkait
        Cache::flush(); // Bersihkan semua cache untuk memastikan data terbaru

        // Tentukan query berdasarkan role pengguna
        $requestQuery = auth()->user()->hasRole('staff')
            ? Request::where('user_id', auth()->id()) // Staff hanya lihat permintaan mereka
            : Request::query(); // Admin dan pimpinan lihat semua permintaan

        // Log query untuk debugging
        $totalRequests = $requestQuery->count();
        $pendingRequests = $requestQuery->where('status', 'pending')->count();
        $approvedRequests = $requestQuery->where('status', 'approved')->count();
        Log::debug('DashboardStats Query: Total=' . $totalRequests . ', Pending=' . $pendingRequests . ', Approved=' . $approvedRequests . ', User ID=' . auth()->id() . ', Role=' . auth()->user()->roles->pluck('name')->implode(','));

        return [
            // Statistik 1: Total Permintaan Barang
            Stat::make('Total Permintaan Barang', $totalRequests)
                ->description('Jumlah total permintaan barang yang diajukan')
                ->color('primary'),

            // Statistik 2: Permintaan Pending
            Stat::make('Permintaan Pending', $pendingRequests)
                ->description('Permintaan yang menunggu persetujuan')
                ->color('warning'),

            // Statistik 3: Permintaan Disetujui
            Stat::make('Permintaan Disetujui', $approvedRequests)
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