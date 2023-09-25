<?php

namespace App\Console;

use Log;
use Carbon\Carbon;
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
                // ->dailyAt('16:05')
                ->timezone('Asia/Jakarta')
                ->when(function () {
                    $datePick = CashOutDate::first()->date;
                    if (!$datePick) {
                        return false;
                    }

                    
                    $dateNow  = Carbon::now()->setTime(0, 0, 0);
                    $datePick = new Carbon($datePick);
                    $diff     = $dateNow->diff($datePick)->days;
                    if ($diff <= 1) {
                        Log::info('MASUK');
                        return true;
                    }
                    Log::info('KELUAR');
                    
                    return false;
                });
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
