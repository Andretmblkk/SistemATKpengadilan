<?php

namespace App\Filament\Widgets;

use App\Models\Request;
use Filament\Widgets\ChartWidget;

class RequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Permintaan';

    protected function getData(): array
    {
        $statusData = Request::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $deliveryData = Request::selectRaw('delivery_status, COUNT(*) as count')
            ->groupBy('delivery_status')
            ->pluck('count', 'delivery_status')
            ->toArray();

        return [
            'labels' => ['Pending', 'Approved', 'Rejected', 'Belum Diambil', 'Sudah Diambil'],
            'datasets' => [
                [
                    'label' => 'Permintaan',
                    'data' => [
                        $statusData['pending'] ?? 0,
                        $statusData['approved'] ?? 0,
                        $statusData['rejected'] ?? 0,
                        $deliveryData['not_delivered'] ?? 0,
                        $deliveryData['delivered'] ?? 0,
                    ],
                    'backgroundColor' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280', '#3b82f6'],
                    'borderColor' => ['#d97706', '#059669', '#dc2626', '#4b5563', '#2563eb'],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}