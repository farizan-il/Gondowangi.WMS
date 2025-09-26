<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'kategori',
        'brand_id',
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
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}