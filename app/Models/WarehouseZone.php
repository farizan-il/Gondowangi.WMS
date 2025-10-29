<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id', //deafultnya = 1
        'zone_code',
        'zone_name',
        'zone_type',
        'status',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bins()
    {
        return $this->hasMany(WarehouseBin::class, 'zone_id');
    }
}