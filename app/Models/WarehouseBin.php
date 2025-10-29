<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseBin extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'warehouse_id', //default = 1
        'bin_code',
        'bin_name',
        'bin_type',
        'capacity',
        'current_items',
        'status',
        'qr_code_path',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_items' => 'integer',
    ];

    public function zone()
    {
        return $this->belongsTo(WarehouseZone::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}