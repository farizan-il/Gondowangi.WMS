<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CycleCountItem extends Model
{
    protected $guarded = [];

    public function cycleCount()
    {
        return $this->belongsTo(CycleCount::class);
    }

    public function bin()
    {
        return $this->belongsTo(WarehouseBin::class, 'bin_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
