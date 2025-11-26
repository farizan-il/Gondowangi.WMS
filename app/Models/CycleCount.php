<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CycleCount extends Model
{
    protected $fillable = [
        'cycle_number', 'material_id', 'warehouse_bin_id',
        'system_qty', 'physical_qty', 'scanned_serial',
        'scanned_bin', 'status', 'count_date', 'spv_note'
    ];

    protected $casts = [
        'count_date' => 'datetime',
        'system_qty' => 'decimal:2',
        'physical_qty' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function bin()
    {
        return $this->belongsTo(WarehouseBin::class, 'warehouse_bin_id');
    }
}