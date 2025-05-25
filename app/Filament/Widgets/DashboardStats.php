<?php
namespace App\Filament\Widgets;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Request;
use App\Models\Item;
class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Permintaan Barang', Request::count())
                ->description('Jumlah total permintaan barang yang diajukan')
                ->color('primary'),
            Stat::make('Permintaan Pending', Request::where('status', 'pending')->count())
                ->description('Permintaan yang menunggu persetujuan')
                ->color('warning'),
            Stat::make('Item Tersedia', Item::count())
                ->description('Jumlah item yang tersedia di stok')
                ->color('success'),
            Stat::make('Stok Rendah', Item::where('stock', '<', 10)->count())
                ->description('Item dengan stok di bawah 10 unit')
                ->color('danger'),
        ];
    }
}