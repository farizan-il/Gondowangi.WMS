<?php

namespace App\Http\Controllers\Gondowangi\Authentifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthentifikasiController extends Controller
{
    public function index()
    {
        return view('Gondowangi.Authentifikasi.index');
    }

    // Register a new user
    public function register(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'newPassword' => 'required|min:6',
            ], [
                'fullName.required' => 'Nama lengkap wajib diisi',
                'fullName.max' => 'Nama lengkap maksimal 255 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar, gunakan email lain',
                'newPassword.required' => 'Password wajib diisi',
                'newPassword.min' => 'Password minimal 6 karakter',
            ]);

            // Create a new user record
            $user = new User();
            $user->fullName = $request->fullName;
            $user->email = $request->email;
            $user->katasandi = Hash::make($request->newPassword);
            $user->role = 'kandidat';
            $user->is_active = true;
            $user->save();

            // Send welcome email notification
            $this->sendWelcomeEmail($user);

            Log::info('New user registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->fullName,
                'timestamp' => now()
            ]);

            return back()->with('success', 'Registrasi berhasil! Email konfirmasi telah dikirim ke ' . $user->email . '. Silakan login.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])->withInput();
        }
    }

    // Send welcome email to new registered user
    private function sendWelcomeEmail($user)
    {
        try {
            $emailData = [
                'name' => $user->fullName,
                'email' => $user->email,
                'app_name' => config('app.name', 'Job Portal'),
                'app_url' => config('app.url'),
                'login_url' => route('Auth.index'),
                'contact_email' => config('mail.from.address'),
                'registration_date' => now()->format('d F Y H:i'),
                'user' => $user
            ];

            Mail::send('emails.welcome-registration', $emailData, function ($message) use ($user) {
                $message->to($user->email, $user->fullName)
                        ->subject('Selamat Datang di ' . config('app.name', 'Job Portal'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Welcome email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception here, registration should still succeed
        }
    }

    // Method untuk mengirim email OTP (existing method)
    private function sendOtpEmail($email, $name, $otp)
    {
        try {
            $emailData = [
                'name' => $name,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10)->format('H:i'),
                'app_name' => config('app.name', 'Job Portal')
            ];

            Mail::send('emails.forgot-password-otp', $emailData, function ($message) use ($email, $name) {
                $message->to($email, $name)
                        ->subject('Kode OTP Reset Password - ' . config('app.name'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('OTP email sent successfully', [
                'email' => $email,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Gagal mengirim email OTP: ' . $e->getMessage());
        }
    }

    // Login and handle role-based redirect
    public function login(Request $request)
    {
        $validated = $request->validate([
            'nikOrEmail' => 'required|string',
            'password' => 'required|string',
        ]);
    
        // Cek apakah inputnya berupa NIK atau Email
        $user = null;
        if (filter_var($request->nikOrEmail, FILTER_VALIDATE_EMAIL)) {
            // Jika email yang dimasukkan
            $user = User::where('email', $request->nikOrEmail)->first();
        } else {
            // Jika NIK yang dimasukkan
            $user = User::where('nik', $request->nikOrEmail)->first();
        }
    
        // Cek apakah user ditemukan dan password cocok
        if (!$user || !Hash::check($request->password, $user->katasandi)) {
            return redirect()->back()->withErrors(['loginError' => 'Invalid NIK/Email or password']);
        }
    
        // Login user ke dalam session
        Auth::login($user);
    
        // Redirect berdasarkan role sesuai dengan route yang ada
        switch (strtolower($user->role)) {
            case 'kandidat':
                // Cek apakah kandidat sudah pernah mengisi form
                $karyawan = Karyawan::where('user_id', $user->id)->first();
                
                if ($karyawan) {
                    // Jika sudah mengisi form, ke dashboard kandidat
                    return redirect()->route('kandidat.dashboard');
                } else {
                    // Jika belum mengisi form, ke form pengisian
                    return redirect()->route('kandidat.form.index');
                }
                
            case 'adminkandidat':
                // Redirect ke dashboard admin kandidat
                return redirect()->route('admin.kandidat.dashboard');
                
            case 'adminweb':
                // Redirect ke dashboard admin web
                return redirect()->route('admin.dashboard');
                
            default:
                // Jika role tidak dikenali, logout dan redirect ke login
                Auth::logout();
                return redirect()->route('Auth.index')->withErrors(['loginError' => 'Role tidak valid']);
        }
    }

    // PERBAIKAN: Send OTP dengan logging yang lebih detail
    public function sendOtpForgotPassword(Request $request)
    {
        try {
            Log::info('Forgot password OTP request started', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
    
            // Validasi input
            $validated = $request->validate([
                'email' => 'required|email'
            ], [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid'
            ]);
    
            // Cek apakah email terdaftar
            $user = User::where('email', $validated['email'])->first();
            
            if (!$user) {
                Log::warning('OTP request for unregistered email', [
                    'email' => $validated['email'],
                    'ip' => $request->ip()
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak terdaftar dalam sistem'
                ], 404);
            }
    
            // Generate OTP 6 digit
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            Log::info('OTP generated', [
                'email' => $user->email,
                'otp' => $otp, // Untuk debugging, hapus di production
                'user_id' => $user->id
            ]);
    
            // Simpan OTP ke cache dengan expire 10 menit
            $cacheKey = 'forgot_password_otp_' . $user->email;
            Cache::put($cacheKey, [
                'otp' => $otp,
                'email' => $user->email,
                'user_id' => $user->id,
                'created_at' => now()
            ], now()->addMinutes(10));
            
            Log::info('OTP saved to cache', [
                'cache_key' => $cacheKey,
                'expires_at' => now()->addMinutes(10)
            ]);
    
            // Kirim email OTP
            $this->sendOtpEmail($user->email, $user->fullName, $otp);
            
            Log::info('OTP process completed successfully', [
                'email' => $user->email,
                'timestamp' => now()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.',
                'debug' => [
                    'email_sent_to' => $user->email,
                    'otp_for_debug' => $otp, // Hapus ini di production
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for OTP request', [
                'errors' => $e->errors(),
                'email' => $request->email
            ]);
    
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
    
        } catch (\Exception $e) {
            Log::error('Critical error in sendOtpForgotPassword', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyOtpForgotPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6'
            ], [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'otp.required' => 'Kode OTP wajib diisi',
                'otp.size' => 'Kode OTP harus 6 digit'
            ]);
    
            $cacheKey = 'forgot_password_otp_' . $validated['email'];
            $cachedData = Cache::get($cacheKey);
    
            if (!$cachedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa. Silakan minta kode baru.'
                ], 400);
            }
    
            if ($cachedData['otp'] !== $validated['otp']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP yang Anda masukkan salah.'
                ], 400);
            }
    
            // Generate reset token
            $resetToken = Str::random(64);
            
            // Simpan reset token ke cache dengan expire 30 menit
            $tokenCacheKey = 'reset_password_token_' . $validated['email'];
            Cache::put($tokenCacheKey, [
                'token' => $resetToken,
                'email' => $validated['email'],
                'user_id' => $cachedData['user_id'],
                'created_at' => now()
            ], now()->addMinutes(30));
            
            // Hapus OTP dari cache
            Cache::forget($cacheKey);
    
            Log::info('OTP verified successfully', [
                'email' => $validated['email'],
                'timestamp' => now()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP berhasil diverifikasi.',
                'token' => $resetToken
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
    
        } catch (\Exception $e) {
            Log::error('Error verifying OTP', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password'
            ], [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'token.required' => 'Token reset wajib diisi',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
                'password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'password_confirmation.same' => 'Konfirmasi password tidak cocok'
            ]);
    
            $tokenCacheKey = 'reset_password_token_' . $validated['email'];
            $cachedData = Cache::get($tokenCacheKey);
    
            if (!$cachedData || $cachedData['token'] !== $validated['token']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset password tidak valid atau sudah kedaluwarsa.'
                ], 400);
            }
    
            $user = User::where('email', $validated['email'])->first();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }
    
            // Update password
            $user->update([
                'katasandi' => Hash::make($validated['password']),
                'last_password_change' => now(),
                'password_change_count' => ($user->password_change_count ?? 0) + 1
            ]);
    
            // Hapus token dari cache
            Cache::forget($tokenCacheKey);
    
            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset. Silakan login dengan password baru.'
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
    
        } catch (\Exception $e) {
            Log::error('Error resetting password', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    // Method untuk test email - debugging purposes
    public function testEmail()
    {
        try {
            $testData = [
                'name' => 'Test User',
                'otp' => '123456',
                'expires_at' => now()->addMinutes(10)->format('H:i'),
                'app_name' => config('app.name', 'Test App')
            ];
    
            Mail::send('emails.forgot-password-otp', $testData, function ($message) {
                $message->to('simplefiedcourse@gmail.com', 'Test User')
                        ->subject('Test Email OTP - ' . config('app.name'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Email test berhasil dikirim',
                'config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from' => config('mail.from.address'),
                    'encryption' => config('mail.mailers.smtp.encryption')
                ]
            ]);
    
        } catch (\Exception $e) {
            Log::error('Email test failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Email test gagal: ' . $e->getMessage(),
                'config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from' => config('mail.from.address'),
                    'encryption' => config('mail.mailers.smtp.encryption')
                ]
            ], 500);
        }
    }
}