<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\StagingBatch;
use App\Models\StagingRow;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Flow registrasi aset Kasus 2 (stiker QR belum tertempel):
 *
 * 1. Tim IT collect sysinfo -> CSV ramping (serial, model, brand).
 * 2. CSV di-upload + lokasi/departemen/kategori/pemegang diisi manual
 *    per batch di form -> parseUpload() menyimpan tiap baris + validasi.
 * 3. User preview -> commit() assign berurutan ke pool dummy
 *    (status Idle + serial NULL, ID terkecil dulu), all-or-nothing.
 * 4. Setelah commit, cetak QR via route asset.print-qr-bulk.
 *
 * Satu batch = satu kelompok (lokasi & departemen sama untuk semua baris).
 * Perubahan location/department/user ditulis lewat AssetHistory agar lolos
 * AssetObserver (field tersebut dikunci untuk update langsung).
 */
class AssetStagingService
{
    /**
     * Kolom CSV (ramping): hanya data perangkat. Lokasi, departemen,
     * kategori, dan pemegang diisi manual per batch di form.
     */
    public const HEADERS = [
        'serial_number',
        'model',
        'brand',
        'processor',
        'memory',
        'storage',
    ];

    public const REQUIRED_HEADERS = [
        'serial_number',
        'model',
    ];

    /**
     * Alias header yang diterima (Indonesia maupun Inggris). Dibandingkan
     * dalam bentuk ringkas: huruf kecil, tanpa spasi/garis miring/underscore.
     * Contoh asli: "Nomor Seri", "Tipe/Model", "Merk".
     */
    private const HEADER_ALIASES = [
        'serial_number' => ['serialnumber', 'nomorseri', 'serial', 'sn', 'servicetag'],
        'model' => ['model', 'tipe', 'tipemodel', 'type', 'product'],
        'brand' => ['brand', 'merk', 'merek', 'manufacturer', 'pabrikan'],
        'processor' => ['processor', 'prosesor', 'cpu', 'chipset'],
        'memory' => ['memory', 'memori', 'ram', 'memoriram'],
        'storage' => ['storage', 'penyimpanan', 'disk', 'harddisk', 'ssd', 'hdd', 'kapasitas', 'capacity', 'totalkapasitas', 'totalcapacity', 'kapasitaspenyimpanan', 'totalkapasitaspenyimpanan'],
    ];

