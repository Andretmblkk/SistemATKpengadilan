<?php

namespace App\Filament\Widgets;

use App\Models\ItemRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Requests', ItemRequest::count())
                ->description('Jumlah permintaan ATK yang diajukan')
                ->color('primary'),
            Stat::make('Pending Requests', ItemRequest::where('status', 'pending')->count())
                ->description('Permintaan menunggu persetujuan')
                ->color('warning'),
            Stat::make('Approved Requests', ItemRequest::where('status', 'approved')->count())
                ->description('Permintaan yang disetujui')
                ->color('success'),
            Stat::make('Delivered Requests', ItemRequest::where('delivery_status', 'delivered')->count())
                ->description('Permintaan yang telah diambil')
                ->color('info'),
        ];
    }
}
