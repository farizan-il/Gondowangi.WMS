<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'Karyawan';
    
    protected $fillable = [
        'nama',
        'email',
        'password',
        'department_id',
        'nik',
        'role_level_id',
        'atasan_id',
        'golongan_id',
        'status',
        'phone',
        'alamat'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'role_level_id' => 'integer',
        'atasan_id' => 'integer',
        'golongan_id' => 'integer',
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
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
    
    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'requester_id');
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

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('nik', 'like', '%' . $search . '%');
        });
    }

    // Static methods untuk validasi
    public static function isEmailExists($email, $excludeId = null)
    {
        $query = self::where('email', strtolower(trim($email)));
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public static function isNikExists($nik, $excludeId = null)
    {
        $query = self::where('nik', trim($nik));
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    // Mutators
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = ucwords(strtolower(trim($value)));
    }

    public function setNikAttribute($value)
    {
        $this->attributes['nik'] = trim($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => 'success',
            'non-aktif' => 'danger',
            'cuti' => 'warning',
            'resign' => 'secondary'
        ];
        
        return $badges[$this->status] ?? 'secondary';
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

    public function canCreateNewPengajuan()
    {
        $pengajuanBelumSelesai = $this->pengajuan()
            ->whereIn('status_pengajuan', ['proses', 'settlement_created'])
            ->whereNotIn('statuspembayaran', ['Dibayarkan'])
            ->count();
            
        return $pengajuanBelumSelesai < 3;
    }
    
    // Method untuk mendapatkan jumlah pengajuan yang belum selesai
    public function getPengajuanBelumSelesaiCount()
    {
        return $this->pengajuan()
            ->whereIn('status_pengajuan', ['proses', 'settlement_created'])
            ->whereNotIn('statuspembayaran', ['Dibayarkan'])
            ->count();
    }
    
    // Method untuk mendapatkan pengajuan yang belum selesai
    public function getPengajuanBelumSelesai()
    {
        return $this->pengajuan()
            ->whereIn('status_pengajuan', ['proses', 'settlement_created'])
            ->whereNotIn('statuspembayaran', ['Dibayarkan'])
            ->with('kategoriPengajuan')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Method untuk reset password
    public function resetPassword($newPassword = 'Gondowangi-123')
    {
        $this->password = $newPassword;
        $this->password_changed_at = null; // Force password change on next login
        $this->save();
    }

    // Method untuk check apakah perlu ganti password
    public function needsPasswordChange()
    {
        return is_null($this->password_changed_at);
    }

    // Method untuk mark password as changed
    public function markPasswordAsChanged()
    {
        $this->password_changed_at = now();
        $this->save();
    }

    // Boot method untuk auto-formatting
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($karyawan) {
            // Auto format data saat create
            $karyawan->email = strtolower(trim($karyawan->email));
            $karyawan->nama = ucwords(strtolower(trim($karyawan->nama)));
            $karyawan->nik = trim($karyawan->nik);
        });

        static::updating(function ($karyawan) {
            // Auto format data saat update
            if ($karyawan->isDirty('email')) {
                $karyawan->email = strtolower(trim($karyawan->email));
            }
            if ($karyawan->isDirty('nama')) {
                $karyawan->nama = ucwords(strtolower(trim($karyawan->nama)));
            }
            if ($karyawan->isDirty('nik')) {
                $karyawan->nik = trim($karyawan->nik);
            }
        });
    }
}