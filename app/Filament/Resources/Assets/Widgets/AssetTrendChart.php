<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AssetTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Aset Masuk (6 Bulan)';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->isoFormat('MMM YYYY');
            $data[] = Asset::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Aset baru',
                    'data' => $data,
                    'borderColor' => '#0066ff',
                    'backgroundColor' => 'rgba(0, 102, 255, 0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
