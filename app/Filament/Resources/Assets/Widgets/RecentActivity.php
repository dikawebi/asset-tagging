<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\AssetHistory;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivity extends TableWidget
{
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Aktivitas Terakhir')
            ->description('Mutasi dan pemindaian aset terbaru')
            ->query(
                AssetHistory::query()->with('asset')->latest()->limit(7)
            )
            ->columns([
                TextColumn::make('asset.asset_id')
                    ->label('ID Aset')
                    ->weight('bold')
                    ->url(fn (AssetHistory $record): string => AssetResource::getUrl('view', ['record' => $record->asset_id])),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->placeholder('Mutasi lokasi / departemen'),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
            ])
            ->paginated(false);
    }
}
