<?php

namespace App\Providers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Rate limit untuk login attempts
        RateLimiter::for('login', function ($request) {
            return [
                // Limit berdasarkan NIK + IP Address
                Limit::perMinute(5)->by($request->input('nik') . '|' . $request->ip()),
                
                // Limit global berdasarkan IP saja (untuk mencegah serangan dari IP yang sama)
                Limit::perMinute(10)->by($request->ip()),
            ];
        });

        // Rate limit untuk halaman login (mencegah spam request ke form login)
        RateLimiter::for('login-page', function ($request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        // Rate limit untuk password reset (jika ada fitur reset password)
        RateLimiter::for('password-reset', function ($request) {
            return [
                Limit::perMinute(3)->by($request->input('email') . '|' . $request->ip()),
                Limit::perHour(10)->by($request->ip()),
            ];
        });
    }
}
