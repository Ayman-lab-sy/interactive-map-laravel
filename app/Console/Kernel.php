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
    protected function schedule($schedule)
    {
       // 📊 تقرير يومي
        $schedule->command('media:publish daily')->dailyAt('08:00');

        // 📢 Social (4 مرات)
        $schedule->command('media:publish social')->dailyAt('10:00');
        $schedule->command('media:publish social')->dailyAt('14:00');
        $schedule->command('media:publish social')->dailyAt('18:00');
        $schedule->command('media:publish social')->dailyAt('22:00');
        $schedule->command('media:check-alerts')->hourly();
        
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
