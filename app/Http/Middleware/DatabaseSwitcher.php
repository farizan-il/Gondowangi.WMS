<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DatabaseSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has selected a database connection in session
        $selectedDatabase = $request->session()->get('selected_database');

        // Validate that the selected database is one of the allowed connections
        if ($selectedDatabase && in_array($selectedDatabase, ['mysql', 'mysql_testing'])) {
            // Set the default database connection for this request
            Config::set('database.default', $selectedDatabase);
            
            // Purge and reconnect to ensure fresh connection
            DB::purge($selectedDatabase);
            DB::reconnect($selectedDatabase);
        } else {
            // Fallback to default (mysql/production) if no valid session exists
            Config::set('database.default', 'mysql');
            DB::purge('mysql');
            DB::reconnect('mysql');
        }

        return $next($request);
    }
}
