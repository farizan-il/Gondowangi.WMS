<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthHelper
{
    /**
     * Cek apakah user adalah admin web
     */
    public function isAdminWeb()
    {
        return Auth::check() && Auth::user()->role === 'adminweb';
    }

    /**
     * Cek apakah user adalah admin kandidat
     */
    public function isAdminKandidat()
    {
        return Auth::check() && Auth::user()->role === 'adminkandidat';
    }

    /**
     * Cek apakah user adalah kandidat
     */
    public function isKandidat()
    {
        return Auth::check() && Auth::user()->role === 'kandidat';
    }

    /**
     * Cek apakah user aktif
     */
    public function isActiveUser()
    {
        return Auth::check() && Auth::user()->is_active;
    }

    /**
     * Redirect berdasarkan role user
     */
    public function redirectBasedOnRole()
    {
        if (!Auth::check()) {
            return redirect()->route('Auth.index');
        }

        switch (Auth::user()->role) {
            case 'adminweb':
                return redirect()->route('admin.dashboard');
            case 'adminkandidat':
                return redirect()->route('admin.kandidat.dashboard');
            case 'kandidat':
                return redirect()->route('kandidat.dashboard');
            default:
                return redirect()->route('Auth.index');
        }
    }

    /**
     * Get user role label
     */
    public function getUserRoleLabel()
    {
        if (!Auth::check()) {
            return 'Guest';
        }

        switch (Auth::user()->role) {
            case 'adminweb':
                return 'Admin Web';
            case 'adminkandidat':
                return 'Admin Kandidat';
            case 'kandidat':
                return 'Kandidat';
            default:
                return 'Unknown';
        }
    }
}