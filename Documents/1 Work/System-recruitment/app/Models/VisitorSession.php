<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class VisitorSession extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'current_page',
        'last_activity'
    ];

    protected $casts = [
        'last_activity' => 'datetime'
    ];

    public function pageViews()
    {
        return $this->hasMany(PageView::class, 'session_id', 'session_id');
    }

    // Scope untuk active visitors (dalam 5 menit terakhir)
    public function scopeActive($query)
    {
        return $query->where('last_activity', '>=', Carbon::now()->subMinutes(5));
    }
}