<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_id')
                    ->label('ID Aset')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->searchable(),
                TextColumn::make('pr_number')
                    ->label('No. PR')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('po_number')
                    ->label('No. PO')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_name')
                    ->label('Pengguna')
                    ->searchable(),
                TextColumn::make('processor')
                    ->label('Prosesor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('memory')
                    ->label('Memori')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('storage')
                    ->label('Penyimpanan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'In use' => 'success',
                        'Idle' => 'info',
                        'Repair' => 'warning',
                        'Broke' => 'danger',
                        'Lost' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'In use' => 'In Use',
                        'Idle' => 'Idle',
                        'Repair' => 'Repair',
                        'Broke' => 'Broke',
                        'Lost' => 'Lost',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'In use' => 'In Use',
                        'Idle' => 'Idle',
                        'Repair' => 'Repair',
                        'Broke' => 'Broke',
                        'Lost' => 'Lost',
                    ])
                    ->placeholder('All statuses'),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->placeholder('Semua kategori'),
                SelectFilter::make('location')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->placeholder('Semua lokasi'),
                SelectFilter::make('department')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->placeholder('Semua departemen'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print_qr')
                        ->label('Cetak QR')
                        ->icon('heroicon-m-printer')
                        ->url(fn (Collection $records): ?string => $records->isNotEmpty()
                            ? route('asset.print-qr-bulk', ['ids' => $records->pluck('id')->implode(',')])
                            : null)
                        ->openUrlInNewTab()
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
