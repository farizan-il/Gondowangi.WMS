<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomingActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'incoming_id',
        'user_id',
        'action',
        'description',
        'material_id',
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
        'exp_date' => 'date',
        'qty_before' => 'decimal:2',
        'qty_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function incomingGood(): BelongsTo
    {
        return $this->belongsTo(IncomingGood::class, 'incoming_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
