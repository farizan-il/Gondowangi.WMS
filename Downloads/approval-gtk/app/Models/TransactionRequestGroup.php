<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRequestGroup extends Model
{
    use HasFactory;

    protected $table = 'transaction_request_groups';

    protected $fillable = [
        'tr_number',
        'status', // 'pending', 'processing', 'completed'
        'notes',
        'created_by',
        'processed_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Generate nomor TR otomatis
    public static function generateTRNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTR = self::whereYear('created_at', $year)
             ->whereMonth('created_at', $month)
             ->orderBy('id', 'desc')
             ->first();
        
        $sequence = $lastTR ? (int)substr($lastTR->tr_number, -4) + 1 : 1;
        
        return 'TR/' . $year . '/' . $month . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relasi ke Transaction Requests
    public function transactionRequests()
    {
        return $this->hasMany(TransactionRequest::class, 'tr_group_id');
    }

    // Relasi ke pengajuan melalui transaction requests
    public function pengajuans()
    {
        return $this->hasManyThrough(Pengajuan::class, TransactionRequest::class, 'tr_group_id', 'id', 'id', 'pengajuan_id');
    }

    // Relasi ke karyawan yang membuat
    public function createdBy()
    {
        return $this->belongsTo(Karyawan::class, 'created_by');
    }

    // Relasi ke karyawan yang memproses
    public function processedBy()
    {
        return $this->belongsTo(Karyawan::class, 'processed_by');
    }

    // Check apakah semua pengajuan sudah paid
    public function isAllPaid()
    {
        return $this->transactionRequests()->where('status', '!=', 'paid')->count() === 0;
    }
    
    public function settlements()
    {
        return $this->hasManyThrough(
            Settlement::class, 
            TransactionRequest::class, 
            'tr_group_id', 
            'id', 
            'id', 
            'settlement_id'
        );
    }

    // Auto update status TR berdasarkan status pengajuan
    // public function updateStatus()
    // {
    //     $totalRequests = $this->transactionRequests()->count();
    //     $paidRequests = $this->transactionRequests()->where('status', 'paid')->count();
    //     $rejectedRequests = $this->transactionRequests()->where('status', 'rejected')->count();

    //     if ($paidRequests === $totalRequests && $totalRequests > 0) {
    //         $this->update(['status' => 'completed']);
    //     } elseif ($paidRequests > 0 || $rejectedRequests > 0) {
    //         $this->update(['status' => 'processing']);
    //     } else {
    //         $this->update(['status' => 'pending']);
    //     }
    // }
    
    public function getTotalNominalAttribute()
    {
        $pengajuanTotal = $this->transactionRequests()
            ->whereNotNull('pengajuan_id')
            ->with('pengajuan')
            ->get()
            ->sum(function($tr) {
                return $tr->pengajuan ? $tr->pengajuan->nominal_pengajuan : 0;
            });

        $settlementTotal = $this->transactionRequests()
            ->whereNotNull('settlement_id')
            ->with('settlement')
            ->get()
            ->sum(function($tr) {
                return $tr->settlement ? abs($tr->settlement->selisih) : 0;
            });

        return $pengajuanTotal + $settlementTotal;
    }

    // Get total item yang sudah dibayar
    public function getPaidCount()
    {
        return $this->transactionRequests()->where('status', 'paid')->count();
    }

    // Get total item
    public function getTotalCount()
    {
        return $this->transactionRequests()->count();
    }

    // Get total pengajuan
    public function getPengajuanCount()
    {
        return $this->transactionRequests()->whereNotNull('pengajuan_id')->count();
    }

    // Get total settlement
    public function getSettlementCount()
    {
        return $this->transactionRequests()->whereNotNull('settlement_id')->count();
    }

    // Get breakdown by type
    public function getItemBreakdown()
    {
        return [
            'pengajuan_count' => $this->getPengajuanCount(),
            'settlement_count' => $this->getSettlementCount(),
            'total_count' => $this->getTotalCount(),
            'paid_count' => $this->getPaidCount(),
            'pending_count' => $this->transactionRequests()->where('status', 'waiting')->count(),
            'rejected_count' => $this->transactionRequests()->where('status', 'rejected')->count()
        ];
    }

    // Auto update status TR berdasarkan status semua item
    public function updateStatus()
    {
        $totalRequests = $this->transactionRequests()->count();
        $paidRequests = $this->transactionRequests()->where('status', 'paid')->count();
        $rejectedRequests = $this->transactionRequests()->where('status', 'rejected')->count();
        $waitingRequests = $this->transactionRequests()->where('status', 'waiting')->count();

        if ($paidRequests === $totalRequests && $totalRequests > 0) {
            // Semua sudah dibayar
            $this->update(['status' => 'completed']);
        } elseif ($paidRequests > 0 || $rejectedRequests > 0) {
            // Ada yang sudah diproses (dibayar atau ditolak)
            $this->update(['status' => 'processing']);
        } else {
            // Semua masih menunggu
            $this->update(['status' => 'pending']);
        }
    }

    // Get status text for display
    public function getStatusTextAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'Menunggu';
            case 'processing':
                return 'Diproses';
            case 'completed':
                return 'Selesai';
            default:
                return ucfirst($this->status);
        }
    }

    // Get status class for badge
    public function getStatusClassAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'badge-secondary';
            case 'processing':
                return 'badge-warning';
            case 'completed':
                return 'badge-success';
            default:
                return 'badge-secondary';
        }
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan pembuat
    public function scopeByCreator($query, $createdBy)
    {
        return $query->where('created_by', $createdBy);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    
    
    
    public function getTotalItemsCount()
    {
        $pengajuanCount = $this->transactionRequests()
            ->whereNotNull('pengajuan_id')
            ->count();
            
        $settlementCount = $this->transactionRequests()
            ->whereNotNull('settlement_id')
            ->count();
            
        return $pengajuanCount + $settlementCount;
    }
    
    // TAMBAHAN: Method untuk mendapatkan breakdown count
    public function getItemsBreakdown()
    {
        return [
            'pengajuan' => $this->transactionRequests()->whereNotNull('pengajuan_id')->count(),
            'settlement' => $this->transactionRequests()->whereNotNull('settlement_id')->count(),
            'total' => $this->getTotalItemsCount()
        ];
    }
    
}