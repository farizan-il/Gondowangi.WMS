<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrderItem extends Model
{
    protected $fillable = [
        'to_id', 'material_id', 'batch_lot', 'source_bin_id', 'destination_bin_id',
        'qty_planned', 'qty_actual', 'uom', 'status', 'box_scanned',
        'source_bin_scanned', 'dest_bin_scanned', 'scanned_at', 'completed_at', 'notes'
    ];

    protected $casts = [
        'box_scanned' => 'boolean',
        'source_bin_scanned' => 'boolean',
        'dest_bin_scanned' => 'boolean',
        'scanned_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function transferOrder()
    {
        return $this->belongsTo(TransferOrder::class, 'to_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function sourceBin()
    {
        return $this->belongsTo(WarehouseBin::class, 'source_bin_id');
    }

    public function destinationBin()
    {
        return $this->belongsTo(WarehouseBin::class, 'destination_bin_id');
    }
}