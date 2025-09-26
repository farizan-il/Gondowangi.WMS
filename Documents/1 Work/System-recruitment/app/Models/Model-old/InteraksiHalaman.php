<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteraksiHalaman extends Model
{
    use HasFactory;

    protected $table = 'interaksi_halaman';

    protected $fillable = [
        'kunjungan_id',
        'jenis_interaksi',
        'elemen',
        'posisi_x',
        'posisi_y',
        'nilai',
        'waktu_interaksi'
    ];

    protected $casts = [
        'waktu_interaksi' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(KunjunganHalaman::class, 'kunjungan_id');
    }

    // Scopes
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_interaksi', $jenis);
    }
}
