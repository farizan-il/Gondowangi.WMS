<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'brand_slug',
        'description',
        'logo_url',
        'brand_img',
        'website_url',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scope untuk brand aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    // Accessor untuk URL gambar brand
    public function getBrandImageUrlAttribute()
    {
        return asset('assets/brand/' . $this->brand_img);
    }

    // Relasi
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_brands');
    }
}