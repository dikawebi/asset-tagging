<?php

namespace Tests\Feature\Staging;

use App\Filament\Resources\Stagings\Pages\ViewStagingBatch;
use App\Filament\Resources\Stagings\RelationManagers\StagingRowsRelationManager;
use App\Models\Asset;
use App\Models\AssetSequence;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\StagingBatch;
use App\Models\StagingRow;
use App\Models\User;
use App\Services\AssetStagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StagingAutoRefreshTest extends TestCase
{
    use RefreshDatabase;

    private StagingBatch $batch;

    private StagingRow $row;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::create(['name' => 'IT']);
        $location = Location::create(['name' => 'Site']);
        Category::create(['name' => 'Laptop']);
        Brand::create(['name' => 'Lenovo']);

        AssetSequence::create([
            'department_id' => $department->id,
            'prefix' => 'IT',
            'format' => '{prefix}-{year}-{sequence}',
            'next_value' => 1,
            'padding' => 4,
        ]);

        $user = User::create([
            'name' => 'IT Staff',
            'email' => 'it@example.com',
            'password' => 'password',
            'department_id' => $department->id,
        ]);
        $this->actingAs($user);

        $this->batch = StagingBatch::create([
            'name' => 'Refresh Batch',
            'status' => StagingBatch::STATUS_VALIDATED,
            'total_rows' => 1,
            'valid_rows' => 0,
            'assigned_rows' => 0,
            'location_id' => $location->id,
            'department_id' => $department->id,
        ]);

        $this->row = $this->batch->rows()->create([
            'row_number' => 2,
            'data' => [
                'serial_number' => 'SN001',
                'model' => 'ThinkPad T14',
                'brand' => 'Lenpvo',
                'brand_id' => null,
                'processor' => '',
                'memory' => '',
                'storage' => '',
                'location' => $location->name,
                'location_id' => $location->id,
                'department' => $department->name,
                'department_id' => $department->id,
                'category' => '',
                'category_id' => null,
                'user_name' => '',
                'extras' => [],
            ],
            'status' => StagingRow::STATUS_INVALID,
            'error_message' => "brand 'Lenpvo' tidak dikenal",
        ]);
    }

    public function test_commit_button_appears_after_event_without_manual_reload(): void
    {
        $page = Livewire::test(ViewStagingBatch::class, ['record' => $this->batch->getRouteKey()]);

        // Kondisi awal: belum ada baris valid -> tombol Commit Assign tidak ada.
        $page->assertDontSee('Commit Assign');

        // Perbaiki baris seperti edit inline di tabel preview.
        app(AssetStagingService::class)->updateRowAssignment($this->row, ['brand' => 'Lenovo']);
        $this->assertSame('valid', $this->row->refresh()->status);

        // Event yang dikirim relation manager me-render ulang halaman.
        $page->dispatch('staging-row-saved');

        $page->assertSee('Commit Assign');
    }

    public function test_inline_edit_dispatches_parent_refresh_event(): void
    {
        Livewire::test(StagingRowsRelationManager::class, [
            'ownerRecord' => $this->batch,
            'pageClass' => ViewStagingBatch::class,
        ])
            ->call('updateTableColumnState', 'row_brand', (string) $this->row->getKey(), 'Lenovo')
            ->assertDispatched('staging-row-saved')
            ->assertHasNoErrors();

        $this->assertSame('valid', $this->row->refresh()->status);
        $this->assertSame(1, $this->batch->refresh()->valid_rows);
    }

    private function makeDummy(): Asset
    {
        return Asset::create([
            'name' => '-',
            'category_id' => Category::first()->id,
            'location_id' => Location::first()->id,
            'department_id' => Department::first()->id,
            'status' => 'Idle',
        ]);
    }

    public function test_print_qr_button_appears_right_after_commit(): void
    {
        app(AssetStagingService::class)->updateRowAssignment($this->row, ['brand' => 'Lenovo']);
        $this->makeDummy();

        Livewire::test(ViewStagingBatch::class, ['record' => $this->batch->getRouteKey()])
            ->assertDontSee('Cetak QR')
            ->callAction('commit')
            ->assertHasNoErrors()
            ->assertSee('Cetak QR');
    }

    public function test_rows_table_shows_assigned_assets_after_commit_event(): void
    {
        app(AssetStagingService::class)->updateRowAssignment($this->row, ['brand' => 'Lenovo']);
        $dummy = $this->makeDummy();
        $assetId = $dummy->asset_id;
        $this->assertNotNull($assetId);

        // Mount saat baris masih valid (simulasi tabel yang sedang dibuka user).
        $table = Livewire::test(StagingRowsRelationManager::class, [
            'ownerRecord' => $this->batch,
            'pageClass' => ViewStagingBatch::class,
        ]);
        $table->assertDontSee($assetId);

        // Commit terjadi (via tombol di halaman induk), lalu event dikirim.
        app(AssetStagingService::class)->commit($this->batch->refresh());

        $table->dispatch('staging-committed')->assertSee($assetId);
    }
}
