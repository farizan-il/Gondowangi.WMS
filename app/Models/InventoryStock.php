<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryStock extends Model
{
    protected $table = 'inventory_stock';

    protected $fillable = [
        'material_id',
        'warehouse_id',
        'bin_id',
        'batch_lot',
        'exp_date',
        'qty_on_hand',
        'qty_reserved',
        'qty_available',
        'uom',
        'status',
        'gr_id',
        'last_movement_date',
    ];

    protected $casts = [
        'exp_date' => 'date',
        'qty_on_hand' => 'decimal:2',
        'qty_reserved' => 'decimal:2',
        'qty_available' => 'decimal:2',
        'last_movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class);
    }

    public function goodReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodReceipt::class, 'gr_id');
    }

    public function hasAvailableStock(): bool
    {
        return $this->qty_available > 0;
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->exp_date && $this->exp_date <= now()->addDays($days);
    }

    public function updateAvailableQty(): void
    {
        $this->qty_available = $this->qty_on_hand - $this->qty_reserved;
        $this->save();
    }

    public function movements(): HasMany
    {
 
        return $this->hasMany(StockMovement::class, 'material_id', 'material_id')
            ->whereColumn('batch_lot', 'batch_lot')
            ->orderByDesc('movement_date');
    }
}
