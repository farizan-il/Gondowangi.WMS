<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatistikHarian extends Model
{
    use HasFactory;

    protected $table = 'statistik_harian';

    protected $fillable = [
        'tanggal',
        'total_pengunjung',
        'total_sesi',
        'total_tayangan_halaman',
        'rata_rata_durasi_sesi',
        'tingkat_pentalan',
        'pengunjung_baru',
        'pengunjung_kembali',
        'konversi_formulir'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'rata_rata_durasi_sesi' => 'decimal:2',
        'tingkat_pentalan' => 'decimal:2',
        'tanggal_dibuat' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = null;

    // Scopes
    public function scopeByPeriode($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }
}