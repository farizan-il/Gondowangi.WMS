<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\NotificationViewComposer;
use App\Models\Pengajuan;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     // Daftarkan View Composer untuk topbar notifications
    //     View::composer([
    //         'approval-app.layout.approver-main',
    //     ], NotificationViewComposer::class);
    // }
    public function boot()
    {
        // Share data ke sidebar layout
        View::composer('Approval-app.Layout.approver-main', function ($view) {
            $readyForTRCount = 0;
    
            if (Auth::check() && Auth::user()->department && strtolower(Auth::user()->department->nama) === 'finance') {
                // Hitung Pengajuan yang siap di-TR-kan
                $pendingPengajuan = Pengajuan::whereIn('status_pengajuan', ['approved', 'proses_settlement'])
                    ->whereDoesntHave('transactionRequest')
                    ->count();
    
                // Hitung Settlement (Over Budget) yang siap di-TR-kan
                $pendingSettlement = Settlement::where('selisih', '<', 0)
                    ->where('status_settlement', 'approved')
                    ->whereDoesntHave('transactionRequest')
                    ->count();
    
                $readyForTRCount = $pendingPengajuan + $pendingSettlement;
            }
    
            $view->with('readyForTRCount', $readyForTRCount);
        });
    }
}
