<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users and non-GET requests (or specific important GETs)
        // For now, let's log everything for authenticated users to see "how often they use it"
        // But maybe filter out some noise like assets or polling if any.
        
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $path = $request->path();
            $method = $request->method();
            
            // Skip logging for some paths if needed (e.g. debugbar, notifications polling)
            if (!str_contains($path, 'notifications/all') && !str_contains($path, 'livewire')) {
                 \App\Helpers\ActivityLogger::log(
                    'Access', 
                    "User accessed {$path} via {$method}"
                );
            }
        }

        return $response;
    }
}
