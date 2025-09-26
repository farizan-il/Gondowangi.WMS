<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunjunganHalaman extends Model
{
    use HasFactory;

    protected $table = 'kunjungan_halaman';

    protected $fillable = [
        'sesi_id',
        'halaman_id',
        'url',
        'judul_halaman',
        'waktu_masuk',
        'waktu_keluar',
        'durasi_detik',
        'sumber_referral',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'is_bounce'
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'is_bounce' => 'boolean',
    ];

    public $timestamps = false;

    // Relationships
    public function sesi()
    {
        return $this->belongsTo(SesiPengunjung::class, 'sesi_id');
    }

    public function halaman()
    {
        return $this->belongsTo(Halaman::class, 'halaman_id');
    }

    public function interaksiHalaman()
    {
        return $this->hasMany(InteraksiHalaman::class, 'kunjungan_id');
    }

    // Scopes
    public function scopeBounce($query)
    {
        return $query->where('is_bounce', true);
    }

    public function scopeByUtmSource($query, $source)
    {
        return $query->where('utm_source', $source);
    }
}
