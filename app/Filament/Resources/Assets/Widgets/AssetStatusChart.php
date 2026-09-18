<?php

namespace App\Filament\Resources\Assets\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Asset;

class AssetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Asset Status';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Number of assets',
                    'data' => [
                        Asset::where('status', 'In use')->count(),
                        Asset::where('status', 'Idle')->count(),
                        Asset::where('status', 'Repair')->count(),
                        Asset::where('status', 'Broke')->count(),
                        Asset::where('status', 'Lost')->count(),
                    ],
                    'backgroundColor' => ['#16a34a', '#0284c7', '#f59e0b', '#ef4444', '#64748b'],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => ['In Use', 'Idle', 'Repair', 'Broke', 'Lost'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
