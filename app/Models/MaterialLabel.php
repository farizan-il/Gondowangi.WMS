<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialLabel extends Model
{
    protected $fillable = [
        'label_number',
        'qr_code',
        'incoming_item_id',
        'material_id',
        'batch_lot',
        'exp_date',
        'qty',
        'uom',
        'status',
        'warehouse_location',
        'printed_at',
        'printed_by',
    ];

    protected $casts = [
        'exp_date' => 'date',
        'qty' => 'decimal:2',
        'printed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function incomingItem(): BelongsTo
    {
        return $this->belongsTo(IncomingGoodsItem::class, 'incoming_item_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function isExpired(): bool
    {
        return $this->exp_date && $this->exp_date < now();
    }

    public function isReleased(): bool
    {
        return $this->status === 'RELEASED';
    }
}