<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'token',
        'created_at',
        'expired_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expired_at < now();
    }
}
