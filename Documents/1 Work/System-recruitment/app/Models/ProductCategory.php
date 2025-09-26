<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_slug',
        'description',
        'image_url',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relasi
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}