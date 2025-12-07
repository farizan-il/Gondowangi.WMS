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
        'approver_id',
        'status',
        'tanggal_approval',
        'catatan',
        'delegasi_dari'
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'flow_approval_id' => 'integer',
        'approver_id' => 'integer',
        'tanggal_approval' => 'datetime',
        'delegasi_dari' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function flowApproval()
    {
        return $this->belongsTo(FlowApproval::class);
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }

    public function delegasiDari()
    {
        return $this->belongsTo(Karyawan::class, 'delegasi_dari');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
