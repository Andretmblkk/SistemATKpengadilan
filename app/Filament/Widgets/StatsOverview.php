<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Request;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        if (auth()->user()->hasRole('super_admin')) {
            return [
                Stat::make('Total Permintaan', Request::count())
                    ->description('Semua permintaan ATK')
                    ->descriptionIcon('heroicon-m-document-text'),
                Stat::make('Permintaan Pending', Request::where('status', 'pending')->count())
                    ->description('Menunggu persetujuan')
                    ->descriptionIcon('heroicon-m-clock'),
                Stat::make('Stok Menipis', Item::where('stock', '<=', 'minimum_stock')->count())
                    ->description('Item yang perlu diisi ulang')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
            ];
        }

        if (auth()->user()->hasRole('admin_gudang')) {
            return [
                Stat::make('Stok Menipis', Item::where('stock', '<=', 'minimum_stock')->count())
                    ->description('Item yang perlu diisi ulang')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
                Stat::make('Total Item', Item::count())
                    ->description('Jumlah jenis barang')
                    ->descriptionIcon('heroicon-m-square-3-stack-3d'),
                Stat::make('Permintaan Disetujui', Request::where('status', 'approved')->count())
                    ->description('Permintaan yang disetujui')
                    ->descriptionIcon('heroicon-m-check-circle'),
            ];
        }

        if (auth()->user()->hasRole('pimpinan')) {
            return [
                Stat::make('Permintaan Pending', Request::where('status', 'pending')->count())
                    ->description('Menunggu persetujuan')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning'),
                Stat::make('Permintaan Disetujui', Request::where('status', 'approved')->count())
                    ->description('Bulan ini')
                    ->descriptionIcon('heroicon-m-check-circle'),
                Stat::make('Permintaan Ditolak', Request::where('status', 'rejected')->count())
                    ->description('Bulan ini')
                    ->descriptionIcon('heroicon-m-x-circle'),
            ];
        }

        // For staff
        return [
            Stat::make('Permintaan Saya', Request::where('user_id', auth()->id())->count())
                ->description('Total permintaan Anda')
                ->descriptionIcon('heroicon-m-document-text'),
            Stat::make('Menunggu Persetujuan', 
                Request::where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->count()
            )
                ->description('Permintaan yang belum disetujui')
                ->descriptionIcon('heroicon-m-clock'),
            Stat::make('Permintaan Disetujui', 
                Request::where('user_id', auth()->id())
                    ->where('status', 'approved')
                    ->count()
            )
                ->description('Permintaan yang disetujui')
                ->descriptionIcon('heroicon-m-check-circle'),
        ];
    }
}