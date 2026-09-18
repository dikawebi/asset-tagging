<?php

namespace App\Filament\Resources\Stagings;

use App\Filament\Resources\Stagings\Pages\CreateStagingBatch;
use App\Filament\Resources\Stagings\Pages\ListStagingBatches;
use App\Filament\Resources\Stagings\Pages\ViewStagingBatch;
use App\Filament\Resources\Stagings\RelationManagers\StagingRowsRelationManager;
use App\Models\StagingBatch;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StagingBatchResource extends Resource
{
    protected static ?string $model = StagingBatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static ?string $navigationLabel = 'Staging Registrasi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Batch')
                ->placeholder('Contoh: Collect BUA - Sep 2026')
                ->required()
                ->maxLength(255),
            FileUpload::make('csv_file')
                ->label('File CSV Sysinfo')
                ->helperText('Header Inggris atau Indonesia (mis. Nomor Seri, Merk, Tipe/Model). Kolom lain jadi info tambahan. Maks 5 MB.')
                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/octet-stream', 'text/x-csv'])
                ->maxSize(5120)
                ->directory('staging-uploads')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name')->label('Nama Batch'),
            TextEntry::make('file_name')->label('File')->placeholder('-'),
            TextEntry::make('location.name')->label('Lokasi')->placeholder('-'),
            TextEntry::make('department.name')->label('Departemen')->placeholder('-'),
            TextEntry::make('category.name')->label('Kategori')->placeholder('Ikut dummy'),
            TextEntry::make('user_name')->label('Pemegang')->placeholder('-'),
            TextEntry::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    StagingBatch::STATUS_COMMITTED => 'success',
                    StagingBatch::STATUS_VALIDATED => 'warning',
                    default => 'gray',
                }),
            TextEntry::make('total_rows')->label('Total Baris'),
            TextEntry::make('valid_rows')->label('Baris Valid'),
            TextEntry::make('assigned_rows')->label('Baris Ter-assign'),
            TextEntry::make('uploader.name')->label('Di-upload Oleh')->placeholder('-'),
            TextEntry::make('committed_at')->label('Waktu Commit')->dateTime()->placeholder('-'),
        ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Batch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        StagingBatch::STATUS_COMMITTED => 'success',
                        StagingBatch::STATUS_VALIDATED => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('total_rows')->label('Total')->sortable(),
                TextColumn::make('valid_rows')->label('Valid')->sortable(),
                TextColumn::make('assigned_rows')->label('Assigned')->sortable(),
                TextColumn::make('uploader.name')->label('Uploader')->placeholder('-'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (StagingBatch $record): bool => ! $record->isCommitted()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStagingBatches::route('/'),
            'create' => CreateStagingBatch::route('/create'),
            'view' => ViewStagingBatch::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            StagingRowsRelationManager::class,
        ];
    }
}
