<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceAnalytics extends Model
{
    protected $fillable = [
        'date',
        'device_type',
        'total_sessions',
        'unique_visitors',
        'percentage'
    ];

    protected $casts = [
        'date' => 'date',
        'percentage' => 'decimal:2'
    ];

    public static function updateDailyDeviceStats($date = null)
    {
        $date = $date ?? Carbon::today();
        
        // Get device stats for the date
        $deviceStats = VisitorSession::whereDate('created_at', $date)
            ->select(
                'device_type',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->groupBy('device_type')
            ->get();
        
        // Calculate total sessions for percentage calculation
        $totalSessions = $deviceStats->sum('total_sessions');
        
        // Delete existing records for the date
        self::where('date', $date)->delete();
        
        // Insert new records
        foreach ($deviceStats as $stat) {
            $percentage = $totalSessions > 0 ? ($stat->total_sessions / $totalSessions) * 100 : 0;
            
            self::create([
                'date' => $date,
                'device_type' => $stat->device_type ?? 'desktop',
                'total_sessions' => $stat->total_sessions,
                'unique_visitors' => $stat->unique_visitors,
                'percentage' => $percentage
            ]);
        }
        
        // Ensure all device types have records (even if 0)
        $deviceTypes = ['desktop', 'mobile', 'tablet'];
        $existingTypes = $deviceStats->pluck('device_type')->toArray();
        
        foreach ($deviceTypes as $type) {
            if (!in_array($type, $existingTypes)) {
                self::create([
                    'date' => $date,
                    'device_type' => $type,
                    'total_sessions' => 0,
                    'unique_visitors' => 0,
                    'percentage' => 0
                ]);
            }
        }
    }
    
    public static function getDeviceStatsForDate($date = null)
    {
        $date = $date ?? Carbon::today();
        
        return self::where('date', $date)
            ->orderBy('total_sessions', 'desc')
            ->get();
    }
    
    public static function getRealTimeDeviceStats()
    {
        $stats = VisitorSession::active()
            ->select(
                'device_type',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('device_type')
            ->get();
        
        $total = $stats->sum('count');
        
        $result = [];
        $deviceTypes = ['desktop', 'mobile', 'tablet'];
        
        foreach ($deviceTypes as $type) {
            $stat = $stats->firstWhere('device_type', $type);
            $count = $stat ? $stat->count : 0;
            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
            
            $result[] = [
                'device_type' => $type,
                'count' => $count,
                'percentage' => round($percentage, 1)
            ];
        }
        
        return collect($result);
    }
}