<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckArgoNotificationCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Jalankan command pada jam 09:00, 11:00, 11:30, 12:00, 15:00, 16:00, 17:00 setiap hari
        $schedule->command('argo:check-notifications')
                ->dailyAt('09:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('11:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('11:30')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('12:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('15:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('16:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
                
        $schedule->command('argo:check-notifications')
                ->dailyAt('17:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/argo-notifications.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}