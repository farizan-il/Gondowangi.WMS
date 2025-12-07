<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'nominal_sblm_revisi',
        'mata_uang',
        'tanggal_pengajuan',
        'tanggal_kebutuhan',
        'status_pengajuan', // 'proses', 'approved', 'rejected', 'completed', 'settlement_created'
        'current_step',
        'total_step',
        'catatan_requester',
        'file_pendukung',
        'statuspembayaran', // 'Menunggu', 'Ditolak', 'Dibayarkan'
        'is_settlement_required',
        'settlement_id',
        'is_intervened_by_finance',
        'finance_intervention_date', 
        'finance_intervention_by',
        'catatan_intervensi_finance',
        'argo' //defaultnya 21 hari
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
        'is_intervened_by_finance' => 'boolean', 
        'finance_intervention_date' => 'datetime',
        'finance_intervention_by' => 'integer', 
        'argo' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function financeInterventionBy()
    {
        return $this->belongsTo(Karyawan::class, 'finance_intervention_by');
    }
    
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
    
    public function emailNotificationLogs()
    {
        return $this->hasMany(EmailNotificationLog::class);
    }
    
    public function settlements()
    {
        return $this->hasMany(Settlement::class, 'pengajuan_id');
    }
    
    
    // Relasi ke Transaction Request (one-to-one)
    public function transactionRequest()
    {
        return $this->hasOne(TransactionRequest::class, 'pengajuan_id');
    }
    
    // Relasi ke Transaction Request Group melalui Transaction Request
    public function transactionRequestGroup()
    {
        return $this->hasOneThrough(
            TransactionRequestGroup::class,
            TransactionRequest::class,
            'pengajuan_id', // Foreign key on transaction_requests table
            'id', // Foreign key on transaction_request_groups table
            'id', // Local key on pengajuans table
            'tr_group_id' // Local key on transaction_requests table
        );
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
        return $query->where('status_pengajuan', 'approved');
    }
    
    // Helper method untuk mendapatkan settlement aktif
    public function getActiveSettlement()
    {
        return $this->settlements()
            ->whereIn('status_settlement', ['proses', 'approved'])
            ->orderBy('created_at', 'desc')
            ->first();
    }
    
    // Helper method untuk mengecek apakah pengajuan sudah selesai
    public function isCompleted()
    {
        return $this->status_pengajuan === 'completed';
    }
    
    // Helper method untuk mengecek apakah email settlement notification sudah dikirim
    public function hasSettlementEmailSent()
    {
        return $this->emailNotificationLogs()
            ->where('status', 'success')
            ->where('message', 'like', '%Settlement approved notification%')
            ->exists();
    }

    public function scopeCanCreateSettlement($query)
    {
        return $query->where('status_pengajuan', 'approved')
                    ->where('is_settlement_required', true)
                    ->whereNull('settlement_id');
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

    /**
     * Cek apakah pengajuan sudah sepenuhnya disetujui
     */
    public function isFullyApproved()
    {
        return $this->status_pengajuan === 'approved' && 
               $this->current_step >= $this->total_step;
    }

    /**
     * Cek apakah settlement sudah dibuat
     */
    public function hasSettlement()
    {
        return !is_null($this->settlement_id) && !is_null($this->settlement);
    }

    /**
     * Cek apakah bisa membuat settlement
     */
    public function canCreateSettlement()
    {
        return $this->status_pengajuan === 'approved' && 
               $this->is_settlement_required && 
               !$this->hasSettlement();
    }

    /**
     * Cek apakah settlement bisa diedit
     */
    public function canEditSettlement()
    {
        if (!$this->hasSettlement()) {
            return false;
        }

        $allowedStatuses = ['draft', 'proses', 'submitted', 'revision'];
        return in_array($this->settlement->status_settlement, $allowedStatuses);
    }

    /**
     * Get status pengajuan dengan label yang lebih user-friendly
     */
    public function getStatusLabel()
    {
        return match($this->status_pengajuan) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'settlement_created' => 'Settlement Dibuat',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status_pengajuan)
        };
    }

    /**
     * Get status badge class untuk UI
     */
    public function getStatusBadgeClass()
    {
        return match($this->status_pengajuan) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'completed' => 'badge-info',
            'settlement_created' => 'badge-primary',
            'cancelled' => 'badge-secondary',
            default => 'badge-light'
        };
    }

    /**
     * Cek apakah pengajuan dalam proses approval
     */
    public function isInProcess()
    {
        return in_array($this->status_pengajuan, ['pending', 'revision']);
    }

    /**
     * Cek apakah pengajuan sudah final (tidak bisa diubah lagi)
     */
    public function isFinal()
    {
        return in_array($this->status_pengajuan, ['completed', 'cancelled']);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage()
    {
        if ($this->total_step <= 0) {
            return 0;
        }
        
        return ($this->current_step / $this->total_step) * 100;
    }

    /**
     * Update status pengajuan ke settlement_created ketika settlement dibuat
     */
    public function markSettlementCreated($settlementId)
    {
        $this->update([
            'settlement_id' => $settlementId,
            'status_pengajuan' => 'settlement_created'
        ]);
    }

    /**
     * Kembalikan status ke approved ketika settlement dihapus
     */
    public function revertToApproved()
    {
        $this->update([
            'settlement_id' => null,
            'status_pengajuan' => 'approved'
        ]);
    }

    /**
     * Get detail pengajuan dengan format yang lebih mudah dibaca
     */
    public function getFormattedDetails()
    {
        return $this->detailPengajuan->map(function($detail) {
            $formField = $detail->formField;
            
            return [
                'label' => $formField->label ?? 'Field',
                'type' => $formField->type ?? 'text',
                'value' => $detail->nilai,
                'formatted_value' => $this->formatDetailValue($detail->nilai, $formField->type ?? 'text')
            ];
        });
    }

    /**
     * Format nilai detail berdasarkan tipe field
     */
    private function formatDetailValue($value, $type)
    {
        if (empty($value)) {
            return '-';
        }

        return match($type) {
            'currency' => $this->mata_uang . ' ' . number_format($value, 0, ',', '.'),
            'date' => \Carbon\Carbon::parse($value)->format('d/m/Y'),
            'file' => '<a href="/storage/' . $value . '" target="_blank" class="btn btn-sm btn-outline-primary">Lihat File</a>',
            default => $value
        };
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
    }

    /**
     * Scope untuk pengajuan yang membutuhkan settlement
     */
    public function scopeNeedSettlement($query)
    {
        return $query->where('status_pengajuan', 'approved')
                    ->where('is_settlement_required', true)
                    ->whereNull('settlement_id');
    }

    /**
     * Auto-assign settlement requirement berdasarkan kategori atau nominal
     */
    public function autoAssignSettlementRequirement()
    {
        // Logic untuk menentukan apakah pengajuan membutuhkan settlement
        // Bisa berdasarkan kategori, nominal, atau rules bisnis lainnya
        
        $requiresSettlement = false;
        
        // Contoh: Semua pengajuan dengan nominal > 1 juta membutuhkan settlement
        if ($this->nominal_pengajuan > 1000000) {
            $requiresSettlement = true;
        }
        
        // Contoh: Kategori tertentu selalu membutuhkan settlement
        $settlementRequiredCategories = ['Perjalanan Dinas', 'Operasional', 'Pembelian'];
        if (in_array($this->kategoriPengajuan->nama ?? '', $settlementRequiredCategories)) {
            $requiresSettlement = true;
        }
        
        $this->update(['is_settlement_required' => $requiresSettlement]);
        
        return $requiresSettlement;
    }
    
    public function isArgoActive()
    {
        return $this->argo && Carbon::parse($this->argo)->isFuture();
    }

    /**
     * Helper method untuk mendapatkan sisa hari argo
     */
    public function getRemainingArgoDays()
    {
        if (!$this->argo) {
            return null;
        }

        $argoDate = Carbon::parse($this->argo);
        $today = Carbon::today();

        if ($argoDate->isPast()) {
            return 0;
        }

        return $today->diffInDays($argoDate);
    }

    /**
     * Helper method untuk mendapatkan status argo
     */
    public function getArgoStatus()
    {
        if (!$this->argo) {
            return 'inactive';
        }

        $argoDate = Carbon::parse($this->argo);
        $today = Carbon::today();

        if ($argoDate->isPast()) {
            return 'expired';
        } elseif ($today->diffInDays($argoDate) <= 3) {
            return 'warning'; // 3 hari terakhir
        } else {
            return 'active';
        }
    }

    /**
     * Scope untuk mendapatkan pengajuan dengan argo aktif
     */
    public function scopeWithActiveArgo($query)
    {
        return $query->whereNotNull('argo')
                    ->where('argo', '>', Carbon::today());
    }

    /**
     * Scope untuk mendapatkan pengajuan dengan argo yang akan expired dalam X hari
     */
    public function scopeArgoExpiringSoon($query, $days = 3)
    {
        return $query->whereNotNull('argo')
                    ->whereBetween('argo', [
                        Carbon::today(),
                        Carbon::today()->addDays($days)
                    ]);
    }

    /**
     * Scope untuk mendapatkan pengajuan dengan argo yang sudah expired
     */
    public function scopeArgoExpired($query)
    {
        return $query->whereNotNull('argo')
                    ->where('argo', '<', Carbon::today());
    }
}