<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'material_id',
        'supplier_id',
        'warehousebin_id',
        'user_id_target', // untuk log user yang di-create/update/delete
        'batch_lot',
        'exp_date',
        'qty_before',
        'qty_after',
        'bin_from',
        'bin_to',
        'reference_document',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'device_info',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'exp_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouseBin()
    {
        return $this->belongsTo(WarehouseBin::class, 'warehousebin_id');
    }
}