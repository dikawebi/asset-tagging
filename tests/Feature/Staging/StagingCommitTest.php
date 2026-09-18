<?php

namespace Tests\Feature\Staging;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetSequence;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\StagingBatch;
use App\Models\User;
use App\Services\AssetStagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StagingCommitTest extends TestCase
{
    use RefreshDatabase;

    private array $masters;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::create(['name' => 'IT']);
        $location = Location::create(['name' => 'BP Site BUA']);
        $category = Category::create(['name' => 'Laptop']);
        $brand = Brand::create(['name' => 'Lenovo']);

        AssetSequence::create([
            'department_id' => $department->id,
            'prefix' => 'IT',
            'format' => '{prefix}-{year}-{sequence}',
            'next_value' => 1,
            'padding' => 4,
        ]);

        $this->user = User::create([
            'name' => 'IT Staff',
            'email' => 'it@example.com',
            'password' => 'password',
            'department_id' => $department->id,
        ]);

        Auth::login($this->user);

        $this->masters = compact('department', 'location', 'category', 'brand');
    }

    private function makeDummy(): Asset
    {
        return Asset::create([
            'name' => '-',
            'category_id' => $this->masters['category']->id,
            'location_id' => $this->masters['location']->id,
            'department_id' => $this->masters['department']->id,
            'status' => 'Idle',
        ]);
    }

    private function putCsv(string $name, string $content): string
    {
        Storage::fake('local');
        Storage::disk('local')->put($name, $content);

        return $name;
    }

    private function makeBatch(array $overrides = []): StagingBatch
    {
        return StagingBatch::create(array_merge([
            'name' => 'Test Batch',
            'uploaded_by' => $this->user->id,
            'status' => StagingBatch::STATUS_UPLOADED,
            'location_id' => $this->masters['location']->id,
            'department_id' => $this->masters['department']->id,
        ], $overrides));
    }

    public function test_parse_validates_rows_and_reports_errors()
    {
        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,lenovo',
            'SN001,ThinkPad T15,Lenovo',
            'SN003,ThinkPad X1,MerekFiktif',
            ',Tanpa Serial,Lenovo',
        ]);

        $batch = $this->makeBatch();
        app(AssetStagingService::class)->parseUpload($batch, $this->putCsv('a.csv', $csv));

        $batch->refresh();

        $this->assertSame(4, $batch->total_rows);
        $this->assertSame(1, $batch->valid_rows);
        $this->assertSame(StagingBatch::STATUS_VALIDATED, $batch->status);

        $statuses = $batch->rows()->orderBy('row_number')->pluck('status')->all();
        $this->assertSame(['valid', 'invalid', 'invalid', 'invalid'], $statuses);
    }

    public function test_commit_assigns_sequentially_and_writes_history()
    {
        $dummy1 = $this->makeDummy();
        $dummy2 = $this->makeDummy();
        $dummy3 = $this->makeDummy();

        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,Lenovo',
            'SN002,Ideapad Slim 3,Lenovo',
        ]);

        $service = app(AssetStagingService::class);
        // Tanpa user_name batch: pemegang tetap null (history pakai '-').
        $batch = $this->makeBatch(['user_name' => null]);
        $service->parseUpload($batch, $this->putCsv('b.csv', $csv));

        $result = $service->commit($batch);

        $this->assertSame(2, $result['count']);

        // FIFO: baris 1 -> dummy terkecil.
        $this->assertSame([$dummy1->id, $dummy2->id], $result['asset_ids']);

        $first = Asset::find($dummy1->id);
        $this->assertSame('SN001', $first->serial_number);
        $this->assertSame('ThinkPad T14', $first->name);
        $this->assertSame('In use', $first->status);
        $this->assertSame($this->masters['location']->id, $first->location_id);
        $this->assertSame($this->masters['department']->id, $first->department_id);
        $this->assertNull($first->user_name);

        $second = Asset::find($dummy2->id);
        $this->assertSame('SN002', $second->serial_number);
        $this->assertNull($second->user_name);

        // Riwayat tercatat untuk keduanya.
        $this->assertSame(2, AssetHistory::count());
        $this->assertStringContainsString(
            "staging batch #{$batch->id}",
            AssetHistory::orderBy('id')->first()->keterangan
        );

        // Dummy ketiga tidak tersentuh.
        $this->assertNull(Asset::find($dummy3->id)->serial_number);

        $batch->refresh();
        $this->assertSame(StagingBatch::STATUS_COMMITTED, $batch->status);
        $this->assertSame(2, $batch->assigned_rows);
        $this->assertNotNull($batch->committed_at);
    }

    public function test_commit_uses_batch_holder_for_all_rows()
    {
        $this->makeDummy();

        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,Lenovo',
        ]);

        $service = app(AssetStagingService::class);
        $batch = $this->makeBatch(['user_name' => 'andi.pratama']);
        $service->parseUpload($batch, $this->putCsv('d.csv', $csv));

        $service->commit($batch);

        $asset = Asset::where('serial_number', 'SN001')->first();
        $this->assertSame('andi.pratama', $asset->user_name);
        $this->assertSame('andi.pratama', AssetHistory::first()->user_baru);
    }

    public function test_manual_fill_overrides_row_and_commit_uses_it()
    {
        $this->makeDummy();
        $surabaya = Location::create(['name' => 'Kantor Surabaya']);

        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,Lenovo',
        ]);

        $service = app(AssetStagingService::class);
        $batch = $this->makeBatch();
        $service->parseUpload($batch, $this->putCsv('e.csv', $csv));

        $row = $batch->rows()->first();

        // Prefill awal mengikuti batch.
        $this->assertSame($this->masters['location']->id, $row->data['location_id']);

        $service->updateRowAssignment($row, [
            'location_id' => $surabaya->id,
            'department_id' => $this->masters['department']->id,
            'category_id' => null,
            'user_name' => 'siti',
        ]);

        $this->assertSame('valid', $row->refresh()->status);
        $this->assertSame('Kantor Surabaya', $row->refresh()->data['location']);

        $service->commit($batch);

        $asset = Asset::where('serial_number', 'SN001')->first();
        $this->assertSame($surabaya->id, $asset->location_id);
        $this->assertSame('siti', $asset->user_name);
    }

    public function test_template_csv_download()
    {
        $response = $this->actingAs($this->user)->get(route('staging.template'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringStartsWith(
            'serial_number,model,brand',
            str_replace("\r\n", "\n", $response->streamedContent() ?? $response->getContent())
        );
    }

    public function test_editing_failed_fields_revalidates_row()
    {
        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,Lenovo',
            'SN001,ThinkPad T15,Lenovo',
            'SN003,ThinkPad X1,MerekFiktif',
        ]);

        $service = app(AssetStagingService::class);
        $batch = $this->makeBatch();
        $service->parseUpload($batch, $this->putCsv('f.csv', $csv));

        $this->assertSame(1, $batch->refresh()->valid_rows);

        // Perbaiki serial duplikat -> valid.
        $dupRow = $batch->rows()->where('row_number', 3)->first();
        $service->updateRowAssignment($dupRow, ['serial_number' => 'SN002']);
        $this->assertSame('valid', $dupRow->refresh()->status);

        // Perbaiki brand salah ketik -> valid + brand_id terisi.
        $brandRow = $batch->rows()->where('row_number', 4)->first();
        $service->updateRowAssignment($brandRow, ['brand' => 'Lenovo']);
        $brandRow->refresh();
        $this->assertSame('valid', $brandRow->status);
        $this->assertSame($this->masters['brand']->id, $brandRow->data['brand_id']);

        // Counter batch ikut terhitung ulang.
        $this->assertSame(3, $batch->refresh()->valid_rows);
    }

    public function test_parse_accepts_indonesian_headers_and_keeps_extras()
    {
        $asus = Brand::create(['name' => 'Asus']);

        $csv = "\xEF\xBB\xBF".implode("\n", [
            '"Nama Perangkat","Merk","Tipe/Model","Nomor Seri","Sistem Operasi","Memori RAM"',
            '"LAPTOP-LSTK1LV3","ASUS","ZenBook UX433FN","K8N0CV13J45935E","Microsoft Windows 11","16.00 GB"',
        ]);

        $service = app(AssetStagingService::class);
        $batch = $this->makeBatch();
        $service->parseUpload($batch, $this->putCsv('g.csv', $csv));

        $batch->refresh();
        $this->assertSame(1, $batch->total_rows);
        $this->assertSame(1, $batch->valid_rows);

        $data = $batch->rows()->first()->data;
        $this->assertSame('K8N0CV13J45935E', $data['serial_number']);
        $this->assertSame('ZenBook UX433FN', $data['model']);
        $this->assertSame($asus->id, $data['brand_id']);
        $this->assertSame('LAPTOP-LSTK1LV3', $data['extras']['Nama Perangkat']);
        $this->assertSame('16.00 GB', $data['extras']['Memori RAM']);
    }

    public function test_commit_aborts_when_pool_is_short()
    {
        $this->makeDummy();

        $csv = implode("\n", [
            'serial_number,model,brand',
            'SN001,ThinkPad T14,Lenovo',
            'SN002,Ideapad Slim 3,Lenovo',
        ]);

        $service = app(AssetStagingService::class);
        $batch = $this->makeBatch();
        $service->parseUpload($batch, $this->putCsv('c.csv', $csv));

        try {
            $service->commit($batch);
            $this->fail('Commit seharusnya gagal karena stok dummy kurang.');
        } catch (ValidationException) {
            // All-or-nothing: tidak ada yang berubah.
            $this->assertSame(0, AssetHistory::count());
            $this->assertSame(0, Asset::whereNotNull('serial_number')->count());
            $this->assertSame(0, $batch->refresh()->assigned_rows);
            $this->assertSame(StagingBatch::STATUS_VALIDATED, $batch->refresh()->status);
        }
    }
}
