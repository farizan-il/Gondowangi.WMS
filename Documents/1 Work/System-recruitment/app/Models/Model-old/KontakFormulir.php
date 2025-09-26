<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakFormulir extends Model
{
    use HasFactory;

    protected $table = 'kontak_formulir';

    protected $fillable = [
        'nama',
        'email',
        'subjek',
        'pesan',
        'nomor_telepon',
        'perusahaan',
        'status',
        'sesi_id'
    ];

    protected $casts = [
        'tanggal_dikirim' => 'datetime',
        'tanggal_dibaca' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dikirim';
    const UPDATED_AT = null;

    // Relationships
    public function sesi()
    {
        return $this->belongsTo(SesiPengunjung::class, 'sesi_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('status', 'baru');
    }
}
