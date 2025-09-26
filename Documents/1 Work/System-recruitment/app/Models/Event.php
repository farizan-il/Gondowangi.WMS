<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_name',
        'event_slug',
        'description',
        'event_date',
        'event_time',
        'location',
        'image_url',
        'status',
        'is_featured'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'is_featured' => 'boolean',
    ];

    // Accessor untuk format tanggal
    public function getFormattedEventDateAttribute()
    {
        return $this->event_date ? $this->event_date->format('d M Y') : null;
    }

    // Accessor untuk format waktu
    public function getFormattedEventTimeAttribute()
    {
        return $this->event_time ? $this->event_time->format('H:i') : null;
    }

    // Scope untuk event yang akan datang
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now())->where('status', 'upcoming');
    }

    // Scope untuk event featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Mutator untuk memastikan event_time disimpan dengan benar
    public function setEventTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['event_time'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
        } else {
            $this->attributes['event_time'] = null;
        }
    }
}