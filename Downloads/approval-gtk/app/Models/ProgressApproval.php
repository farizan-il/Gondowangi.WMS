<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressApproval extends Model
{
    use HasFactory;

   protected $table = 'ProgressApproval';
    
    protected $fillable = [
        'pengajuan_id',
        'flow_approval_id',
        'requester_id',        // Tambahan: ID karyawan yang mengajukan
        'approver_id',         // ID karyawan yang akan/sudah approve
        'step_name',
        'urutan',
        'status',              // pending, approved, rejected, waiting, cancelled
        'step_type',           // approval, notification, etc
        'tanggal_approval',
        'catatan',
        'settlement_id', // null jika pengajunya blm sampai ke tahap settlement
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
    
    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }

    public function flowApproval()
    {
        return $this->belongsTo(FlowApproval::class);
    }

    public function requester()
    {
        return $this->belongsTo(Karyawan::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForApprover($query, $approverId)
    {
        return $query->where('approver_id', $approverId);
    }

    public function scopeCurrentStep($query)
    {
        return $query->where('status', 'waiting');
    }

    // Methods
    public function canBeApprovedBy($karyawanId)
    {
        return $this->approver_id == $karyawanId && $this->status == 'waiting';
    }

    public function approve($approverId, $catatan = null)
    {
        $this->update([
            'status' => 'approved',
            'approver_id' => $approverId,
            'tanggal_approval' => now(),
            'catatan' => $catatan
        ]);

        // Update pengajuan ke step selanjutnya
        $this->moveToNextStep();
    }

    public function reject($approverId, $catatan)
    {
        $this->update([
            'status' => 'rejected',
            'approver_id' => $approverId,
            'tanggal_approval' => now(),
            'catatan' => $catatan
        ]);

        // Update status pengajuan menjadi rejected
        $this->pengajuan->update([
            'status_pengajuan' => 'rejected'
        ]);
    }

    private function moveToNextStep()
    {
        $pengajuan = $this->pengajuan;
        $nextStep = ProgressApproval::where('pengajuan_id', $pengajuan->id)
            ->where('urutan', $this->urutan + 1)
            ->first();

        if ($nextStep) {
            // Ada step selanjutnya
            $nextStep->update(['status' => 'waiting']);
            $pengajuan->update(['current_step' => $nextStep->urutan]);
        } else {
            // Sudah step terakhir
            $pengajuan->update([
                'status_pengajuan' => 'approved',
                'current_step' => $pengajuan->total_step
            ]);

            // Buat settlement jika diperlukan
            if ($pengajuan->is_settlement_required) {
                $this->createSettlement($pengajuan);
            }
        }
    }

    private function createSettlement($pengajuan)
    {
        // Logic untuk membuat settlement (sama seperti di controller)
        try {
            $tahun = date('Y');
            $bulan = date('m');
            
            $lastSettlement = Settlement::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count();
            
            $sequence = str_pad($lastSettlement + 1, 4, '0', STR_PAD_LEFT);
            $nomorSettlement = 'STL/' . $tahun . $bulan . '/' . $sequence;

            $settlement = Settlement::create([
                'pengajuan_id' => $pengajuan->id,
                'nomor_settlement' => $nomorSettlement,
                'tanggal_settlement' => now(),
                'total_actual' => 0,
                'selisih' => $pengajuan->nominal_pengajuan,
                'status_settlement' => 'pending',
                'catatan_settlement' => 'Settlement otomatis dibuat dari pengajuan: ' . $pengajuan->nomor_pengajuan,
                'file_bukti' => null,
                'current_step' => 1,
                'total_step' => 1
            ]);

            $pengajuan->update([
                'settlement_id' => $settlement->id,
                'status_pengajuan' => 'settlement_created'
            ]);

        } catch (\Exception $e) {
            \Log::error("Error creating settlement: " . $e->getMessage());
        }
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'badge-secondary',
            'waiting' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-dark',
            default => 'badge-secondary'
        };
    }

    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'waiting' => 'Dalam Proses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => 'Status Tidak Dikenal'
        };
    }
}