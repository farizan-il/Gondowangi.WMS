<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPengajuan extends Model
{
    use HasFactory;

    protected $table = 'KategoriPengajuan';
    
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'icon',
        'warna',
        'status',
        'settlement' // 0 = tidak perlu settlement, 1 = perlu settlement
    ];

    protected $casts = [
        'settlement' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function formFields()
    {
        return $this->hasMany(FormField::class);
    }

    public function flowApprovals()
    {
        return $this->hasMany(FlowApproval::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeRequireSettlement($query)
    {
        return $query->where('settlement', true);
    }

    // Scope untuk kategori yang dapat diakses oleh user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('flowApprovals', function($subQuery) use ($userId) {
            $subQuery->where('karyawan_id', $userId)
                     ->orWhere('requester_id', $userId)
                     ->orWhere('user_id', $userId); // Sesuaikan dengan nama kolom di tabel FlowApproval
        });
    }

    // Scope untuk kategori berdasarkan department dan role
    public function scopeForDepartmentAndRole($query, $departmentId, $roleLevelId)
    {
        return $query->whereHas('flowApprovals', function($subQuery) use ($departmentId, $roleLevelId) {
            $subQuery->where('department_id', $departmentId)
                     ->orWhere('role_level_id', $roleLevelId);
        });
    }

    // Helper methods
    public function requiresSettlement()
    {
        return $this->settlement == 1;
    }

    // Method untuk check apakah user bisa mengakses kategori ini
    public function canBeAccessedByUser($userId)
    {
        return $this->flowApprovals()
            ->where(function($query) use ($userId) {
                $query->where('karyawan_id', $userId)
                      ->orWhere('requester_id', $userId)
                      ->orWhere('user_id', $userId);
            })
            ->exists();
    }
}