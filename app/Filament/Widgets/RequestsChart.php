<?php

namespace App\Filament\Widgets;

use App\Models\Request;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Permintaan ATK';

    protected function getData(): array
    {
        $data = Request::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Permintaan ATK',
                    'data' => $data->pluck('count')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin_gudang', 'pimpinan']);
    }
}