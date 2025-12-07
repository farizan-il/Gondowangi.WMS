<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $table = 'Settlement';

    protected $fillable = [
        'pengajuan_id',
        'nomor_settlement',
        'tanggal_settlement',
        'total_actual',
        'selisih',
        'status_settlement', //'draft','approved','proses','revisi'
        'status_realisasi', // 'balance','over','under','proses'
        'status', // 'paid', 'proses', 'balance'
        'catatan_settlement',
        'file_bukti',
        'file_bukti_transfer',  // NEW
        'tanggal_transfer',     // NEW
        'catatan_transfer',     // NEW
        'current_step',
        'total_step',
        'is_intervened_by_finance',
        'finance_intervention_date',
        'finance_intervention_by',
        'original_keterangan',
        'original_nominal',
        'original_kategori_biaya'
        
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'tanggal_settlement' => 'datetime',
        'tanggal_transfer' => 'date',  // NEW
        'total_actual' => 'decimal:2',
        'selisih' => 'decimal:2',
        'file_bukti' => 'array',
        'current_step' => 'integer',
        'total_step' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'original_nominal' => 'decimal:2',
        'is_intervened_by_finance' => 'boolean',
        'finance_intervention_date' => 'datetime',
        'finance_intervention_by' => 'integer',
    ];
    
    public function financeInterventionBy()
    {
        return $this->belongsTo(Karyawan::class, 'finance_intervention_by');
    }

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function details()
    {
        return $this->hasMany(DetailSettlement::class, 'settlement_id');
    }
    
    // Tambahkan method ini di model Settlement
    public function transactionRequest()
    {
        return $this->hasOne(TransactionRequest::class, 'settlement_id');
    }
    
    public function emailNotificationLogs()
    {
        return $this->hasMany(EmailNotificationLog::class, 'settlement_id');
    }
    
    public function progressApprovals()
    {
        return $this->hasMany(ProgressApproval::class, 'settlement_id');
    }
    
    public function hasNegativeBalance()
    {
        return $this->selisih < 0;
    }
    
    public function getAbsoluteSelisih()
    {
        return abs($this->selisih);
    }
    
    // NEW: Helper method untuk get approver berdasarkan urutan
    public function getApproverByStep($step)
    {
        $progressApproval = $this->progressApprovals()
            ->where('urutan', $step)
            ->first();
            
        if (!$progressApproval) {
            return null;
        }
        
        return Karyawan::find($progressApproval->approver_id);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_settlement', $status);
    }
    
    public function isLastStep()
    {
        return $this->current_step >= $this->total_step;
    }
    
    // Helper method untuk mendapatkan status approval saat ini
    public function getCurrentApprovalStatus()
    {
        if ($this->isLastStep() && $this->status_settlement === 'approved') {
            return 'fully_approved';
        } elseif ($this->status_settlement === 'rejected') {
            return 'rejected';
        } elseif ($this->status_settlement === 'revision') {
            return 'needs_revision';
        } else {
            return 'in_progress';
        }
    }
    
    // Helper method untuk mendapatkan approver step terakhir
    public function getLastStepApprover()
    {
        return $this->getApproverByStep($this->total_step);
    }
    
    // Helper method untuk mengecek apakah email notifikasi sudah pernah dikirim
    public function hasEmailNotificationSent()
    {
        return $this->emailNotificationLogs()
            ->where('status', 'success')
            ->where('message', 'like', '%Settlement approved notification%')
            ->exists();
    }

    public function scopePending($query)
    {
        return $query->where('status_settlement', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_settlement', 'completed');
    }

    // Helper methods
    public function calculateTotalActual()
    {
        return $this->details()->sum('nominal');
    }

    public function calculateSelisih()
    {
        $totalActual = $this->calculateTotalActual();
        $nominalPengajuan = $this->pengajuan->nominal_pengajuan ?? 0;
        
        return $nominalPengajuan - $totalActual;
    }

    public function updateTotals()
    {
        $totalActual = $this->calculateTotalActual();
        $selisih = $this->calculateSelisih();
        
        $this->update([
            'total_actual' => $totalActual,
            'selisih' => $selisih
        ]);
    }
    
    public function hasBuktiTransfer()
    {
        return !empty($this->file_bukti_transfer) && \Storage::disk('public')->exists($this->file_bukti_transfer);
    }

    // NEW: Get URL bukti transfer
    public function getBuktiTransferUrl()
    {
        if ($this->hasBuktiTransfer()) {
            return asset('storage/' . $this->file_bukti_transfer);
        }
        return null;
    }

    // NEW: Check apakah settlement memerlukan bukti transfer
    public function requiresBuktiTransfer()
    {
        return $this->selisih > 0;
    }
}