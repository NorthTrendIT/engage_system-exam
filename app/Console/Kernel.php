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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // $schedule->command('sapauth:hourly')->cron('*/5 * * * *'); 
        $schedule->command('sync:all_module_data_in_half_hour')->everyThirtyMinutes()->timezone('Asia/Manila')->between('8:00', '20:00');
        // $schedule->command('sync:all_module_data_in_midnight')->everySixHours();
        $schedule->command('sync:allmoduledata')->daily();
        $schedule->command('maintenance:user_disable')->daily();
        $schedule->command('sync:apbw_inv')->dailyAt('02:00');
        $schedule->command('sync:ntmc_inv')->dailyAt('04:00');
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
