<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $table = 'settlement';

    protected $fillable = [
        'pengajuan_id',
        'nomor_settlement',
        'tanggal_settlement',
        'total_actual',
        'selisih',
        'status_settlement',
        'catatan_settlement',
        'file_bukti',
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'tanggal_settlement' => 'datetime',
        'total_actual' => 'decimal:2',
        'selisih' => 'decimal:2',
        'file_bukti' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function details()
    {
        return $this->hasMany(DetailSettlement::class, 'settlement_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_settlement', $status);
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
}