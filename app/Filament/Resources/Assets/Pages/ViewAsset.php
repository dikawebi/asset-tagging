<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected static ?string $title = 'Detail Aset';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_qr')
                ->label('Cetak QR')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (Asset $record): string => route('asset.print-qr', $record->id))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
