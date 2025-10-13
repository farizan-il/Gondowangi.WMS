<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'movement_number',
        'movement_type',
        'material_id',
        'batch_lot',
        'from_warehouse_id',
        'from_bin_id',
        'to_warehouse_id',
        'to_bin_id',
        'qty',
        'uom',
        'reference_type',
        'reference_id',
        'movement_date',
        'executed_by',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function fromBin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'from_bin_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function toBin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'to_bin_id');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
