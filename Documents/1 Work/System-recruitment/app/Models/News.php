<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'status', // 'draft', 'published', 'archived'
        'is_featured', // jika is_featured = 1 berarti berita itu utama
        'is_active',
        'published_at',
        // 'views_count',
        // 'meta_description',
        // 'tags'
    ];
    
    protected $casts = [
        'category_id' => 'integer',
        'author_id' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F j, Y');
    }

    public function getFormattedViewsAttribute()
    {
        return number_format($this->views_count ?? 0);
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->featured_image) {
            return asset($this->featured_image);
        }
        return 'https://via.placeholder.com/80x60/28a745/fff?text=News';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'published' => '<span class="badge status-published text-success"><strong>Published</strong></span>',
            'draft' => '<span class="badge status-draft text-warning"><strong>Draft</strong></span>',
            'archived' => '<span class="badge status-archived text-secondary"><strong>Archived</strong></span>',
        ];

        return $badges[$this->status] ?? '<span class="badge text-muted">Unknown</span>';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}