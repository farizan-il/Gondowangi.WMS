<?php
// app/Models/WebsiteAnalytics.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WebsiteAnalytics extends Model
{
    protected $fillable = [
        'date',
        'total_visitors',
        'total_page_views',
        'unique_visitors',
        'bounce_rate',
        'avg_session_duration',
        'contact_forms_submitted'
    ];

    protected $casts = [
        'date' => 'date',
        'bounce_rate' => 'decimal:2'
    ];

    public static function updateDailyStats($date = null)
    {
        $date = $date ?? Carbon::today();
        
        // Hitung total visitors (unique sessions)
        $totalVisitors = VisitorSession::whereDate('created_at', $date)->count();
        
        // Hitung unique visitors berdasarkan IP
        $uniqueVisitors = VisitorSession::whereDate('created_at', $date)
            ->distinct('ip_address')
            ->count();
        
        // Hitung total page views
        $totalPageViews = PageView::whereDate('viewed_at', $date)->count();
        
        // Hitung bounce rate (sessions dengan hanya 1 page view)
        $bouncedSessions = VisitorSession::whereDate('created_at', $date)
            ->whereHas('pageViews', function($query) {
                $query->havingRaw('COUNT(*) = 1');
            })
            ->count();
        
        $bounceRate = $totalVisitors > 0 ? ($bouncedSessions / $totalVisitors) * 100 : 0;
        
        // Update atau create record
        self::updateOrCreate(
            ['date' => $date],
            [
                'total_visitors' => $totalVisitors,
                'total_page_views' => $totalPageViews,
                'unique_visitors' => $uniqueVisitors,
                'bounce_rate' => $bounceRate
            ]
        );
    }

    public static function getGrowthPercentage($currentValue, $previousValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }
        
        return (($currentValue - $previousValue) / $previousValue) * 100;
    }
}