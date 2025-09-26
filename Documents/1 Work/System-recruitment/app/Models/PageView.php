<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PageView extends Model
{
    protected $fillable = [
        'session_id',
        'page_name',
        'page_url',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    public function visitorSession()
    {
        return $this->belongsTo(VisitorSession::class, 'session_id', 'session_id');
    }
}