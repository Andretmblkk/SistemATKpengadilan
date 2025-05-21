<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\AtkRequest; // Ganti dari Request ke AtkRequest

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            StatsOverviewWidget\Stat::make('Total Requests', AtkRequest::count())
                ->description('Number of ATK requests submitted')
                ->descriptionIcon('heroicon-o-clipboard-document'),
            StatsOverviewWidget\Stat::make('Pending Requests', AtkRequest::where('status', 'pending')->count())
                ->description('Requests awaiting approval')
                ->descriptionIcon('heroicon-o-clock'),
            StatsOverviewWidget\Stat::make('Approved Requests', AtkRequest::where('status', 'approved')->count())
                ->description('Requests approved')
                ->descriptionIcon('heroicon-o-check-circle'),
        ];
    }
}