<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerPosition extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'position_title',
        // 'position_slug',
        'department',
        'job_type',
        'description',
        'requirements',
        'benefits',
        'salary_range',
        'location',
        'status', // 'open', 'closed', 'draf'
        'posted_date',
        'deadline',
        'image_url'
    ];
    
    protected $casts = [
        'posted_date' => 'date',
        'deadline' => 'date',
    ];
    
    // Scope untuk lowongan aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // Scope untuk lowongan yang belum expired
    public function scopeNotExpired($query)
    {
        return $query->where('deadline', '>=', now());
    }
    
    // Accessor untuk format deadline
    public function getFormattedDeadlineAttribute()
    {
        return $this->deadline ? $this->deadline->format('d M Y') : null;
    }
    
    // Relasi
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'position_id');
    }
    
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'posisi_dilamar_id');
    }
}