<?php

namespace App\Filament\Resources\Stagings\Pages;

use App\Filament\Resources\Stagings\StagingBatchResource;
use App\Models\StagingBatch;
use App\Services\AssetStagingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateStagingBatch extends CreateRecord
{
    protected static string $resource = StagingBatchResource::class;

    protected string $uploadedPath = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->uploadedPath = (string) ($data['csv_file'] ?? '');

        if ($this->uploadedPath === ''
            || ! in_array(mb_strtolower(pathinfo($this->uploadedPath, PATHINFO_EXTENSION)), ['csv', 'txt'], true)) {
            throw ValidationException::withMessages([
                'csv_file' => 'File harus berekstensi .csv (atau .txt berisi CSV).',
            ]);
        }

        unset($data['csv_file']);

        $data['file_name'] = basename($this->uploadedPath);
        $data['uploaded_by'] = Auth::id();
        $data['status'] = StagingBatch::STATUS_UPLOADED;

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            app(AssetStagingService::class)->parseUpload($this->record, $this->uploadedPath);
        } catch (ValidationException $e) {
            // File tidak valid: hapus batch kosong agar tidak menggantung.
            $this->record->delete();

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
