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

    // Helper methods
    public function requiresSettlement()
    {
        return $this->settlement == 1;
    }
}