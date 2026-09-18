<?php

namespace App\Filament\Resources\Stagings\Pages;

use App\Filament\Resources\Stagings\StagingBatchResource;
use App\Services\AssetStagingService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ListStagingBatches extends ListRecords
{
    protected static string $resource = StagingBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template')
                ->label('Unduh Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('staging.template'))
                ->openUrlInNewTab(),
            Action::make('generate_dummies')
                ->label('Buat Dummy')
                ->icon('heroicon-o-plus-circle')
                ->color('info')
                ->schema([
                    TextInput::make('count')
                        ->label('Jumlah Dummy')
                        ->helperText('Dibuat untuk departemen Anda, ID mengikuti sequence departemen.')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(500)
                        ->default(50),
                ])
                ->action(function (array $data): void {
                    try {
                        $created = app(AssetStagingService::class)
                            ->generateDummies(Auth::user(), (int) $data['count']);

                        Notification::make()
                            ->success()
                            ->title("{$created} dummy berhasil dibuat")
                            ->send();
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal membuat dummy')
                            ->body(collect($e->errors())->flatten()->implode(' '))
                            ->send();
                    }
                }),
            CreateAction::make()->label('Upload CSV Baru'),
        ];
    }
}
