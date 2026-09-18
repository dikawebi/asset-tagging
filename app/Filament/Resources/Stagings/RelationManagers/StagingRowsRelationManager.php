<?php

namespace App\Filament\Resources\Stagings\RelationManagers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\StagingRow;
use App\Services\AssetStagingService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\On;

class StagingRowsRelationManager extends RelationManager
{
    protected static string $relationship = 'rows';

    protected static ?string $title = 'Baris CSV';

    /**
     * Dipicu setelah Commit Assign: render ulang tabel agar status
     * assigned + nomor aset langsung tampil (siap cetak QR).
     */
    #[On('staging-committed')]
    public function refreshAfterCommit(): void
    {
        // Kosong disengaja: menangani event saja sudah me-render ulang.
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        // Kolom manual: inline edit langsung di tabel, tersimpan ke data
        // baris + validasi ulang otomatis. Terkunci setelah ter-assign.
        $locked = fn (StagingRow $record): bool => $record->status === StagingRow::STATUS_ASSIGNED;

        $save = fn (StagingRow $record, string $key, mixed $state): mixed => app(AssetStagingService::class)
            ->updateRowAssignment($record, [$key => $state])
            ->data[$key] ?? null;

        // Setiap perubahan nilai kolom memicu refresh halaman induk
        // (tombol Commit Assign + ringkasan ikut mutakhir tanpa reload manual).
        $notifyParent = fn (Column $column): mixed => $column->getLivewire()->dispatch('staging-row-saved');

        $manualText = function (string $key, string $label) use ($locked, $save, $notifyParent): TextInputColumn {
            return TextInputColumn::make("row_{$key}")
                ->label($label)
                ->getStateUsing(fn (StagingRow $record): string => (string) ($record->data[$key] ?? ''))
                ->updateStateUsing(fn (StagingRow $record, $state) => $save($record, $key, trim((string) $state)))
                ->afterStateUpdated($notifyParent)
                ->disabled($locked);
        };

        $manualSelect = function (string $key, string $label, string $modelClass) use ($locked, $save, $notifyParent): SelectColumn {
            return SelectColumn::make("row_{$key}")
                ->label($label)
                ->options($modelClass::pluck('name', 'id'))
                ->searchableOptions()
                ->getStateUsing(fn (StagingRow $record) => $record->data[$key] ?? null)
                ->updateStateUsing(fn (StagingRow $record, $state) => $save(
                    $record,
                    $key,
                    $state !== null && $state !== '' ? (int) $state : null
                ))
                ->afterStateUpdated($notifyParent)
                ->disabled($locked);
        };

        return $table
            ->columns([
                TextColumn::make('row_number')->label('Baris')->sortable(),
                $manualText('serial_number', 'Serial'),
                $manualText('model', 'Model'),
                $manualText('brand', 'Brand'),
                $manualText('processor', 'Prosesor'),
                $manualText('memory', 'Memori'),
                $manualText('storage', 'Storage'),
                $manualSelect('location_id', 'Lokasi', Location::class),
                $manualSelect('department_id', 'Departemen', Department::class),
                $manualSelect('category_id', 'Kategori', Category::class),
                $manualText('user_name', 'Pemegang'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        StagingRow::STATUS_ASSIGNED => 'success',
                        StagingRow::STATUS_VALID => 'info',
                        StagingRow::STATUS_INVALID => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('error_message')
                    ->label('Error')
                    ->placeholder('-')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('asset.asset_id')
                    ->label('Asset ID')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        StagingRow::STATUS_VALID => 'Valid',
                        StagingRow::STATUS_INVALID => 'Invalid',
                        StagingRow::STATUS_ASSIGNED => 'Assigned',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Action::make('add_brand')
                    ->label('Tambah ke master')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->visible(function (StagingRow $record): bool {
                        if ($record->status !== StagingRow::STATUS_INVALID) {
                            return false;
                        }

                        $brand = trim((string) ($record->data['brand'] ?? ''));

                        return $brand !== ''
                            && str_contains((string) $record->error_message, 'tidak dikenal');
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (StagingRow $record): string => "Tambahkan brand '".trim((string) $record->data['brand'])."' ke master?")
                    ->modalDescription('Brand baru langsung dipakai untuk validasi ulang baris ini.')
                    ->modalSubmitActionLabel('Ya, tambahkan')
                    ->action(function (StagingRow $record): void {
                        $brand = trim((string) ($record->data['brand'] ?? ''));

                        app(AssetStagingService::class)->createBrandFromRow($record);

                        Notification::make()
                            ->success()
                            ->title("Brand '{$brand}' ditambahkan ke master")
                            ->body('Baris ini sudah divalidasi ulang otomatis.')
                            ->send();
                    })
                    ->after(fn ($livewire) => $livewire->dispatch('staging-row-saved')),
            ]);
    }
}
