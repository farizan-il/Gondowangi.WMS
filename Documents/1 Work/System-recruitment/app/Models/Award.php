<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'award_name',
        'award_description',
        'award_date',
        'awarding_body',
        'image_url',
        'display_order',
        'is_active' // null or true
    ];

    protected $casts = [
        'award_date' => 'date',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
