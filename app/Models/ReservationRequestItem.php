<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationRequestItem extends Model
{
    protected $fillable = [
        'request_id',
        'material_id',
        'kode_item',
        'keterangan',
        'qty',
        'uom',
        'nama_material',
        'kode_pm',
        'jumlah_permintaan',
        'kode_bahan',
        'nama_bahan',
        'jumlah_kebutuhan',
        'jumlah_kirim',
        'alasan_penambahan',
        'qty_picked',
        'qty_remaining',
        'status',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'jumlah_permintaan' => 'decimal:2',
        'jumlah_kebutuhan' => 'decimal:2',
        'jumlah_kirim' => 'decimal:2',
        'qty_picked' => 'decimal:2',
        'qty_remaining' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ReservationRequest::class, 'request_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}