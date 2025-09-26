<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_location',
        'parent_id',
        'menu_title',
        'menu_url',
        'menu_type',
        'target_blank',
        'css_class',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'target_blank' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relasi
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    // Scope untuk menu berdasarkan lokasi
    public function scopeByLocation($query, $location)
    {
        return $query->where('menu_location', $location);
    }

    // Scope untuk menu aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk menu utama (tanpa parent)
    public function scopeMainItems($query)
    {
        return $query->whereNull('parent_id');
    }
}
