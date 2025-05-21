<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AtkRequest; // Ganti dari Request ke AtkRequest

class RequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Requests Chart';

    protected function getData(): array
    {
        $data = AtkRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Requests per Day',
                    'data' => $data->pluck('count'),
                ],
            ],
            'labels' => $data->pluck('date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}