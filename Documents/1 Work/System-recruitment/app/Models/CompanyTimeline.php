<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'description',
        'image_url',
        'timeline_type',
    ];

    protected $casts = [
        'year' => 'integer',
        'display_order' => 'integer',
    ];

    // Scope untuk mengurutkan berdasarkan tahun
    public function scopeOrderByYear($query, $direction = 'desc')
    {
        return $query->orderBy('year', $direction);
    }

    // Scope untuk filter berdasarkan tipe timeline
    public function scopeByType($query, $type)
    {
        return $query->where('timeline_type', $type);
    }

    // Mutator untuk memastikan image_url disimpan dengan benar
    public function setImageUrlAttribute($value)
    {
        $this->attributes['image_url'] = $value ? ltrim($value, '/') : null;
    }

    // Accessor untuk mendapatkan URL gambar lengkap
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // Jika sudah berupa URL lengkap, return as is
        if (strpos($value, 'http') === 0) {
            return $value;
        }
        
        // Jika berupa path relatif, buat URL lengkap
        return asset(ltrim($value, '/'));
    }

    // Method untuk mendapatkan raw image_url tanpa asset()
    public function getRawImageUrl()
    {
        return $this->getRawOriginal('image_url');
    }

    // Accessor untuk mendapatkan tahun dengan format yang bagus
    public function getFormattedYearAttribute()
    {
        return $this->year ? $this->year . ' M' : null;
    }

    // Accessor untuk mendapatkan deskripsi singkat
    public function getShortDescriptionAttribute()
    {
        return strlen($this->description) > 100 ? 
            substr($this->description, 0, 100) . '...' : 
            $this->description;
    }
}