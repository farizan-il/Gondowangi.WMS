<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class AuthController extends Controller
{
    use ActivityLogger;

    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
            'database_connection' => 'required|string|in:mysql,mysql_testing',
        ]);

        // Cek apakah identifier adalah email atau NIK
        $fieldType = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        // Set database connection sesuai pilihan user
        $databaseConnection = $credentials['database_connection'];
        \Config::set('database.default', $databaseConnection);
        \DB::purge($databaseConnection);
        \DB::reconnect($databaseConnection);

        // Cari user dan cek status - LOAD RELATION ROLE
        $user = User::with('role')->where($fieldType, $credentials['identifier'])->first();

        if (!$user) {
            return back()->withErrors([
                'identifier' => 'NIK/Email tidak ditemukan.',
            ])->onlyInput('identifier');
        }

        if ($user->status !== 'active') {
            return back()->withErrors([
                'identifier' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->onlyInput('identifier');
        }

        if (Auth::attempt([$fieldType => $credentials['identifier'], 'password' => $credentials['password']], $request->remember)) {
            $request->session()->regenerate();

            // Simpan pilihan database di session
            $request->session()->put('selected_database', $databaseConnection);

            $this->logActivity(Auth::user(), 'Login', [
                'description' => 'User berhasil Login ke sistem',
                'database' => $databaseConnection,
            ]);

            // Determine redirect URL based on permissions
            $redirectUrl = '/dashboard'; // Default fallback
            
            $user = Auth::user();
            
            if ($user->hasPermission('onhand.view')) {
                $redirectUrl = '/dashboard';
            } elseif ($user->hasPermission('incoming.view')) {
                $redirectUrl = '/transaction/goods-receipt';
            } elseif ($user->hasPermission('qc.view')) {
                $redirectUrl = '/transaction/quality-control';
            } elseif ($user->hasPermission('putaway.view')) {
                $redirectUrl = '/transaction/putaway-transfer';
            } elseif ($user->hasPermission('picking.view')) {
                $redirectUrl = '/transaction/picking-list';
            } elseif ($user->hasPermission('cycle_count.view')) {
                $redirectUrl = '/transaction/cycle-count';
            } elseif ($user->hasPermission('return.view')) {
                $redirectUrl = '/transaction/return';
            } elseif ($user->hasPermission('reservation.view')) {
                $redirectUrl = '/transaction/reservation';
            } elseif ($user->hasPermission('master_data.view')) {
                $redirectUrl = '/master-data';
            } elseif ($user->hasPermission('it_dashboard.view')) {
                $redirectUrl = '/it-dashboard';
            }

            return redirect()->intended($redirectUrl);
        }

        return back()->withErrors([
            'identifier' => 'NIK/Email atau password salah.',
        ])->onlyInput('identifier');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50|unique:users',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'departement' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['nama_lengkap'], // PERBAIKI INI
            'email' => $validated['email'],
            'departement' => $validated['departement'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'role_id' => null,
        ]);

        Auth::login($user);

        $this->logActivity($user, 'Register', [
            'description' => 'User baru registrasi dan login',
        ]);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->logActivity(Auth::user(), 'Logout', [
                'description' => 'User Logout dari sistem',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}