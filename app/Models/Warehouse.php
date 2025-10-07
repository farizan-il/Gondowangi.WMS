<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'alamat',
        'tipe',
        'status',
    ];

    public function zones()
    {
        return $this->hasMany(WarehouseZone::class);
    }

    public function bins()
    {
        return $this->hasMany(WarehouseBin::class);
    }
}