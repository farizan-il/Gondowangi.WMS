<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialReqc extends Model
{
    protected $table = 'material_reqc';

    protected $fillable = [
        'material_id',
        'inventory_stock_id',
        'batch_lot',
        'old_exp_date',
        'new_exp_date',
        'bin_from_id',
        'bin_qrt_id',
        'qty',
        'status',
        'qc_notes',
        'qc_by',
        'qc_date',
    ];

    protected $casts = [
        'old_exp_date' => 'date',
        'new_exp_date' => 'date',
        'qc_date' => 'datetime',
        'qty' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function inventoryStock(): BelongsTo
    {
        return $this->belongsTo(InventoryStock::class);
    }

    public function binFrom(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'bin_from_id');
    }

    public function binQrt(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'bin_qrt_id');
    }

    public function qcUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qc_by');
    }
}
