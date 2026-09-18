<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StagingBatch extends Model
{
    public const STATUS_UPLOADED = 'uploaded';

    public const STATUS_VALIDATED = 'validated';

    public const STATUS_COMMITTED = 'committed';

    protected $fillable = [
        'name',
        'file_name',
        'status',
        'total_rows',
        'valid_rows',
        'assigned_rows',
        'uploaded_by',
        'committed_at',
        'location_id',
        'department_id',
        'category_id',
        'user_name',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(StagingRow::class, 'batch_id')->orderBy('row_number');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isCommitted(): bool
    {
        return $this->status === self::STATUS_COMMITTED;
    }
}
