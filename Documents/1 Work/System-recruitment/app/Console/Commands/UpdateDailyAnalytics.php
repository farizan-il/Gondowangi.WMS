<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebsiteAnalytics;
use Carbon\Carbon;

class UpdateDailyAnalytics extends Command
{
    protected $signature = 'analytics:update-daily {--date=}';
    protected $description = 'Update daily analytics stats';

    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        
        WebsiteAnalytics::updateDailyStats($date);
        
        $this->info("Daily analytics updated for {$date->format('Y-m-d')}");
    }
}