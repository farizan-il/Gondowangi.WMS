<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferOrder extends Model
{
    protected $fillable = [
        'to_number',
        'transaction_type',
        'warehouse_id',
        'reservation_request_id',
        'reservation_no',
        'creation_date',
        'scheduled_date',
        'completion_date',
        'started_at',
        'status',
        'created_by',
        'executed_by',
        'notes',
    ];

    protected $casts = [
        'creation_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'completion_date' => 'datetime',
        'started_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function reservationRequest(): BelongsTo
    {
        return $this->belongsTo(ReservationRequest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferOrderItem::class, 'to_id');
    }
}
