<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\AuthHelper;

class User extends Authenticatable
{
   use HasFactory, AuthHelper, Notifiable;

    protected $fillable = [
        'nik',
        'email',
        'katasandi',
        'fullName',
        'role', // adminweb, adminkandidat, kandidat
        'is_active',
        'last_password_change', 
        'password_change_count'
    ];

    protected $hidden = [
        'katasandi', 
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_password_change' => 'datetime',
        'password_change_count' => 'integer'
    ];

    // ===== ROLE CONSTANTS =====
    const ROLE_ADMIN_WEB = 'adminweb';
    const ROLE_ADMIN_KANDIDAT = 'adminkandidat';
    const ROLE_KANDIDAT = 'kandidat';

    // ===== ROLE METHODS =====
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdminWeb()
    {
        return $this->hasRole(self::ROLE_ADMIN_WEB);
    }

    public function isAdminKandidat()
    {
        return $this->hasRole(self::ROLE_ADMIN_KANDIDAT);
    }

    public function isKandidat()
    {
        return $this->hasRole(self::ROLE_KANDIDAT);
    }

    public function isAdmin()
    {
        return $this->isAdminWeb() || $this->isAdminKandidat();
    }

    public function getRoleLabel()
    {
        switch ($this->role) {
            case self::ROLE_ADMIN_WEB:
                return 'Admin Web';
            case self::ROLE_ADMIN_KANDIDAT:
                return 'Admin Kandidat';
            case self::ROLE_KANDIDAT:
                return 'Kandidat';
            default:
                return 'Unknown';
        }
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdminWeb($query)
    {
        return $query->where('role', self::ROLE_ADMIN_WEB);
    }

    public function scopeAdminKandidat($query)
    {
        return $query->where('role', self::ROLE_ADMIN_KANDIDAT);
    }

    public function scopeKandidat($query)
    {
        return $query->where('role', self::ROLE_KANDIDAT);
    }

    // ===== RELATIONSHIPS =====
    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function uploadedMedia()
    {
        return $this->hasMany(MediaLibrary::class, 'uploaded_by');
    }

    // ===== MUTATORS =====
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // ===== ACCESSORS =====
    public function getIsActiveStatusAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}