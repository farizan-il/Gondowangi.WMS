<?php

// App/Http/Middleware/TrackVisitor.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorSession;
use App\Models\PageView;
use Carbon\Carbon;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip tracking untuk admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        $sessionId = session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $currentPage = $this->getCurrentPageName($request);
        $pageUrl = $request->url();

        // Update atau create visitor session
        VisitorSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
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
        } elseif (str_contains($path, 'semuabrand')) {
            return 'semuabrand';
        } elseif (str_contains($path, 'azalea')) {
            return 'azalea';
        } elseif (str_contains($path, 'natur')) {
            return 'natur';
        }elseif (str_contains($path, 'mizzu')) {
            return 'mizzu';
        } elseif (str_contains($path, 'hgforman')) {
            return 'hgforman';
        } elseif (str_contains($path, 'karir')) {
            return 'karir';
        }
        
        return 'other';
    }
}