<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Halaman extends Model
{
    use HasFactory;

    protected $table = 'halaman';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'meta_description',
        'meta_keywords',
        'gambar_utama',
        'status',
        'urutan',
        'penulis_id'
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = 'tanggal_diperbarui';

    // Relationships
    public function penulis()
    {
        return $this->belongsTo(Pengguna::class, 'penulis_id');
    }

    public function kunjunganHalaman()
    {
        return $this->hasMany(KunjunganHalaman::class, 'halaman_id');
    }

    public function statistikHalaman()
    {
        return $this->hasMany(StatistikHalaman::class, 'halaman_id');
    }

    // Scopes
    public function scopeTerbit($query)
    {
        return $query->where('status', 'terbit');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}