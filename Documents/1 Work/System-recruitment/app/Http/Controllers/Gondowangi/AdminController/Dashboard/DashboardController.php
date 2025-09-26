<?php
namespace App\Http\Controllers\Gondowangi\AdminController\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\WebsiteAnalytics;
use App\Models\VisitorSession;
use App\Models\DeviceAnalytics;
use App\Models\PageView;
use Carbon\Carbon;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Update daily stats untuk hari ini
        WebsiteAnalytics::updateDailyStats();
        
        // Ambil data hari ini dan kemarin
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $todayStats = WebsiteAnalytics::where('date', $today)->first();
        $yesterdayStats = WebsiteAnalytics::where('date', $yesterday)->first();
        
        // Default values jika tidak ada data
        $totalVisitors = $todayStats->total_visitors ?? 0;
        $totalPageViews = $todayStats->total_page_views ?? 0;
        $bounceRate = $todayStats->bounce_rate ?? 0;
        
        $yesterdayVisitors = $yesterdayStats->total_visitors ?? 0;
        $yesterdayPageViews = $yesterdayStats->total_page_views ?? 0;
        $yesterdayBounceRate = $yesterdayStats->bounce_rate ?? 0;
        
        // Hitung growth percentage
        $visitorsGrowth = WebsiteAnalytics::getGrowthPercentage($totalVisitors, $yesterdayVisitors);
        $pageViewsGrowth = WebsiteAnalytics::getGrowthPercentage($totalPageViews, $yesterdayPageViews);
        $bounceRateGrowth = WebsiteAnalytics::getGrowthPercentage($bounceRate, $yesterdayBounceRate);
        
        
        // Real-time visitors (active dalam 5 menit terakhir)
            $activeVisitors = VisitorSession::active()->count();
            
            // Visitors per page (active sessions)
            $activeVisitorsByPage = VisitorSession::active()
                ->select('current_page', DB::raw('count(*) as count'))
                ->groupBy('current_page')
                ->get()
                ->keyBy('current_page');
    
            // Mapping nama halaman untuk display
            $pageNames = [
                'beranda' => 'Beranda',
                'tentangkami' => 'Tentang Kami',
                'berita' => 'Berita',
                'kontakkami' => 'Kontak Kami',
                'karir' => 'Karir'
            ];
    
            $visitorsPerPage = [];
            foreach ($pageNames as $key => $name) {
                $visitorsPerPage[$key] = [
                    'name' => $name,
                    'count' => $activeVisitorsByPage->get($key)->count ?? 0
                ];
            }
    
            // Hitung persentase untuk progress bar
            $maxVisitors = max(array_column($visitorsPerPage, 'count'));
            $maxVisitors = $maxVisitors > 0 ? $maxVisitors : 1; // Avoid division by zero
    
            foreach ($visitorsPerPage as $key => &$page) {
                $page['percentage'] = ($page['count'] / $maxVisitors) * 100;
            }
        // Real-time visitors (active dalam 5 menit terakhir)
        
        // Device Analytics
        $deviceStats = DeviceAnalytics::getDeviceStatsForDate($today);
        $realTimeDeviceStats = DeviceAnalytics::getRealTimeDeviceStats();
        
        // Pages we want to track (match with routes/views)
            $pages = [
                'Beranda' => 'beranda',
                'Tentang Kami' => 'tentangkami',
                'Berita' => 'berita',
                'Karir' => 'karir',
                'Kontak Kami' => 'kontakkami',
            ];
        
            $pagePerformanceData = [];
            $totalViews = 0;
        
            // First pass: collect data and calculate total
            foreach ($pages as $label => $url) {
                $visitors = VisitorSession::where('current_page', $url)
                    ->distinct('ip_address')
                    ->count('ip_address');
        
                $pageViews = PageView::where('page_name', $url)->count();
                $totalViews += $pageViews;
        
                $pagePerformanceData[] = [
                    'label' => $label,
                    'url' => $url,
                    'visitors' => $visitors,
                    'views' => $pageViews,
                ];
            }
    
            // Second pass: calculate percentages
            foreach ($pagePerformanceData as &$data) {
                $data['percentage'] = $totalViews > 0 ? round(($data['views'] / $totalViews) * 100, 1) : 0;
            }
    
            // Define colors for chart (Star Admin 2 compatible colors)
            $chartColors = [
                '#4B49AC', // Primary Purple
                '#FFC100', // Warning Yellow  
                '#F3797E', // Danger Red
                '#57C7D4', // Info Cyan
                '#5A8DEE', // Success Blue
            ];
        // Pages we want to track (match with routes/views)
        
        // Contact Forms Statistics
        $totalContactForms = Contact::count(); // Total semua contact forms
        
        // Contact forms bulan ini
        $thisMonthContacts = Contact::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Contact forms bulan lalu
        $lastMonthContacts = Contact::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        // Hitung growth percentage untuk contact forms
        $contactFormsGrowth = 0;
        if ($lastMonthContacts > 0) {
            $contactFormsGrowth = round((($thisMonthContacts - $lastMonthContacts) / $lastMonthContacts) * 100);
        } elseif ($thisMonthContacts > 0) {
            $contactFormsGrowth = 100; // 100% growth jika bulan lalu tidak ada data
        }
        
        $brands = [
            [
                'name' => 'Semua Brand',
                'url' => 'https://gondowangi.com/semuabrand',
                'icon' => 'mdi-store',
                'color' => 'text-primary',
                'category' => 'Semua Brand'
            ],
            [
                'name' => 'Natur Hair Care',
                'url' => 'https://gondowangi.com/natur',
                'icon' => 'mdi-leaf',
                'color' => 'text-success',
                'category' => 'Hair Care Products'
            ],
            [
                'name' => 'MIZZU Cosmetics',
                'url' => 'https://gondowangi.com/mizzu',
                'icon' => 'mdi-star',
                'color' => 'text-warning',
                'category' => 'Beauty Products'
            ],
            [
                'name' => 'Azalea Hijab',
                'url' => 'https://gondowangi.com/azalea',
                'icon' => 'mdi-heart',
                'color' => 'text-danger',
                'category' => 'Fashion & Style'
            ],
            [
                'name' => 'HG For Men',
                'url' => 'https://gondowangi.com/hgforman',
                'icon' => 'mdi-account',
                'color' => 'text-info',
                'category' => "Men's Care"
            ],
        ];
    
        foreach ($brands as &$brand) {
            $brand['views'] = PageView::where('page_url', $brand['url'])->count();
    
            $lastMonth = PageView::where('page_url', $brand['url'])
                ->where('viewed_at', '>=', now()->subDays(60))
                ->where('viewed_at', '<', now()->subDays(30))
                ->count();
    
            $thisMonth = $brand['views'];
    
            $growth = $lastMonth > 0
                ? round((($thisMonth - $lastMonth) / $lastMonth) * 100)
                : ($thisMonth > 0 ? 100 : 0);
    
            $brand['growth'] = $growth;
        }

        return view('Gondowangi.Admin.Beranda.index', compact(
            'activeVisitors',
            'visitorsPerPage',
            'totalVisitors', 'visitorsGrowth',
            'totalPageViews', 'pageViewsGrowth',
            'bounceRate',
            'bounceRateGrowth', 
            'deviceStats',
            'realTimeDeviceStats',
            'pagePerformanceData', 
            'brands', 
            'chartColors',
            'totalContactForms',
            'thisMonthContacts',
            'contactFormsGrowth'
        ));
    }
    
    public function getRealTimeData()
    {
        $activeVisitors = VisitorSession::active()->count();
        
        $activeVisitorsByPage = VisitorSession::active()
            ->select('current_page', DB::raw('count(*) as count'))
            ->groupBy('current_page')
            ->get()
            ->keyBy('current_page');

        $pageNames = [
            'beranda' => 'Beranda',
            'tentangkami' => 'Tentang Kami',
            'berita' => 'Berita',
            'kontakkami' => 'Kontak Kami',
            'karir' => 'Karir'
        ];

        $visitorsPerPage = [];
        foreach ($pageNames as $key => $name) {
            $visitorsPerPage[$key] = [
                'name' => $name,
                'count' => $activeVisitorsByPage->get($key)->count ?? 0
            ];
        }

        $maxVisitors = max(array_column($visitorsPerPage, 'count'));
        $maxVisitors = $maxVisitors > 0 ? $maxVisitors : 1;

        foreach ($visitorsPerPage as $key => &$page) {
            $page['percentage'] = ($page['count'] / $maxVisitors) * 100;
        }

        // Real-time device stats
        $realTimeDeviceStats = DeviceAnalytics::getRealTimeDeviceStats();

        return response()->json([
            'activeVisitors' => $activeVisitors,
            'visitorsPerPage' => $visitorsPerPage,
            'deviceStats' => $realTimeDeviceStats,
            'lastUpdated' => now()->format('H:i:s')
        ]);
    }
    
    public function getTrafficData(Request $request)
    {
        $period = $request->input('period', 7);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Determine date range
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            $end = Carbon::now()->endOfDay();
            $start = Carbon::now()->subDays($period - 1)->startOfDay();
        }
        
        // Calculate total days
        $totalDays = $start->diffInDays($end) + 1;
        
        // Determine grouping based on period
        $groupBy = $totalDays <= 31 ? 'day' : 'week';
        
        if ($groupBy == 'day') {
            // Daily data
            $visitors = DB::table('visitor_sessions')
                ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT session_id) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            $pageViews = DB::table('page_views')
                ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
                ->whereBetween('viewed_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            $labels = [];
            $visitorsData = [];
            $pageViewsData = [];
            
            $currentDate = $start->copy();
            while ($currentDate <= $end) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Format labels
                if ($totalDays <= 7) {
                    $labels[] = $currentDate->format('D'); // Mon, Tue, etc.
                } else {
                    $labels[] = $currentDate->format('j/n'); // 1/1, 2/1, etc.
                }
                
                $visitorsData[] = $visitors->get($dateStr)->count ?? 0;
                $pageViewsData[] = $pageViews->get($dateStr)->count ?? 0;
                
                $currentDate->addDay();
            }
            
        } else {
            // Weekly data
            $visitors = DB::table('visitor_sessions')
                ->selectRaw('YEARWEEK(created_at, 1) as week, COUNT(DISTINCT session_id) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->keyBy('week');
            
            $pageViews = DB::table('page_views')
                ->selectRaw('YEARWEEK(viewed_at, 1) as week, COUNT(*) as count')
                ->whereBetween('viewed_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->keyBy('week');
            
            $labels = [];
            $visitorsData = [];
            $pageViewsData = [];
            
            $currentDate = $start->copy()->startOfWeek();
            $weekNumber = 1;
            
            while ($currentDate <= $end) {
                $yearWeek = $currentDate->format('oW');
                
                $labels[] = 'Week ' . $weekNumber;
                $visitorsData[] = $visitors->get($yearWeek)->count ?? 0;
                $pageViewsData[] = $pageViews->get($yearWeek)->count ?? 0;
                
                $currentDate->addWeek();
                $weekNumber++;
            }
        }
        
        return response()->json([
            'labels' => $labels,
            'visitorsData' => $visitorsData,
            'pageViewsData' => $pageViewsData,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}



