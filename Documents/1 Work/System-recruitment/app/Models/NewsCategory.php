<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_name',
        'category_slug',
        'description',
        'color',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }

    // Accessors
    public function getBadgeAttribute()
    {
        $color = $this->color ?? 'info';
        return '<span class="badge badge-category bg-' . $color . '">' . $this->category_name . '</span>';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
