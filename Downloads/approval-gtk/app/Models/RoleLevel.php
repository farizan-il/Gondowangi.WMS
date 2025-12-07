<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleLevel extends Model
{
    use HasFactory;

    protected $table = 'RoleLevel';
    
    protected $fillable = [
        'nama',
        // 'level', //ini jabatan/level approval
        'deskripsi',
        'department_id'
    ];

    protected $casts = [
        'level' => 'integer',
        'created_at' => 'datetime'
    ];

    // Relationships
    public function flowApprovals()
    {
        return $this->hasMany(FlowApproval::class);
    }
    
    public function department()
    {
        return $this->BelongsTo(Department::class);
    }

    public function progressApprovals()
    {
        return $this->hasMany(ProgressApproval::class);
    }

    // Scopes
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeOrderedByLevel($query)
    {
        return $query->orderBy('level');
    }
}