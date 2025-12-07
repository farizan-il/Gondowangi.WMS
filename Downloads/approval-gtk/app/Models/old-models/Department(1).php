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
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Relationships
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
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
}