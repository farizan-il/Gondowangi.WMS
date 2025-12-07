<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'Pengajuan';
    
    protected $fillable = [
        'nomor_pengajuan',
        'kategori_pengajuan_id',
        'requester_id',
        'judul',
        'deskripsi',
        'nominal_pengajuan',
        'mata_uang',
        'tanggal_pengajuan',
        'tanggal_kebutuhan',
        'status_pengajuan',
        'current_step',
        'total_step',
        'catatan_requester',
        'file_pendukung',
        'is_settlement_required',
        'settlement_id'
    ];

    protected $casts = [
        'kategori_pengajuan_id' => 'integer',
        'requester_id' => 'integer',
        'nominal_pengajuan' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_kebutuhan' => 'date',
        'current_step' => 'integer',
        'total_step' => 'integer',
        'file_pendukung' => 'array',
        'is_settlement_required' => 'boolean',
        'settlement_id' => 'integer',
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

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function detailPengajuan()
    {
        return $this->hasMany(DetailPengajuan::class);
    }

    public function historyPengajuan()
    {
        return $this->hasMany(HistoryPengajuan::class);
    }

    public function progressApprovals()
    {
        return $this->hasMany(ProgressApproval::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pengajuan', $status);
    }

    public function scopeByRequester($query, $requesterId)
    {
        return $query->where('requester_id', $requesterId);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_pengajuan_id', $kategoriId);
    }

    public function scopeRequireSettlement($query)
    {
        return $query->where('is_settlement_required', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status_pengajuan', 'disetujui');
    }

    // Helper methods
    public function generateNomorPengajuan()
    {
        $kategori = $this->kategoriPengajuan;
        $tahun = date('Y');
        $bulan = date('m');
        
        $lastNumber = static::where('kategori_pengajuan_id', $this->kategori_pengajuan_id)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->count();
        
        $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return $kategori->kode . '/' . $tahun . $bulan . '/' . $sequence;
    }

    // Method untuk cek apakah semua approval sudah selesai
    public function isFullyApproved()
    {
        return $this->status_pengajuan === 'disetujui' && 
               $this->current_step >= $this->total_step;
    }

    // Method untuk cek apakah settlement sudah dibuat
    public function hasSettlement()
    {
        return !is_null($this->settlement_id);
    }

    // Method untuk auto-create settlement jika belum ada
    public function createSettlementIfNeeded()
    {
        if ($this->is_settlement_required && 
            $this->isFullyApproved() && 
            !$this->hasSettlement()) {
            
            return $this->createSettlement();
        }
        
        return null;
    }

    private function createSettlement()
    {
        $tahun = date('Y');
        $bulan = date('m');
        
        $lastSettlement = Settlement::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->count();
        
        $sequence = str_pad($lastSettlement + 1, 4, '0', STR_PAD_LEFT);
        $nomorSettlement = 'STL/' . $tahun . $bulan . '/' . $sequence;

        $settlement = Settlement::create([
            'pengajuan_id' => $this->id,
            'nomor_settlement' => $nomorSettlement,
            'tanggal_settlement' => now(),
            'total_actual' => 0,
            'selisih' => 0,
            'status_settlement' => 'pending',
            'catatan_settlement' => 'Settlement otomatis dibuat dari pengajuan: ' . $this->nomor_pengajuan,
            'file_bukti' => null
        ]);

        $this->update(['settlement_id' => $settlement->id]);

        return $settlement;
    }
}