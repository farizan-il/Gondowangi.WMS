<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    protected $table = 'company_info';

    protected $fillable = [
        'company_name',
        'tagline',
        'description',
        'vision',
        'mission',
        'founded_year',
        'address',
        'phone',
        'email',
        'website',
        'logo_url'
    ];

    protected $casts = [
        'founded_year' => 'integer',
    ];
}
