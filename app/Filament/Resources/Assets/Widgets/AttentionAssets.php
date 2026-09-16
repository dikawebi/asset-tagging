<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AttentionAssets extends TableWidget
{
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Perlu Perhatian')
            ->description('Aset dalam perbaikan, rusak, atau hilang')
            ->query(
                Asset::query()
                    ->whereIn('status', ['Repair', 'Broke', 'Lost'])
                    ->latest()
                    ->limit(7)
            )
            ->columns([
                TextColumn::make('asset_id')
                    ->label('ID')
                    ->weight('bold')
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record])),
                TextColumn::make('name')
                    ->label('Nama')
                    ->limit(28),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Repair' => 'warning',
                        'Broke' => 'danger',
                        'Lost' => 'gray',
                        default => 'info',
                    }),
            ])
            ->paginated(false);
    }
}
