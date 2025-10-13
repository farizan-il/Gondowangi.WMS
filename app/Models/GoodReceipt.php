<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodReceipt extends Model
{
    protected $fillable = [
        'gr_number',
        'qc_checklist_id',
        'incoming_item_id',
        'material_id',
        'batch_lot',
        'qty_received',
        'uom',
        'status_material',
        'warehouse_location',
        'tanggal_gr',
        'created_by',
    ];

    protected $casts = [
        'qty_received' => 'decimal:2',
        'tanggal_gr' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function qcChecklist(): BelongsTo
    {
        return $this->belongsTo(QcChecklist::class);
    }

    public function incomingItem(): BelongsTo
    {
        return $this->belongsTo(IncomingGoodsItem::class, 'incoming_item_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventoryStock()
    {
        return $this->hasMany(InventoryStock::class, 'gr_id');
    }

    public function isReleased(): bool
    {
        return $this->status_material === 'RELEASED';
    }

    public function isKarantina(): bool
    {
        return $this->status_material === 'KARANTINA';
    }
}
