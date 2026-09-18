<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Location;
use App\Models\StagingBatch;
use App\Models\User;
use App\Services\AssetStagingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data demo untuk mencoba form staging (idempotent, aman dijalankan ulang).
 * Batch dibiarkan status validated (belum di-commit) agar bisa dicoba
 * lewat UI: Staging Registrasi -> Demo Collect BUA -> Commit Assign.
 */
class StagingDemoSeeder extends Seeder
{
    public const BATCH_NAME = 'Demo Collect BUA - Sep 2026';

    public function run(): void
    {
        $csv = implode("\n", [
            'serial_number,model,brand',
            'TEST-SN-0001,ThinkPad T14 Gen 3,Lenovo',
            'TEST-SN-0002,Galaxy Tab A9,Samsung',
            'TEST-SN-0001,ThinkPad T14 Gen 3 Duplikat,Lenovo',
            'TEST-SN-0004,Laptop Fiktif,MerekFiktif',
            ',Tanpa Serial,Lenovo',
            'TEST-SN-0006,Latitude 5440,Dell',
        ]);

        Storage::disk('local')->put('staging-uploads/demo-sysinfo.csv', $csv);

        StagingBatch::where('name', self::BATCH_NAME)->each(
            fn (StagingBatch $batch) => $batch->delete()
        );

        $batch = StagingBatch::create([
            'name' => self::BATCH_NAME,
            'file_name' => 'demo-sysinfo.csv',
            'uploaded_by' => User::orderBy('id')->value('id'),
            'status' => StagingBatch::STATUS_UPLOADED,
            'location_id' => Location::whereRaw('LOWER(name) = ?', ['bp site bua'])->value('id'),
            'department_id' => Department::whereRaw('LOWER(name) = ?', ['it'])->value('id'),
            'user_name' => 'andi.pratama',
        ]);

        app(AssetStagingService::class)->parseUpload($batch, 'staging-uploads/demo-sysinfo.csv');

        $batch->refresh();
        $pool = app(AssetStagingService::class)->poolCount();

        $this->command->info("Batch demo siap: {$batch->total_rows} baris, "
            ."{$batch->valid_rows} valid. Stok dummy tersedia: {$pool}.");
    }
}
