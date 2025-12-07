<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'Department';
    
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'parent_id', // untuk struktur hierarki department
        'status'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function roleLevel()
    {
        return $this->hasMany(RoleLevel::class);
    }

    public function flowApprovals()
    {
        return $this->hasMany(FlowApproval::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Static methods
    public static function getFinanceDepartment()
    {
        return static::where('nama', 'Finance')
            ->orWhere('kode', 'FIN')
            ->aktif()
            ->first();
    }

    public static function getDireksiDepartment()
    {
        return static::where('nama', 'Direksi')
            ->orWhere('kode', 'DIR')
            ->aktif()
            ->first();
    }

    // Helper methods
    public function getHeadOfDepartment()
    {
        return $this->karyawan()
            ->whereHas('roleLevel', function($query) {
                $query->where('nama', 'LIKE', '%head%')
                    ->orWhere('nama', 'LIKE', '%manager%')
                    ->orWhere('nama', 'LIKE', '%gm%');
            })
            ->aktif()
            ->first();
    }
}