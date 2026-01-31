<?php

namespace App\Models;

use App\Enums\StockImportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockImport extends Model
{
    protected $fillable = [
        'company_id',
        'original_filename',
        'stored_path',
        'status',
        'total_rows',
        'inserted_rows',
        'error_message',
    ];

    protected $casts = [
        'status' => StockImportStatus::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', StockImportStatus::PENDING->value);
    }

    public function scopeProcessing($q)
    {
        return $q->where('status', StockImportStatus::PROCESSING->value);
    }

    public function scopeDone($q)
    {
        return $q->where('status', StockImportStatus::DONE->value);
    }

    public function scopeFailed($q)
    {
        return $q->where('status', StockImportStatus::FAILED->value);
    }

    public function markProcessing(): void
    {
        $this->update([
            'status' => StockImportStatus::PROCESSING,
            'error_message' => null,
            'total_rows' => null,
            'inserted_rows' => null,
        ]);
    }

    public function markDone(int $total, int $inserted): void
    {
        $this->update([
            'status' => StockImportStatus::DONE,
            'total_rows' => $total,
            'inserted_rows' => $inserted,
            'error_message' => null,
        ]);
    }

    public function markFailed(string $message, ?int $total = null, ?int $inserted = null): void
    {
        $this->update([
            'status' => StockImportStatus::FAILED,
            'total_rows' => $total ?: null,
            'inserted_rows' => $inserted ?: null,
            'error_message' => $message,
        ]);
    }
}
