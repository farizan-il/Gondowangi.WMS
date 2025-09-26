<?php

// App/Console/Commands/CleanupOldSessions.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitorSession;
use App\Models\PageView;
use Carbon\Carbon;

class CleanupOldSessions extends Command
{
    protected $signature = 'visitors:cleanup';
    protected $description = 'Cleanup old visitor sessions and page views';

    public function handle()
    {
        $cutoffTime = Carbon::now()->subDays(7); // Hapus data lebih dari 7 hari
        
        // Hapus page views lama
        $deletedPageViews = PageView::where('viewed_at', '<', $cutoffTime)->delete();
        
        // Hapus visitor sessions lama
        $deletedSessions = VisitorSession::where('last_activity', '<', $cutoffTime)->delete();
        
        $this->info("Cleanup completed:");
        $this->info("- Deleted {$deletedPageViews} old page views");
        $this->info("- Deleted {$deletedSessions} old visitor sessions");
        
        return 0;
    }
}