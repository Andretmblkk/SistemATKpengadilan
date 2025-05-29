<?php

namespace App\Filament\Widgets;

use App\Models\Request;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Requests', Request::count())
                ->description('Jumlah permintaan ATK yang diajukan')
                ->color('primary'),
            Stat::make('Pending Requests', Request::where('status', 'pending')->count())
                ->description('Permintaan menunggu persetujuan')
                ->color('warning'),
            Stat::make('Approved Requests', Request::where('status', 'approved')->count())
                ->description('Permintaan yang disetujui')
                ->color('success'),
            Stat::make('Delivered Requests', Request::where('delivery_status', 'delivered')->count())
                ->description('Permintaan yang telah diambil')
                ->color('info'),
        ];
    }
}