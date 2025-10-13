<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'material_id',
        'batch_lot',
        'qty_return',
        'uom',
        'return_reason',
        'reason_notes',
        'from_bin_id',
        'stock_deducted',
        'deducted_at',
        'item_condition',
    ];

    protected $casts = [
        'qty_return' => 'decimal:2',
        'stock_deducted' => 'boolean',
        'deducted_at' => 'datetime',
    ];

    public function return(): BelongsTo
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function fromBin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'from_bin_id');
    }
}
