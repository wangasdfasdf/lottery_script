<?php

namespace App\Console;

use App\Console\Commands\BJDCLotteryResult;
use App\Console\Commands\BJDCTotalLotteryResult;
use App\Console\Commands\InstallMatchResult;
use App\Console\Commands\PLSLotteryResult;
use App\Console\Commands\SyncBJDCResult;
use App\Console\Commands\SyncMatchResult;
use App\Console\Commands\SyncPlsResult;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(InstallMatchResult::class)->cron("5,17,35 * * * *")->withoutOverlapping();

        $schedule->command(SyncMatchResult::class)->everyMinute()->withoutOverlapping();
        $schedule->command(SyncPlsResult::class)->everyMinute()->withoutOverlapping();
        $schedule->command(SyncBJDCResult::class)->everyMinute()->withoutOverlapping();
        $schedule->command(PLSLotteryResult::class)->cron("35 * * * *")->withoutOverlapping();
//        $schedule->command(BJDCLotteryResult::class)->cron("35 * * * *")->withoutOverlapping();
        $schedule->command(BJDCTotalLotteryResult::class)->cron("35 * * * *")->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
