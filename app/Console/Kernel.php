<?php

namespace App\Console;

use App\Jobs\CashOutJob;
use App\Models\CashOutDate;
use App\Console\Commands\CashOutCommands;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:cashout')
                ->dailyAt('22:17')
                ->timezone('Asia/Jakarta')
                ->when(function () {
                    $datePick = CashOutDate::first()->date;
                    if (!$datePick) {
                        return false;
                    }

                    $dateNow = date("Y-m-d");
                    $datePick = date('Y-m-d', strtotime($datePick));
                    if ($dateNow == $datePick) {
                        return true;
                    }

                    return false;
                });
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
