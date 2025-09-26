<?php

// app/Models/Pengguna.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'nomor_telepon',
        'alamat',
        'foto_profil',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tanggal_dibuat' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = 'tanggal_dibuat';
    const UPDATED_AT = 'tanggal_diperbarui';

    // Relationships
    public function halaman()
    {
        return $this->hasMany(Halaman::class, 'penulis_id');
    }

    public function lowonganKerja()
    {
        return $this->hasMany(LowonganKerja::class, 'dibuat_oleh');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'kandidat_id');
    }

    public function sesiPengunjung()
    {
        return $this->hasMany(SesiPengunjung::class, 'pengguna_id');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'pengguna_id');
    }

    // Scopes
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}