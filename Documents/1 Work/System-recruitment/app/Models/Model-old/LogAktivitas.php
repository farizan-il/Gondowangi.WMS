<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'pengguna_id',
        'aksi',
        'tabel_target',
        'id_target',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'data_lama' => 'json',
        'data_baru' => 'json',
        'waktu_aksi' => 'datetime',
    ];

    const CREATED_AT = 'waktu_aksi';
    const UPDATED_AT = null;

    // Relationships
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    // Scopes
    public function scopeByAksi($query, $aksi)
    {
        return $query->where('aksi', $aksi);
    }

    public function scopeByTabel($query, $tabel)
    {
        return $query->where('tabel_target', $tabel);
    }
}
