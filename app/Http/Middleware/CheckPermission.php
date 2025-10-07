<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Jika user tidak punya role
        if (!auth()->user()->role) {
            abort(403, 'Anda belum memiliki role. Silakan hubungi administrator.');
        }

        // Cek permission
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}