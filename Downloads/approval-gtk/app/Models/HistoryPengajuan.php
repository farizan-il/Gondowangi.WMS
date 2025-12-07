<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HistoryPengajuan extends Model
{
    use HasFactory;

    protected $table = 'HistoryPengajuan';
    
    protected $fillable = [
        'pengajuan_id',
        'action',
        'status_before',
        'status_after',
        'actor_id',
        'actor_name',
        'actor_department',
        'description',
        'catatan',
        'step_name',
        'urutan_step',
        'is_read'
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'actor_id' => 'integer',
        'urutan_step' => 'integer',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function actor()
    {
        return $this->belongsTo(Karyawan::class, 'actor_id');
    }
    
    // Scopes untuk notifikasi
    public function scopeForRequester($query, $requesterId)
    {
        return $query->whereHas('pengajuan', function($q) use ($requesterId) {
            $q->where('requester_id', $requesterId);
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeApprovalUpdates($query)
    {
        return $query->where('action', 'status_update');
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Helper methods untuk notifikasi
    public function getNotificationTitle()
    {
        $titles = [
            'approved' => $this->isLastStep() ? '🎉 Pengajuan Disetujui Lengkap!' : '✅ Pengajuan Disetujui - ' . $this->step_name,
            'rejected' => '❌ Pengajuan Ditolak',
            'revision' => '📝 Pengajuan Perlu Revisi'
        ];

        return $titles[$this->status_after] ?? '📋 Update Pengajuan';
    }

    public function getNotificationMessage()
    {
        $pengajuan = $this->pengajuan;
        
        if (!$pengajuan) {
            return $this->description ?? 'Pengajuan telah diperbarui.';
        }
        
        if ($this->status_after === 'approved' && $this->isLastStep()) {
            return "Pengajuan {$pengajuan->nomor_pengajuan} telah disetujui lengkap oleh semua approver. Akan diproses oleh Tim Finance.";
        } elseif ($this->status_after === 'approved') {
            return "Pengajuan {$pengajuan->nomor_pengajuan} telah disetujui oleh {$this->actor_name}. {$this->step_name} selesai.";
        } elseif ($this->status_after === 'rejected') {
            return "Pengajuan {$pengajuan->nomor_pengajuan} ditolak oleh {$this->actor_name}. Periksa catatan untuk detail.";
        } elseif ($this->status_after === 'revision') {
            return "Pengajuan {$pengajuan->nomor_pengajuan} memerlukan revisi dari {$this->actor_name}. Silakan perbaiki dan ajukan kembali.";
        }

        return $this->description ?? "Pengajuan {$pengajuan->nomor_pengajuan} telah diperbarui.";
    }

    public function getTimeAgo()
    {
        if (!$this->created_at) {
            return 'Tidak diketahui';
        }
        
        return $this->created_at->diffForHumans();
    }

    public function getNotificationIcon()
    {
        $icons = [
            'approved' => $this->isLastStep() ? 'icon-check-circle' : 'icon-check',
            'rejected' => 'icon-x-circle',
            'revision' => 'icon-edit'
        ];

        return $icons[$this->status_after] ?? 'icon-bell';
    }

    public function getNotificationTypeClass()
    {
        $classes = [
            'approved' => $this->isLastStep() ? 'text-success' : 'text-warning',
            'rejected' => 'text-danger',
            'revision' => 'text-info'
        ];

        return $classes[$this->status_after] ?? 'text-muted';
    }

    public function getNotificationType()
    {
        if ($this->status_after === 'approved' && $this->isLastStep()) {
            return 'final_approved';
        } elseif ($this->status_after === 'approved') {
            return 'partial_approved';
        } elseif ($this->status_after === 'rejected') {
            return 'rejected';
        } elseif ($this->status_after === 'revision') {
            return 'revision';
        }

        return 'default';
    }

    private function isLastStep()
    {
        $pengajuan = $this->pengajuan;
        return $pengajuan && $pengajuan->current_step >= $pengajuan->total_step;
    }

    // Helper method untuk membuat history
    public static function createHistory($pengajuanId, $action, $statusBefore, $statusAfter, $actorId, $description = null, $catatan = null, $stepName = null, $urutanStep = null)
    {
        try {
            $actor = Karyawan::find($actorId);
            
            return self::create([
                'pengajuan_id' => $pengajuanId,
                'action' => $action, // Perbaikan: gunakan parameter action yang dikirim
                'status_before' => $statusBefore,
                'status_after' => $statusAfter,
                'actor_id' => $actorId,
                'actor_name' => $actor ? $actor->nama : 'Unknown',
                'actor_department' => $actor && $actor->department ? $actor->department->nama : null,
                'description' => $description,
                'catatan' => $catatan,
                'step_name' => $stepName,
                'urutan_step' => $urutanStep,
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating history: ' . $e->getMessage());
            return null;
        }
    }
    
    // Method untuk format tanggal yang lebih baik
    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return 'Tidak diketahui';
        }
        
        $now = Carbon::now();
        $diff = $this->created_at->diffInMinutes($now);
        
        if ($diff < 1) {
            return 'Baru saja';
        } elseif ($diff < 60) {
            return $diff . ' menit yang lalu';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' jam yang lalu';
        } else {
            $days = floor($diff / 1440);
            return $days . ' hari yang lalu';
        }
    }
}