<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelegasiApproval extends Model
{
    use HasFactory;

    protected $table = 'DelegasiApproval';

    protected $fillable = [
        'pemberi_id',
        'penerima_id',
        'kategori_pengajuan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function pemberi()
    {
        return $this->belongsTo(User::class, 'pemberi_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function kategoriPengajuan()
    {
        return $this->belongsTo(KategoriPengajuan::class, 'kategori_pengajuan_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    public function scopeBerlaku($query)
    {
        $today = now()->toDateString();
        return $query->where('tanggal_mulai', '<=', $today)
                    ->where('tanggal_selesai', '>=', $today)
                    ->where('status', 'aktif');
    }

    public function scopeByPemberi($query, $pemberiId)
    {
        return $query->where('pemberi_id', $pemberiId);
    }

    public function scopeByPenerima($query, $penerimaId)
    {
        return $query->where('penerima_id', $penerimaId);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_pengajuan_id', $kategoriId);
    }

    // Accessor & Mutator
    public function getStatusLabelAttribute()
    {
        return $this->status === 'aktif' ? 'Aktif' : 'Nonaktif';
    }

    // Helper Methods
    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    public function isBerlaku()
    {
        $today = now()->toDateString();
        return $this->tanggal_mulai <= $today && 
               $this->tanggal_selesai >= $today && 
               $this->status === 'aktif';
    }

    public function durasi()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }
}