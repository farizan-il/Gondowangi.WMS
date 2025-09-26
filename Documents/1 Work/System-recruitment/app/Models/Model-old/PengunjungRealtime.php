<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengunjungRealtime extends Model
{
    use HasFactory;

    protected $table = 'pengunjung_realtime';

    protected $fillable = [
        'sesi_id',
        'halaman_saat_ini',
        'waktu_terakhir_aktif',
        'status'
    ];

    protected $casts = [
        'waktu_terakhir_aktif' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function sesi()
    {
        return $this->belongsTo(SesiPengunjung::class, 'sesi_id');
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }
}