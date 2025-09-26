<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_path',
        'button_text',
        'button_url',
        'is_active',
        'sort_order',
        'type'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
    
    // Untuk gambar yang disimpan di storage
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
    
    // Untuk gambar yang disimpan di public/assets/banners
    public function getBannerImageUrlAttribute()
    {
        return asset('assets/banners/' . $this->image_path);
    }
}