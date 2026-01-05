<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationRequest extends Model
{
    protected $fillable = [
        'no_reservasi',
        'request_type', // 'FOH-RS','Packaging','raw-material','Additional'
        'tanggal_permintaan',
        'status',
        'alasan_reservasi',
        'departemen',
        'nama_produk',
        'no_bets_filling',
        'kode_produk',
        'no_bets',
        'besar_bets',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'picking_started_at',
        'picking_completed_at',
        // TO Number Generation Fields
        'to_number',
        'to_generated_at',
        'to_generated_by',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'datetime',
        'besar_bets' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'picking_started_at' => 'datetime',
        'picking_completed_at' => 'datetime',
        'to_generated_at' => 'datetime',
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationRequestItem::class, 'request_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnModel::class);
    }

    public function transferOrders(): HasMany
    {
        return $this->hasMany(TransferOrder::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
