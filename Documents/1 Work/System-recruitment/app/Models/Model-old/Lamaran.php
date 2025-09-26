<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;

    protected $table = 'lamaran';

    protected $fillable = [
        'lowongan_id',
        'kandidat_id',
        'cv_file',
        'cover_letter',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_lamar' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_lamar';
    const UPDATED_AT = 'tanggal_diperbarui';

    // Relationships
    public function lowongan()
    {
        return $this->belongsTo(LowonganKerja::class, 'lowongan_id');
    }

    public function kandidat()
    {
        return $this->belongsTo(Pengguna::class, 'kandidat_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
