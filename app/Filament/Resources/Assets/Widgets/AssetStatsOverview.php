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
            Stat::make('Total Assets', Asset::count())
                ->description('All registered assets')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->icon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart($this->monthlyTrend()),

            Stat::make('In Use', Asset::where('status', 'In use')->count())
                ->description('Items currently in use')
                ->icon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Idle', Asset::where('status', 'Idle')->count())
                ->description('Available / standby items')
                ->icon('heroicon-m-pause-circle')
                ->color('info'),

            Stat::make('Repair', Asset::where('status', 'Repair')->count())
                ->description('Currently under repair')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Broke', Asset::where('status', 'Broke')->count())
                ->description('Broken condition')
                ->icon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Lost', Asset::where('status', 'Lost')->count())
                ->description('Lost assets')
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
