<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AssetHistory extends Model
{
    //
    protected $fillable = [
    'asset_id', 'dari_lokasi', 'ke_lokasi', 'dari_departemen',
    'ke_departemen', 'user_lama', 'user_baru', 'keterangan'
];

    protected static function booted(): void
    {
        // Setiap ada pencatatan baru, sinkronkan lokasi, departemen,
        // dan pemegang ke data aset induknya.
        static::created(function (AssetHistory $history): void {
            $asset = $history->asset;
            if (! $asset) {
                return;
            }

            $sync = [];

            if (is_numeric($history->ke_lokasi)) {
                $sync['location_id'] = (int) $history->ke_lokasi;
            }

            if (is_numeric($history->ke_departemen)) {
                $sync['department_id'] = (int) $history->ke_departemen;
            }

            $holder = trim((string) $history->user_baru);
            if ($holder !== '' && $holder !== '-') {
                $sync['user_name'] = $holder;
            }

            if ($sync !== []) {
                Asset::$syncingFromHistory = true;
                try {
                    $asset->update($sync);
                } finally {
                    Asset::$syncingFromHistory = false;
                }
            }
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Tambahkan ini di app/Models/AssetHistory.php
public function location()
{
    return $this->belongsTo(Location::class, 'ke_lokasi'); // 'ke_lokasi' adalah foreign key di tabel asset_histories
}

public function dariLokasi(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dari_lokasi');
    }

    public function keLokasi(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'ke_lokasi');
    }

    public function departemenTujuan(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'ke_departemen');
    }
}
