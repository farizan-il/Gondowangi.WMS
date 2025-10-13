<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferOrderItem extends Model
{
    protected $fillable = [
        'to_id',
        'material_id',
        'batch_lot',
        'source_bin_id',
        'destination_bin_id',
        'qty_planned',
        'qty_actual',
        'uom',
        'status',
        'box_scanned',
        'box_scan_time',
        'source_bin_scanned',
        'source_bin_scan_time',
        'dest_bin_scanned',
        'dest_bin_scan_time',
        'scanned_at',
        'completed_at',
        'picker_user_id',
        'notes',
    ];

    protected $casts = [
        'qty_planned' => 'decimal:2',
        'qty_actual' => 'decimal:2',
        'box_scanned' => 'boolean',
        'box_scan_time' => 'datetime',
        'source_bin_scanned' => 'boolean',
        'source_bin_scan_time' => 'datetime',
        'dest_bin_scanned' => 'boolean',
        'dest_bin_scan_time' => 'datetime',
        'scanned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function transferOrder(): BelongsTo
    {
        return $this->belongsTo(TransferOrder::class, 'to_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function sourceBin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'source_bin_id');
    }

    public function destinationBin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'destination_bin_id');
    }

    public function picker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picker_user_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(TransferOrderScan::class, 'to_item_id');
    }
}
