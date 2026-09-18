<?php

namespace App\Filament\User\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserAssetOverview extends BaseWidget
{
    protected ?string $pollingInterval = '15s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Menghitung seluruh aset global di database
        $totalAset = Asset::count();
        $asetDigunakan = Asset::where('status', 'In use')->count();
        $brokenAssets = Asset::where('status', 'Broke')->count();

        return [
            Stat::make('Total Assets', $totalAset . ' Units')
                ->description('All assets recorded in the system')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Assets In Use', $asetDigunakan . ' Units')
                ->description('Currently in operational use')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Broken Assets', $brokenAssets . ' Units')
                ->description('Need immediate action/repair')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($brokenAssets > 0 ? 'danger' : 'gray'),
        ];
    }
}
