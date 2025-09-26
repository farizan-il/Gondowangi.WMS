<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('Auth.index')->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek apakah user memiliki role yang sesuai
        if (Auth::user()->role !== $role) {
            // Redirect berdasarkan role yang dimiliki user
            switch (Auth::user()->role) {
                case 'adminweb':
                    return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak');
                case 'adminkandidat':
                    return redirect()->route('admin.kandidat.dashboard')->with('error', 'Akses ditolak');
                case 'kandidat':
                    return redirect()->route('kandidat.dashboard')->with('error', 'Akses ditolak');
                default:
                    return redirect()->route('Auth.index')->with('error', 'Role tidak valid');
            }
        }

        // Cek apakah user aktif
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('Auth.index')->with('error', 'Akun Anda tidak aktif');
        }

        return $next($request);
    }
}