<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AssetStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Aset', Asset::count())
                ->description('Seluruh aset terdaftar')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->icon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart($this->monthlyTrend()),

            Stat::make('Dipakai', Asset::where('status', 'In use')->count())
                ->description('Barang sedang dipakai')
                ->icon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Siaga', Asset::where('status', 'Idle')->count())
                ->description('Barang tersedia / standby')
                ->icon('heroicon-m-pause-circle')
                ->color('info'),

            Stat::make('Perbaikan', Asset::where('status', 'Repair')->count())
                ->description('Sedang dalam perbaikan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Rusak', Asset::where('status', 'Broke')->count())
                ->description('Kondisi rusak')
                ->icon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Hilang', Asset::where('status', 'Lost')->count())
                ->description('Aset hilang')
                ->icon('heroicon-m-question-mark-circle')
                ->color('gray'),
        ];
    }

    /**
     * @return array<int>
     */
    protected function monthlyTrend(): array
    {
        $trend = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $trend[] = Asset::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return $trend;
    }
}
