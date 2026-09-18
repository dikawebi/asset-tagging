<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\{CreateAsset, EditAsset, ListAssets, ViewAsset};
use App\Models\{Asset, AssetSequence};
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Section, Grid};
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\{TextInput, Select, FileUpload, ViewField};
use Filament\Resources\Resource;
use Filament\Tables\{Table, Tables};
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\{ViewAction, EditAction, DeleteAction, ButtonAction, BulkAction, DeleteBulkAction};
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
// Tambahkan tanda '\' di depan semua import Filament

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'asset_id';

    /**
     * 1. SKEMA FORM (CREATE & EDIT)
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    TextInput::make('asset_id')
                        ->label('ID Aset')
                        ->default(function () {
                            $user = Auth::user();
                            $setting = AssetSequence::where('department_id', $user->department_id)->first();

                            if (!$setting) return 'Menunggu pengaturan...';

                            // Logika untuk menampilkan format (sama dengan preview)
                            $sequenceString = str_pad($setting->next_value, $setting->padding, '0', STR_PAD_LEFT);
                            return str_replace(['{prefix}', '{year}', '{sequence}'], [$setting->prefix, date('Y'), $sequenceString], $setting->format);
                        })
                        ->disabled() // User tidak boleh ganti manual
                        ->dehydrated(true), // Tetap kirim ke database
                    Placeholder::make('sequence_warning')
                        ->label('')
                        ->content(fn () => AssetSequence::where('department_id', Auth::user()->department_id)->exists()
                            ? '✅ Sequence siap.' : '⚠️ Perhatian: Sequence belum diatur.')
                        ->hiddenOn('view')
                        ->columnSpanFull(),

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->label('Kategori Aset'),

                    Select::make('brand_id')
                        ->relationship('brand', 'name')
                        ->required()
                        ->label('Brand'),

                    TextInput::make('name')
                        ->required()
                        ->label('Tipe / Seri'),

                    TextInput::make('serial_number')
                        ->required()
                        ->label('Serial Number'),

                    Select::make('status')
                        ->label('Status')
                        ->options(['In use' => 'In Use', 'Idle' => 'Idle', 'Repair' => 'Repair', 'Broke' => 'Broke', 'Lost' => 'Lost'])
                        ->required(),
                ]),

            Section::make('Lokasi & Kepemilikan')
                ->columns(2)
                ->schema([
                    TextInput::make('pr_number')->label('Nomor PR'),
                    TextInput::make('po_number')->label('Nomor PO'),
                    Select::make('location_id')
                        ->relationship('location', 'name')
                        ->label('Lokasi')
                        ->required()
                        ->disabledOn('edit')
                        ->helperText('Terkunci. Ubah hanya via Catat Perpindahan Baru pada tab Riwayat.'),
                    Select::make('department_id')
                        ->relationship('department', 'name')
                        ->label('Departemen')
                        ->required()
                        ->disabledOn('edit')
                        ->helperText('Terkunci. Ubah hanya via Catat Perpindahan Baru pada tab Riwayat.'),
                    TextInput::make('user_name')
                        ->label('Pemegang')
                        ->columnSpanFull()
                        ->required()
                        ->disabledOn('edit')
                        ->helperText('Terkunci. Ubah hanya via Catat Perpindahan Baru pada tab Riwayat.'),
                ]),
Section::make('Dokumentasi Foto Aset')
    ->schema([
        // Gunakan ViewField untuk menyisipkan input murni
        ViewField::make('images')
            ->view('filament.forms.components.custom-mobile-camera')
            ->label('Ambil Foto Aset')
            ->helperText('Klik tombol di bawah untuk membuka kamera HP.')
            ->hiddenOn('view'),
        // Tampilan read-only khusus halaman view (tata letak sama seperti form edit).
        // content() mengembalikan View (Htmlable) agar HTML tidak disanitasi.
        Placeholder::make('images_gallery')
            ->label('Foto Fisik Aset')
            ->content(fn (Get $get) => view('filament.forms.components.asset-images-view', [
                'images' => $get('images'),
            ]))
            ->html()
            ->visibleOn('view')
            ->columnSpanFull(),
    ]),
Section::make('Label QR Code')
    ->visibleOn('view')
    ->schema([
        ViewField::make('qr_preview')
            ->view('filament.forms.components.qr-preview')
            ->label('QR Code Aset')
            ->columnSpanFull(),
    ]),
        ]);
    }



    /**
     * 2. SKEMA INFOLIST (VIEW DETAIL)
     *
     * Sengaja tidak didefinisikan: halaman view memakai skema form yang sama
     * seperti halaman edit, dan framework otomatis me-render-nya dalam
     * keadaan disabled (view-only). Komponen yang hanya relevan untuk
     * create/edit disembunyikan via ->hiddenOn('view'), sedangkan komponen
     * khusus view ditandai ->visibleOn('view').
     */


     // 3. TABLE SCHEMA

    public static function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('asset_id')->label('ID Aset')->searchable()->sortable(),
            TextColumn::make('brand.name')->label('Brand / Merek')->searchable()->sortable(),
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('category.name')->label('Kategori')->searchable()->sortable(),
            TextColumn::make('location.name')->label('Lokasi')->searchable()->sortable(),
            TextColumn::make('department.name')->label('Dept')->searchable()->sortable(),
            TextColumn::make('user_name')->label('Pengguna')->searchable()->sortable(),
            TextColumn::make('status')
            ->label('Status')
            ->sortable()
            ->searchable()
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
              }),
                  ])
                  ->filters([
                    \Filament\Tables\Filters\SelectFilter::make('status')
                        ->label('Status')
                        ->options(['In use' => 'In Use', 'Idle' => 'Idle', 'Repair' => 'Repair', 'Broke' => 'Broke', 'Lost' => 'Lost'])
                        ->placeholder('All statuses'),
                    \Filament\Tables\Filters\SelectFilter::make('category')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->placeholder('Semua kategori'),
                    \Filament\Tables\Filters\SelectFilter::make('location')
                        ->label('Lokasi')
                        ->relationship('location', 'name')
                        ->placeholder('Semua lokasi'),
                    \Filament\Tables\Filters\SelectFilter::make('department')
                        ->label('Departemen')
                        ->relationship('department', 'name')
                        ->placeholder('Semua departemen'),
                  ])
                  ->actions([
                            ViewAction::make(),
                            EditAction::make(),
                            DeleteAction::make(),
                            ButtonAction::make('print_qr')
                            ->label('Print QR')
                            ->icon('heroicon-o-printer')
                            ->color('success')
                            ->url(fn (Asset $record): string => route('asset.print-qr', $record->id))
                            ->openUrlInNewTab(),
                  ])
                  ->bulkActions([
                            BulkAction::make('print_qr_bulk')
                                ->label('Cetak QR Terpilih')
                                ->icon('heroicon-o-printer')
                                ->color('success')
                                ->url(fn (Collection $records): ?string => $records->isNotEmpty()
                                    ? route('asset.print-qr-bulk', ['ids' => $records->pluck('id')->implode(',')])
                                    : null)
                                ->openUrlInNewTab()
                                ->deselectRecordsAfterCompletion(),
                            DeleteBulkAction::make(),
                  ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit'   => EditAsset::route('/{record}/edit'),
            'view'   => ViewAsset::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
    return [
        \App\Filament\Resources\Assets\RelationManagers\HistoriesRelationManager::class,
    ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    return parent::getEloquentQuery()->with(['department', 'category', 'location']); // Sesuaikan dengan nama relasi Anda
}

protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
{
    return parent::getTableQuery()->cacheFor(now()->addMinutes(5));
}
}
