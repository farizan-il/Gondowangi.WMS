<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'full_name',
        'email',
        'phone',
        'resume_url',
        'cover_letter',
        'status',
        'applied_at'
    ];

    protected $casts = [
        'position_id' => 'integer',
        'applied_at' => 'datetime',
    ];

    // Relasi
    public function position()
    {
        return $this->belongsTo(CareerPosition::class, 'position_id');
    }
}
