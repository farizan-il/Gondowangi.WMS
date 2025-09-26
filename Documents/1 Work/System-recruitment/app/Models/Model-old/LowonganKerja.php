<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowonganKerja extends Model
{
    use HasFactory;

    protected $table = 'lowongan_kerja';

    protected $fillable = [
        'judul',
        'deskripsi',
        'requirements',
        'lokasi',
        'jenis_kerja',
        'gaji_min',
        'gaji_max',
        'tanggal_tutup',
        'status',
        'dibuat_oleh'
    ];

    protected $casts = [
        'tanggal_tutup' => 'date',
        'gaji_min' => 'decimal:2',
        'gaji_max' => 'decimal:2',
        'tanggal_dibuat' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = 'tanggal_diperbarui';

    // Relationships
    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'lowongan_id');
    }

    // Scopes
    public function scopeBuka($query)
    {
        return $query->where('status', 'buka')
                    ->where('tanggal_tutup', '>=', now());
    }

    public function scopeByJenisKerja($query, $jenis)
    {
        return $query->where('jenis_kerja', $jenis);
    }
}
