<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowApproval extends Model
{
    use HasFactory;

    protected $table = 'FlowApproval';
    
    protected $fillable = [
        'kategori_pengajuan_id',
        'requester_id',    // Mengikat ke karyawan spesifik sebagai requester
        'approver_id',     // Mengikat ke karyawan spesifik sebagai approver
        'urutan',
        'nama_step',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function kategoriPengajuan()
    {
        return $this->belongsTo(KategoriPengajuan::class);
    }
    
    public function requester()
    {
        return $this->belongsTo(Karyawan::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }

    public function progressApprovals()
    {
        return $this->hasMany(ProgressApproval::class);
    }
    
    // PERBAIKAN: Scope untuk filter berdasarkan kategori
    public function scopeForKategori($query, $kategoriId)
    {
        return $query->where('kategori_pengajuan_id', $kategoriId);
    }

    // PERBAIKAN: Scope untuk filter berdasarkan requester dengan validasi null
    public function scopeForRequester($query, $requesterId)
    {
        return $query->where(function($q) use ($requesterId) {
            $q->where('requester_id', $requesterId)
              ->orWhereNull('requester_id'); // Jika null, berlaku untuk semua requester
        });
    }

    // PERBAIKAN: Scope untuk filter status aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // PERBAIKAN: Method untuk memvalidasi apakah flow approval sudah lengkap
    public static function isFlowCompleteForKategori($kategoriId, $requesterId = null)
    {
        $flowCount = static::forKategori($kategoriId)
            ->when($requesterId, function($query) use ($requesterId) {
                return $query->forRequester($requesterId);
            })
            ->aktif()
            ->count();
            
        return $flowCount > 0;
    }

    // PERBAIKAN: Method untuk mendapatkan flow approval terurut
    public static function getOrderedFlowForKategori($kategoriId, $requesterId = null)
    {
        return static::forKategori($kategoriId)
            ->when($requesterId, function($query) use ($requesterId) {
                return $query->forRequester($requesterId);
            })
            ->aktif()
            ->orderBy('urutan')
            ->get();
    }

    // PERBAIKAN: Method untuk validasi approver
    public function hasValidApprover()
    {
        return $this->approver_id && $this->approver()->exists();
    }

    // PERBAIKAN: Method untuk mendapatkan step berikutnya
    public function getNextStep($kategoriId, $requesterId = null)
    {
        return static::forKategori($kategoriId)
            ->when($requesterId, function($query) use ($requesterId) {
                return $query->forRequester($requesterId);
            })
            ->aktif()
            ->where('urutan', '>', $this->urutan)
            ->orderBy('urutan')
            ->first();
    }
}

