<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'product_name',
        'product_slug',
        'description',
        'tagline',
        'image_url',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relasi
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'product_brands');
    }
}
