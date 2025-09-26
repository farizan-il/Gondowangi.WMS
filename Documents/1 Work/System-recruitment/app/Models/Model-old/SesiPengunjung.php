<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiPengunjung extends Model
{
    use HasFactory;

    protected $table = 'sesi_pengunjung';

    protected $fillable = [
        'session_id',
        'pengguna_id',
        'ip_address',
        'user_agent',
        'perangkat',
        'browser',
        'os',
        'negara',
        'kota',
        'waktu_mulai',
        'waktu_selesai',
        'status'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function kunjunganHalaman()
    {
        return $this->hasMany(KunjunganHalaman::class, 'sesi_id');
    }

    public function pengunjungRealtime()
    {
        return $this->hasOne(PengunjungRealtime::class, 'sesi_id');
    }

    public function kontakFormulir()
    {
        return $this->hasMany(KontakFormulir::class, 'sesi_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByPerangkat($query, $perangkat)
    {
        return $query->where('perangkat', $perangkat);
    }
}