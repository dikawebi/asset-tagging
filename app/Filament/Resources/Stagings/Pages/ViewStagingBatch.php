<?php

namespace App\Filament\Resources\Stagings\Pages;

use App\Filament\Resources\Stagings\StagingBatchResource;
use App\Models\StagingBatch;
use App\Services\AssetStagingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;

class ViewStagingBatch extends ViewRecord
{
    protected static string $resource = StagingBatchResource::class;

    protected static ?string $title = 'Preview Staging';

    /**
     * Konten full-width: tabel 14 kolom butuh ruang agar tidak scroll
     * horizontal.
     */
    protected Width | string | null $maxContentWidth = Width::Full;

    /**
     * Dipicu setiap ada edit inline di tabel baris: render ulang halaman
     * agar tombol Commit Assign + ringkasan (valid/total) selalu mutakhir
     * tanpa reload manual.
     */
    #[On('staging-row-saved')]
    public function refreshAfterRowSaved(): void
    {
        // Kosong disengaja: menangani event saja sudah me-render ulang.
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('commit')
                ->label('Commit Assign')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Commit batch ini?')
                ->modalDescription(fn (StagingBatch $record): string => "Assign {$record->valid_rows} baris valid ke dummy kosong secara berurutan. "
                    .'Lokasi, departemen, dan pemegang dicatat via riwayat. Proses ini transaksional (batal semua jika gagal).')
                ->visible(fn (StagingBatch $record): bool => ! $record->isCommitted() && $record->valid_rows > 0)
                ->action(function (StagingBatch $record, $livewire): void {
                    try {
                        $result = app(AssetStagingService::class)->commit($record);

                        Notification::make()
                            ->success()
                            ->title("{$result['count']} aset berhasil di-assign")
                            ->body('Cetak QR lalu tempel ke perangkat via tombol Cetak QR.')
                            ->send();

                        // Segarkan tabel baris agar nomor aset ter-assign +
                        // tombol Cetak QR langsung tampil tanpa reload manual.
                        $livewire->dispatch('staging-committed');
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Commit gagal')
                            ->body(collect($e->errors())->flatten()->implode(' '))
                            ->send();
                    }
                }),
            Action::make('print_qr')
                ->label('Cetak QR')
                ->icon('heroicon-o-qr-code')
                ->color('info')
                ->visible(fn (StagingBatch $record): bool => $record->assigned_rows > 0)
                ->url(fn (StagingBatch $record): string => route('asset.print-qr-bulk', [
                    'ids' => $record->rows()->whereNotNull('asset_id')->pluck('asset_id')->implode(','),
                ]))
                ->openUrlInNewTab(),
            DeleteAction::make()
                ->visible(fn (StagingBatch $record): bool => ! $record->isCommitted()),
        ];
    }
}
