<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'posisi_dilamar_id',
        'nama',
        'email',
        'kota_domisili',
        'tanggal_lahir',
        'no_telepon',
        'cv',
        'foto',
        
        'pendidikan_formal',
        'pengalaman_kerja',
        
        'gaji_terakhir',
        'tunjangan_terakhir',
        'fasilitas_terakhir',
        'fasilitas_lain',
        
        'jabatan_diminati',
        'gaji_diharapkan',
        'tunjangan_diharapkan',
        'fasilitas_diharapkan',
        'jaminan_diharapkan',
        'lain_diharapkan',
        
        'informasi_tambahan',
        'status' // 'Pending','Lanjut','Ditolak','Diterima','Simpan'
    ];

    protected $casts = [
        'pendidikan_formal' => 'array',
        'pengalaman_kerja' => 'array',
    ];
    
    public function posisilamaran()
    {
        return $this->belongsTo(CareerPosition::class, 'posisi_dilamar_id');
    }

    public function credentials()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