    /**
     * Isi template CSV (header + 1 baris contoh).
     */
    public function templateCsv(): string
    {
        $lines = [
            implode(',', self::HEADERS),
            'SN12345678,ThinkPad T14,Lenovo,Intel Core i7-1355U,16GB DDR4,512GB SSD',
        ];

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * Dummy yang masih kosong: status Idle dan belum punya serial.
     */
    public function poolQuery()
    {
        return Asset::where('status', 'Idle')
            ->whereNull('serial_number')
            ->orderBy('id');
    }

    public function poolCount(): int
    {
        return $this->poolQuery()->count();
    }

    /**
     * Buat dummy baru untuk departemen user pembuat (ID mengikuti
     * AssetSequence departemen tersebut via AssetObserver).
     */
    public function generateDummies(User $user, int $count): int
    {
        $categoryId = Category::orderBy('id')->value('id');
        $locationId = Location::orderBy('id')->value('id');

        if (! $categoryId || ! $locationId) {
            throw ValidationException::withMessages([
                'count' => 'Master kategori dan lokasi harus diisi dulu sebelum generate dummy.',
            ]);
        }

        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            Asset::create([
                'name' => '-',
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'department_id' => $user->department_id,
                'status' => 'Idle',
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Parse file CSV hasil upload ke staging_rows + validasi per baris.
     *
     * @param  string  $relativePath  path relatif di disk default (hasil FileUpload)
     */
    public function parseUpload(StagingBatch $batch, string $relativePath): StagingBatch
    {
        $absolutePath = Storage::disk(config('filesystems.default', 'local'))->path($relativePath);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw ValidationException::withMessages([
                'csv_file' => 'File CSV tidak ditemukan atau tidak bisa dibaca.',
            ]);
        }

        $handle = fopen($absolutePath, 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'csv_file' => 'File CSV tidak bisa dibuka.',
            ]);
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                throw ValidationException::withMessages([
                    'csv_file' => 'File CSV kosong.',
                ]);
            }

            $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            rewind($handle);

            $header = $this->readCsvLine($handle, $delimiter);

            if ($header === null) {
                throw ValidationException::withMessages([
                    'csv_file' => 'File CSV kosong.',
                ]);
            }

            // Petakan header ke kunci kanonik (alias Indonesia/Inggris).
            // Kolom tak dikenal disimpan sebagai info tambahan, tidak dibuang.
            $columns = [];

            foreach ($header as $index => $original) {
                // Urutan penting: BOM dulu (agar enclosure terbaca), lalu kutip sisa.
                $clean = trim(trim($this->stripBom((string) $original)), '"');
                $canonical = $this->resolveHeaderKey($clean);
                $columns[$index] = $canonical ?? 'extra:'.$clean;
            }

            $present = array_values(array_filter(
                $columns,
                fn ($column) => ! str_starts_with($column, 'extra:'))
            );

            $missing = array_diff(self::REQUIRED_HEADERS, $present);

            if ($missing !== []) {
                throw ValidationException::withMessages([
                    'csv_file' => 'Header wajib tidak ditemukan: '.implode(', ', $missing)
                        .'. Unduh template CSV sebagai acuan.',
                ]);
            }

            $batch->rows()->delete();

            $seenSerials = [];
            $total = 0;
            $valid = 0;
            $lineNumber = 1; // baris 1 = header

            while (($fields = $this->readCsvLine($handle, $delimiter)) !== null) {
                $lineNumber++;

                if ($this->isBlankLine($fields)) {
                    continue;
                }

                $total++;
                $data = [];
                $extras = [];

                foreach ($columns as $index => $column) {
                    $value = trim((string) ($fields[$index] ?? ''));

                    if (str_starts_with($column, 'extra:')) {
                        if ($value !== '') {
                            $extras[substr($column, 6)] = $value;
                        }

                        continue;
                    }

                    if (! in_array($column, self::HEADERS, true)) {
                        continue;
                    }
                    $data[$column] = $value;
                }

                [$normalized, $errors] = $this->validateData($data, $seenSerials);

                // Prefill kolom manual dari nilai batch (bisa diubah per baris).
                $normalized = array_merge($normalized, $this->batchPrefill($batch));
                $normalized['extras'] = $extras;

                if (isset($normalized['serial_number'])) {
                    $seenSerials[mb_strtolower($normalized['serial_number'])] = true;
                }

                $batch->rows()->create([
                    'row_number' => $lineNumber,
                    'data' => $normalized,
                    'status' => $errors === []
                        ? StagingRow::STATUS_VALID
                        : StagingRow::STATUS_INVALID,
                    'error_message' => $errors === [] ? null : implode('; ', $errors),
                ]);

                if ($errors === []) {
                    $valid++;
                }
            }
        } finally {
            fclose($handle);
        }

        $batch->update([
            'status' => StagingBatch::STATUS_VALIDATED,
            'total_rows' => $total,
            'valid_rows' => $valid,
            'assigned_rows' => 0,
        ]);

        return $batch->refresh();
    }

