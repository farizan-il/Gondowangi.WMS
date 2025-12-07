<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Karyawan;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('Approval-app.Authentifikasi.index');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik' => 'required|string|size:6',
            'password' => 'required|string',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 6 digit.',
            'password.required' => 'Password wajib diisi.',
        ]);
    
        // Cek rate limiting
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            $minutes = ceil($seconds / 60);
            
            return back()->withErrors([
                'nik' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
            ])->withInput($request->except('password'));
        }
    
        // Cari karyawan berdasarkan NIK (Load relasi department agar hemat query)
        $karyawan = Karyawan::with('department') 
            ->where('nik', $request->nik)
            ->where('status', 'aktif')
            ->first();
    
        // Cek apakah karyawan ditemukan dan password benar
        if ($karyawan && Hash::check($request->password, $karyawan->password)) {
            // Reset rate limiting jika login berhasil
            RateLimiter::clear($this->throttleKey($request));
            
            // Login karyawan
            Auth::guard('web')->login($karyawan, $request->filled('remember'));
            \App\Helpers\ActivityLogger::log('Login', 'User logged in successfully');
            
            // --- LOGIKA REDIRECT BERDASARKAN DEPARTMENT ---
            $targetUrl = '/dashboard'; // Default URL
    
            // Cek jika karyawan punya department
            if ($karyawan->department) {
                // Ubah ke huruf kecil semua agar tidak sensitif huruf besar/kecil
                $deptName = strtolower($karyawan->department->nama);
                
                // Cek apakah department adalah finance atau direktur
                if (in_array($deptName, ['finance', 'BOD'])) {
                    $targetUrl = '/overview';
                }
            }
            // ----------------------------------------------
    
            return redirect()->intended($targetUrl)
                ->with('success', 'Login berhasil! Selamat datang di halaman approval.');
        }
    
        // Increment rate limiting counter untuk percobaan gagal
        RateLimiter::hit($this->throttleKey($request), 900); // 15 menit lockout
    
        // Jika login gagal
        return back()->withErrors([
            'nik' => 'NIK atau password tidak valid.',
        ])->withInput($request->except('password'));
    }
    
    

    /**
     * Generate throttle key untuk rate limiting
     * Kombinasi NIK + IP address untuk keamanan lebih baik
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('nik') . '|' . $request->ip());
    }

    public function logout(Request $request)
    {
        // Simpan nama user sebelum logout untuk pesan
        $userName = Auth::user()->nama_lengkap ?? 'Pengguna';
        
        // Logout user
        \App\Helpers\ActivityLogger::log('Logout', 'User logged out');
        Auth::logout();
        
        // Invalidate session dan regenerate token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect ke login dengan pesan sukses
        return redirect('/login')->with('success', "Sampai jumpa, {$userName}! Anda telah berhasil logout.");
    }
}