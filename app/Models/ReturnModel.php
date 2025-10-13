<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'return_type',
        'return_date',
        'supplier_id',
        'department',
        'incoming_id',
        'reservation_request_id',
        'reference_number',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function incomingGood(): BelongsTo
    {
        return $this->belongsTo(IncomingGood::class, 'incoming_id');
    }

    public function reservationRequest(): BelongsTo
    {
        return $this->belongsTo(ReservationRequest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ReturnDocument::class, 'return_id');
    }
}