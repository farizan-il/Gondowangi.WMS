<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferOrderScan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'to_item_id',
        'scan_type',
        'scan_code',
        'scan_time',
        'scanned_by',
        'is_valid',
        'error_message',
        'qty_scanned',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'is_valid' => 'boolean',
        'qty_scanned' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function transferOrderItem(): BelongsTo
    {
        return $this->belongsTo(TransferOrderItem::class, 'to_item_id');
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
