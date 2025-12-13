<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update only if it's been more than a minute to reduce DB writes
            if (!$user->last_seen_at || Carbon::parse($user->last_seen_at)->diffInMinutes(now()) >= 1) {
                $user->timestamps = false; // Prevent updating 'updated_at'
                $user->last_seen_at = now();
                $user->save();
            }
        }

        return $next($request);
    }
}
