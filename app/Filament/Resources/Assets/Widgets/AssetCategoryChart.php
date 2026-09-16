<?php

namespace App\Filament\Resources\Assets\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AssetCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Aset per Kategori';

    protected function getData(): array
    {
        $rows = DB::table('assets')
            ->leftJoin('categories', 'assets.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa kategori') as name, COUNT(assets.id) as total")
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah aset',
                    'data' => $rows->pluck('total')->toArray(),
                    'backgroundColor' => '#0066ff',
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $rows->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
