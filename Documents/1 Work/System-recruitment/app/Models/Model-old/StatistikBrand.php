<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatistikBrand extends Model
{
    use HasFactory;

    protected $table = 'statistik_brand';

    protected $fillable = [
        'brand_id',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_kunjungan',
        'total_interaksi',
        'persentase_pertumbuhan',
        'ranking_popularitas'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'persentase_pertumbuhan' => 'decimal:2',
        'tanggal_dibuat' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = null;

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Scopes
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }
}
