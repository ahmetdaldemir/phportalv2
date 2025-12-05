<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        // Veritabanı yedeği - Her gün saat 00:00'da Cloudflare R2'ye yükle
        $schedule->command('db:backup-r2 --compress')
            ->dailyAt('00:00')
            ->timezone('Europe/Istanbul')
            ->onFailure(function () {
                \Log::error('Database backup to R2 failed at ' . now());
            })
            ->onSuccess(function () {
                \Log::info('Database backup to R2 completed successfully at ' . now());
            });
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
