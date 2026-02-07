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
        // Seed school data daily at midnight
        // $schedule->command('school:seed')->daily();
        
        // Or run it weekly on Monday at 2 AM
        // $schedule->command('school:seed')->weeklyOn(1, '2:00');
        
        // Or run it monthly on the 1st at 3 AM
        // $schedule->command('school:seed')->monthlyOn(1, '3:00');

        // Automatic student upgrade - runs on December 31st at 11:59 PM
        // This upgrades all students to the next grade at year end
        $schedule->command('students:auto-upgrade')
            ->yearlyOn(12, 31, '23:59')
            ->runInBackground()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Annual student upgrade completed successfully.');
            })
            ->onFailure(function () {
                \Log::error('Annual student upgrade failed.');
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
