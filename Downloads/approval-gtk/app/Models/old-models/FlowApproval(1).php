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
        'department_id',
        'urutan',
        'role_level_id',
        'nama_step',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'urutan' => 'integer',
        'kategori_pengajuan_id' => 'integer',
        'department_id' => 'integer',
        'role_level_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function kategoriPengajuan()
    {
        return $this->belongsTo(KategoriPengajuan::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function roleLevel()
    {
        return $this->belongsTo(RoleLevel::class);
    }

    public function progressApprovals()
    {
        return $this->hasMany(ProgressApproval::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByKategoriAndDepartment($query, $kategoriId, $departmentId)
    {
        return $query->where('kategori_pengajuan_id', $kategoriId)
                    ->where('department_id', $departmentId);
    }

    public function scopeOrderedByUrutan($query)
    {
        return $query->orderBy('urutan');
    }

    // Static methods
    public static function getWorkflowSteps($kategoriId, $departmentId)
    {
        return static::with(['roleLevel'])
            ->byKategoriAndDepartment($kategoriId, $departmentId)
            ->aktif()
            ->orderedByUrutan()
            ->get();
    }

    public static function getTotalSteps($kategoriId, $departmentId)
    {
        return static::byKategoriAndDepartment($kategoriId, $departmentId)
            ->aktif()
            ->count();
    }
}

