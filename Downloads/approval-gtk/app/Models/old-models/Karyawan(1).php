<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// class Karyawan extends Authenticatable
// {
//     use HasFactory, Notifiable;

//     protected $table = 'Karyawan';
//     protected $primaryKey = 'id';
//     public $timestamps = true;
    
//     protected $fillable = [
//         'nik',
//         'nama',
//         'email',
//         'password',
//         'department_id',
//         'role_level_id',
//         'jabatan',
//         'atasan_id',
//         'status'
//     ];

//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     protected $casts = [
//         'department_id' => 'integer',
//         'atasan_id' => 'integer',
//         'role_level_id' => 'integer',
//         'created_at' => 'datetime',
//         'updated_at' => 'datetime'
//     ];

//     // Override authentication identifier
//     public function getAuthIdentifierName()
//     {
//         return 'nik';
//     }
    
//     public function roleLevel()
//     {
//         return $this->belongsTo(RoleLevel::class);
//     }

//     public function getAuthIdentifier()
//     {
//         return $this->getAttribute($this->getAuthIdentifierName());
//     }

//     public function getAuthPassword()
//     {
//         return $this->password;
//     }

//     public function getRememberToken()
//     {
//         return $this->remember_token;
//     }

//     public function setRememberToken($value)
//     {
//         $this->remember_token = $value;
//     }

//     public function getRememberTokenName()
//     {
//         return 'remember_token';
//     }

//     // Relationships
//     public function department()
//     {
//         return $this->belongsTo(Department::class);
//     }

//     public function atasan()
//     {
//         return $this->belongsTo(Karyawan::class, 'atasan_id');
//     }

//     public function bawahan()
//     {
//         return $this->hasMany(Karyawan::class, 'atasan_id');
//     }

//     public function pengajuan()
//     {
//         return $this->hasMany(Pengajuan::class, 'requester_id');
//     }

//     public function historyPengajuan()
//     {
//         return $this->hasMany(HistoryPengajuan::class, 'approver_id');
//     }

//     public function progressApprovals()
//     {
//         return $this->hasMany(ProgressApproval::class, 'approver_id');
//     }

//     public function notifikasiDiterima()
//     {
//         return $this->hasMany(Notifikasi::class, 'penerima_id');
//     }

//     public function notifikasiDikirim()
//     {
//         return $this->hasMany(Notifikasi::class, 'pengirim_id');
//     }

//     // Scopes
//     public function scopeAktif($query)
//     {
//         return $query->where('status', 'aktif');
//     }

//     public function scopeByDepartment($query, $departmentId)
//     {
//         return $query->where('department_id', $departmentId);
//     }

//     // Helper methods
//     public function isAtasan($karyawanId)
//     {
//         return $this->bawahan()->where('id', $karyawanId)->exists();
//     }

//     public function getFullNameAttribute()
//     {
//         return $this->nama . ' (' . $this->nik . ')';
//     }

//     public function hasBawahan()
//     {
//         return $this->bawahan()->count() > 0;
//     }
// }


class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'Karyawan';
    
    protected $fillable = [
        'nama',
        'email',
        'department_id',
        'role_level_id', // Tambahan untuk relasi ke RoleLevel
        'atasan_id', // ID karyawan yang menjadi atasan langsung
        'status',
        'phone',
        'alamat'
    ];

    protected $casts = [
        'department_id' => 'integer',
        'role_level_id' => 'integer',
        'atasan_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function roleLevel()
    {
        return $this->belongsTo(RoleLevel::class);
    }

    public function atasan()
    {
        return $this->belongsTo(Karyawan::class, 'atasan_id');
    }

    public function bawahan()
    {
        return $this->hasMany(Karyawan::class, 'atasan_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id');
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByRoleLevel($query, $roleLevelId)
    {
        return $query->where('role_level_id', $roleLevelId);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper methods untuk workflow
    public function getHierarchyChain()
    {
        $chain = collect([$this]);
        $current = $this;
        
        while ($current->atasan && $current->atasan_id != $current->id) {
            $current = $current->atasan;
            $chain->push($current);
        }
        
        return $chain;
    }

    public function isDepartmentHead()
    {
        return $this->roleLevel && 
               in_array($this->roleLevel->nama, ['GM', 'Head', 'Manager']) &&
               $this->atasan_id == null;
    }

    public function isFinanceStaff()
    {
        return $this->department_id == 2 && // Finance department
               $this->roleLevel && 
               $this->roleLevel->level == 1;
    }

    public function isFinanceSupervisor()
    {
        return $this->department_id == 2 && 
               $this->roleLevel && 
               $this->roleLevel->level == 2;
    }

    public function isFinanceManager()
    {
        return $this->department_id == 2 && 
               $this->roleLevel && 
               $this->roleLevel->level == 3;
    }

    public function isDireksi()
    {
        return $this->department_id == 1 && // Direksi department
               $this->roleLevel && 
               in_array($this->roleLevel->nama, ['Direktur', 'CEO', 'President']);
    }
}