<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brand';

    protected $fillable = [
        'nama',
        'deskripsi',
        'logo',
        'website',
        'status',
        'urutan'
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = 'tanggal_diperbarui';

    // Relationships
    public function produk()
    {
        return $this->hasMany(Produk::class, 'brand_id');
    }

    public function statistikBrand()
    {
        return $this->hasMany(StatistikBrand::class, 'brand_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
