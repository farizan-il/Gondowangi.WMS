<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
    protected $fillable = [
        'to_number',
        'transaction_type', //'Putaway - QC Release','Transfer - Internal','Transfer - Bin to Bin','Picking - Production','Picking - Sales Order'
        'warehouse_id',
        'reservation_request_id',
        'reservation_no',
        'creation_date',
        'scheduled_date',
        'completion_date',
        'status', //'Pending','In Progress','Completed','Short-Pick','Cancelled'
        'created_by',
        'executed_by',
        'notes'
    ];

    protected $casts = [
        'creation_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'completion_date' => 'datetime'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(TransferOrderItem::class, 'to_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executedBy()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}