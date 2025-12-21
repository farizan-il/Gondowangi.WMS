<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRemovalLog extends Model
{
    protected $fillable = [
        'reservation_request_id',
        'reservation_id',
        'material_code',
        'material_name',
        'batch_lot',
        'expiry_date',
        'qty_removed',
        'uom',
        'removal_reason',
        'days_until_expiry',
        'removed_by',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty_removed' => 'decimal:2',
    ];

    public function reservationRequest()
    {
        return $this->belongsTo(ReservationRequest::class);
    }

    public function removedByUser()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }
}
