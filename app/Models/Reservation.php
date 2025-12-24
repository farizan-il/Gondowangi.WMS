<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_no',
        'reservation_request_id',
        'reservation_type', 
        'material_id',
        'warehouse_id',
        'bin_id',
        'batch_lot',
        'qty_reserved',
        'uom',
        'status',
        'reference_no',
        'reservation_date',
        'expiry_date',
        'picked_qty',
        'created_by',
    ];

    protected $casts = [
        'qty_reserved' => 'decimal:6',
        'picked_qty' => 'decimal:6',
        'reservation_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function reservationRequest(): BelongsTo
    {
        return $this->belongsTo(ReservationRequest::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationRequestItem::class, 'reservation_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'bin_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
