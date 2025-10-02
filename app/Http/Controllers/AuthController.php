<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah identifier adalah email atau NIK
        $fieldType = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        // Cari user dan cek status
        $user = User::where($fieldType, $credentials['identifier'])->first();

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
            return redirect()->intended('/dashboard');
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
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'departement' => $validated['departement'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'role_id' => null, // Akan di-set oleh admin
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}