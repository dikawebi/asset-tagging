<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetPrintController;
use App\Models\Asset;
use App\Services\AssetStagingService;
use Illuminate\Http\Request;

// 🚀 TEMPORARY DEPLOYMENT TRIGGER ROUTE:

Route::get('/assets/print-qr/{ids}', function ($ids) {
    $assetIds = explode(',', $ids);

    $assets = Asset::whereIn('id', $assetIds)->get()
        ->sortBy(fn ($asset) => array_search($asset->id, $assetIds));

    return view('filament.forms.components.qr-print-page', compact('assets'));
})->name('asset.print-qr-bulk');

Route::get('/run-migration-safely', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate --force');
        \Illuminate\Support\Facades\Artisan::call('db:seed'); // Optional if you have seeders
        return "Database tables successfully initialized!";
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});

Route::get('/asset/print-qr/{id}', [AssetPrintController::class, 'print'])->name('asset.print-qr');

// Template CSV untuk staging registrasi aset (Kasus 2: assign berurutan ke dummy).
Route::get('/staging/template.csv', function (AssetStagingService $service) {
    return response()->streamDownload(
        fn () => print($service->templateCsv()),
        'sysinfo-template.csv',
        ['Content-Type' => 'text/csv; charset=UTF-8']
    );
})->middleware(['auth'])->name('staging.template');

// 💡 ENDPOINT API JEMBATAN TRANSLASI STRING KODE KE ID INTEGER DATABASE
Route::get('/api/get-asset-id-by-code', function (Request $request) {
    $code = $request->query('code');

    // Melakukan pencarian baris row di database PostgreSQL berdasarkan kode asset_id unik
    $asset = Asset::where('asset_id', $code)->first();

    if ($asset) {
        return response()->json([
            'success' => true,
            'id' => $asset->id // Mengembalikan ID primer urutan database (Contoh: 52)
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Data inventaris barang tidak ditemukan'
    ]);
})->middleware(['web', 'auth']); // Keamanan mutlak: Hanya akun login yang bisa melakukan scanning

Route::get('/', function () {
    return view('welcome');


});

// Galeri preview varian branding + login (sementara, hapus sebelum production)
Route::get('/preview-branding', function () {
    return view('brand-preview');
})->name('brand.preview');

// Galeri 4 konsep login, layout split tetap (sementara, hapus sebelum production)
Route::get('/preview-login', function () {
    return view('login-concepts');
})->name('brand.login-concepts');