    /**
     * Commit batch: assign urut ke pool dummy. All-or-nothing.
     *
     * @return array{count: int, asset_ids: array<int>}
     */
    public function commit(StagingBatch $batch): array
    {
        $batch->refresh();

        if ($batch->isCommitted()) {
            throw ValidationException::withMessages([
                'batch' => 'Batch ini sudah di-commit sebelumnya.',
            ]);
        }

        $rows = $batch->rows()
            ->where('status', StagingRow::STATUS_VALID)
            ->orderBy('row_number')
            ->get();

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'batch' => 'Tidak ada baris valid untuk di-commit.',
            ]);
        }

        // Baris tanpa lokasi/departemen (belum diisi di preview) tidak ikut commit.
        $incomplete = $batch->rows()
            ->where('status', StagingRow::STATUS_VALID)
            ->orderBy('row_number')
            ->get()
            ->filter(fn (StagingRow $row) => ! ($row->data['location_id'] ?? $batch->location_id)
                || ! ($row->data['department_id'] ?? $batch->department_id))
            ->values();

        if ($incomplete->isNotEmpty()) {
            $numbers = $incomplete->pluck('row_number')->implode(', ');

            throw ValidationException::withMessages([
                'batch' => "Baris {$numbers} belum punya lokasi/departemen. Lengkapi dulu di tabel preview.",
            ]);
        }

        $pool = $this->poolCount();

        if ($pool < $rows->count()) {
            throw ValidationException::withMessages([
                'batch' => "Stok dummy tidak cukup: butuh {$rows->count()}, tersedia {$pool}. "
                    .'Generate dummy dulu, lalu ulangi commit. Tidak ada baris yang diproses.',
            ]);
        }

        $assignedIds = [];

        DB::transaction(function () use ($batch, $rows, &$assignedIds) {
            foreach ($rows as $row) {
                // Kunci baris dummy berikutnya (FIFO) agar aman dari race.
                $dummy = $this->poolQuery()->lockForUpdate()->first();

                if (! $dummy) {
                    throw ValidationException::withMessages([
                        'batch' => 'Stok dummy habis di tengah proses. Seluruh commit dibatalkan.',
                    ]);
                }

                $data = $row->data;

                // Nilai per baris (isi manual), fallback ke nilai batch.
                $locationId = $data['location_id'] ?? $batch->location_id;
                $departmentId = $data['department_id'] ?? $batch->department_id;
                $categoryId = $data['category_id'] ?? $batch->category_id ?? $dummy->category_id;
                $holder = trim((string) ($data['user_name'] ?? $batch->user_name ?? ''));

                // 1. Field bebas (lolos AssetObserver karena tidak menyentuh
                //    location/department/user).
                $dummy->update([
                    'name' => $data['model'],
                    'serial_number' => $data['serial_number'],
                    'brand_id' => $data['brand_id'] ?? null,
                    'processor' => $data['processor'] ?? null,
                    'memory' => $data['memory'] ?? null,
                    'storage' => $data['storage'] ?? null,
                    'category_id' => $categoryId,
                    'status' => 'In use',
                ]);

                // 2. Lokasi/departemen/pemegang via history (jalur resmi —
                //    AssetHistory::booted menyinkronkannya ke aset).
                AssetHistory::create([
                    'asset_id' => $dummy->id,
                    'dari_lokasi' => $dummy->location_id,
                    'ke_lokasi' => (string) $locationId,
                    'dari_departemen' => $dummy->department_id,
                    'ke_departemen' => (string) $departmentId,
                    'user_lama' => $dummy->user_name,
                    'user_baru' => $holder !== '' ? $holder : '-',
                    'keterangan' => "Registrasi awal via staging batch #{$batch->id} (baris {$row->row_number})",
                ]);

                $row->update([
                    'status' => StagingRow::STATUS_ASSIGNED,
                    'asset_id' => $dummy->id,
                ]);

                $assignedIds[] = $dummy->id;
            }

            $batch->update([
                'status' => StagingBatch::STATUS_COMMITTED,
                'assigned_rows' => count($assignedIds),
                'committed_at' => now(),
            ]);
        });

        return ['count' => count($assignedIds), 'asset_ids' => $assignedIds];
    }

    /**
     * Nilai awal kolom manual (lokasi/departemen/kategori/pemegang)
     * diambil dari batch. Bisa diubah per baris di tabel preview.
     */
    public function batchPrefill(StagingBatch $batch): array
    {
        return [
            'location' => $batch->location?->name ?? '',
            'location_id' => $batch->location_id,
            'department' => $batch->department?->name ?? '',
            'department_id' => $batch->department_id,
            'category' => $batch->category?->name ?? '',
            'category_id' => $batch->category_id,
            'user_name' => (string) ($batch->user_name ?? ''),
        ];
    }

    /**
     * Isi manual per baris (inline edit di tabel preview): kunci yang ada
     * di $input ditimpa, sisanya ikut nilai sekarang, lalu validasi ulang
     * dan update status.
     *
     * @param  array{serial_number?: string, model?: string, brand?: string, processor?: string, memory?: string, storage?: string, location_id?: int, department_id?: int, category_id?: int|null, user_name?: string|null}  $input
     */
    public function updateRowAssignment(StagingRow $row, array $input): StagingRow
    {
        $batch = $row->batch;
        $data = $row->data ?? [];

        $takeString = fn (string $key): string => array_key_exists($key, $input)
            ? trim((string) $input[$key])
            : trim((string) ($data[$key] ?? ''));

        $takeId = fn (string $key): ?int => array_key_exists($key, $input)
            ? ($input[$key] !== null && $input[$key] !== '' ? (int) $input[$key] : null)
            : ($data[$key] ?? null);

        $serial = $takeString('serial_number');
        $model = $takeString('model');
        $brand = $takeString('brand');
        $specs = [
            'processor' => $takeString('processor'),
            'memory' => $takeString('memory'),
            'storage' => $takeString('storage'),
        ];
        $locationId = $takeId('location_id');
        $departmentId = $takeId('department_id');
        $categoryId = $takeId('category_id');
        $holder = $takeString('user_name');

        // Validasi ulang perangkat (abaikan serial baris ini sendiri).
        $seenSerials = [];

        foreach ($batch->rows()->whereKeyNot($row->id)->get(['data']) as $sibling) {
            $siblingSerial = mb_strtolower(trim((string) ($sibling->data['serial_number'] ?? '')));

            if ($siblingSerial !== '') {
                $seenSerials[$siblingSerial] = true;
            }
        }

        [$device, $errors] = $this->validateData([
            'serial_number' => $serial,
            'model' => $model,
            'brand' => $brand,
            ...$specs,
        ], $seenSerials);

        $location = $locationId ? Location::find($locationId) : null;
        $department = $departmentId ? Department::find($departmentId) : null;
        $category = $categoryId ? Category::find($categoryId) : null;

        if (! $location) {
            $errors[] = 'location wajib diisi';
        }

        if (! $department) {
            $errors[] = 'department wajib diisi';
        }

        if ($categoryId && ! $category) {
            $errors[] = 'category tidak dikenal';
        }

        $row->update([
            'data' => array_merge($data, $device, [
                'location' => $location?->name ?? '',
                'location_id' => $location?->id,
                'department' => $department?->name ?? '',
                'department_id' => $department?->id,
                'category' => $category?->name ?? '',
                'category_id' => $category?->id,
                'user_name' => $holder,
            ]),
            'status' => $errors === [] ? StagingRow::STATUS_VALID : StagingRow::STATUS_INVALID,
            'error_message' => $errors === [] ? null : implode('; ', $errors),
        ]);

        // Hitung ulang agar tombol commit & ringkasan selalu akurat.
        $batch->update([
            'valid_rows' => $batch->rows()->where('status', StagingRow::STATUS_VALID)->count(),
        ]);

        return $row->refresh();
    }

    /**
     * @return array{0: array, 1: array<string>}
     */
    private function validateData(array $data, array $seenSerials): array
    {
        $errors = [];

        $serial = $data['serial_number'] ?? '';
        $model = $data['model'] ?? '';

        if ($serial === '') {
            $errors[] = 'serial_number wajib diisi';
        } elseif (isset($seenSerials[mb_strtolower($serial)])) {
            $errors[] = "serial_number '{$serial}' duplikat dalam file";
        } elseif (Asset::where('serial_number', $serial)->exists()) {
            $errors[] = "serial_number '{$serial}' sudah terdaftar di database";
        }

        if ($model === '') {
            $errors[] = 'model wajib diisi';
        }

        $normalized = [
            'serial_number' => $serial,
            'model' => $model,
            'brand' => $data['brand'] ?? '',
            'brand_id' => null,
            'processor' => trim((string) ($data['processor'] ?? '')),
            'memory' => trim((string) ($data['memory'] ?? '')),
            'storage' => trim((string) ($data['storage'] ?? '')),
        ];

        $brandName = $normalized['brand'];

        if ($brandName !== '') {
            $brandId = $this->findIdByName(Brand::class, $brandName);

            if (! $brandId) {
                $errors[] = $this->unknownBrandMessage($brandName);
            } else {
                $normalized['brand_id'] = $brandId;
            }
        }

        return [$normalized, $errors];
    }

    /**
     * Pesan brand tak dikenal + saran terdekat dari master (bila ada
     * yang cukup mirip) agar user bisa langsung perbaiki atau tambah.
     */
    public function unknownBrandMessage(string $brandName): string
    {
        $message = "brand '{$brandName}' tidak dikenal";

        $suggestion = $this->suggestBrand($brandName);

        if ($suggestion) {
            return $message.". Mungkin maksud Anda: '{$suggestion}'? Perbaiki ejaannya atau tambahkan '{$brandName}' ke master brand.";
        }

        return $message.'. Tambahkan ke master brand atau perbaiki ejaannya.';
    }

    /**
     * Cari nama brand master yang paling mirip (case-insensitive,
     * abaikan spasi/tanda baca). Null bila tidak ada yang cukup mirip.
     */
    public function suggestBrand(string $name, float $threshold = 60.0): ?string
    {
        $needle = $this->compactName($name);

        if ($needle === '') {
            return null;
        }

        $best = null;
        $bestScore = 0.0;

        foreach (Brand::pluck('name') as $candidate) {
            similar_text($needle, $this->compactName((string) $candidate), $score);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = (string) $candidate;
            }
        }

        return $bestScore >= $threshold ? $best : null;
    }

    private function compactName(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower(trim($name))) ?? '';
    }

    /**
     * Daftarkan brand dari baris staging ke master, lalu validasi ulang
     * baris tersebut (idempotent: aman bila brand sudah ada).
     */
    public function createBrandFromRow(StagingRow $row): StagingRow
    {
        $brandName = trim((string) ($row->data['brand'] ?? ''));

        if ($brandName === '') {
            throw ValidationException::withMessages([
                'brand' => 'Baris ini tidak punya nama brand untuk ditambahkan.',
            ]);
        }

        $existingId = $this->findIdByName(Brand::class, $brandName);

        if (! $existingId) {
            Brand::create(['name' => $brandName]);
        }

        return $this->updateRowAssignment($row->refresh(), []);
    }

    private function findIdByName(string $modelClass, string $name): ?int
    {
        return $modelClass::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])->value('id');
    }

    /**
     * Ubah teks header apa pun menjadi kunci kanonik atau null bila tak dikenal.
     */
    private function resolveHeaderKey(string $header): ?string
    {
        $compact = preg_replace('/[^a-z0-9]/', '', mb_strtolower(trim($this->stripBom($header)))) ?? '';

        foreach (self::HEADER_ALIASES as $canonical => $aliases) {
            if ($compact === $canonical || in_array($compact, $aliases, true)) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>|null  null saat EOF
     */
    private function readCsvLine($handle, string $delimiter): ?array
    {
        while (($fields = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            // Lewati baris yang benar-benar kosong (tetap hitung untuk nomor baris di pemanggil).
            if ($fields === [null] || $fields === [false]) {
                return [];
            }

            return $fields;
        }

        return null;
    }

    private function isBlankLine(array $fields): bool
    {
        foreach ($fields as $field) {
            if (trim((string) $field) !== '') {
                return false;
            }
        }

        return true;
    }

    private function stripBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
    }
}
