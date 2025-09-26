<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorSession;
use App\Models\PageView;
use Carbon\Carbon;

class VisitorTrackingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Hanya track guest routes
        if ($request->is('admin/*') || $request->is('login')) {
            return $next($request);
        }

        $sessionId = session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $currentPage = $this->getCurrentPageName($request);
        $pageUrl = $request->url();
        
        // Detect device information
        $deviceType = DeviceDetectionService::detectDevice($userAgent);
        $browser = DeviceDetectionService::detectBrowser($userAgent);
        $os = DeviceDetectionService::detectOS($userAgent);

        // Update atau create visitor session
        VisitorSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_type' => $deviceType,
                'browser' => $browser,
                'operating_system' => $os,
                'current_page' => $currentPage,
                'last_activity' => Carbon::now()
            ]
        );

        // Record page view
        PageView::create([
            'session_id' => $sessionId,
            'page_name' => $currentPage,
            'page_url' => $pageUrl,
            'viewed_at' => Carbon::now()
        ]);

        return $next($request);
    }

    private function getCurrentPageName(Request $request)
    {
        $path = $request->path();
        
        if ($path === '/' || $path === 'beranda') {
            return 'beranda';
        } elseif (str_contains($path, 'tentangkami')) {
            return 'tentangkami';
        } elseif (str_contains($path, 'beritaclient')) {
            return 'berita';
        } elseif (str_contains($path, 'kontakkami')) {
            return 'kontakkami';
        } elseif (str_contains($path, 'karir')) {
            return 'karir';
        }
        
        return 'other';
    }
}