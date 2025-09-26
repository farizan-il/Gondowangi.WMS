<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatistikHalaman extends Model
{
    use HasFactory;

    protected $table = 'statistik_halaman';

    protected $fillable = [
        'halaman_id',
        'tanggal',
        'total_kunjungan',
        'pengunjung_unik',
        'rata_rata_durasi',
        'tingkat_pentalan',
        'total_interaksi'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'rata_rata_durasi' => 'decimal:2',
        'tingkat_pentalan' => 'decimal:2',
    ];

    public $timestamps = false;

    // Relationships
    public function halaman()
    {
        return $this->belongsTo(Halaman::class, 'halaman_id');
    }

    // Scopes
    public function scopeByPeriode($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }
}
