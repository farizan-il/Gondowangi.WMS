<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSlip extends Model
{
    use HasFactory;

    protected $table = 'return_slips';

    protected $fillable = [
        'return_number',
        'qc_checklist_id',
        'incoming_item_id',
        'material_id',
        'supplier_id',
        'batch_lot',
        'qty_return',
        'uom',
        'alasan_reject',
        'status',
        'tanggal_return',
        'created_by',
    ];

    protected $casts = [
        'qty_return' => 'decimal:2',
        'tanggal_return' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function qcChecklist(): BelongsTo
    {
        return $this->belongsTo(QcChecklist::class, 'qc_checklist_id');
    }

    public function incomingItem(): BelongsTo
    {
        return $this->belongsTo(IncomingGoodsItem::class, 'incoming_item_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}