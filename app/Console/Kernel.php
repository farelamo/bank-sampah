<?php

namespace App\Console;

use App\Jobs\CashOutJob;
use App\Models\CashOutDate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->job(new CashOutJob())->dailyAt('00:00')->timezone('Asia/Jakarta')
        //          ->when(function (){
                    
        //             $datePick = CashOutDate::first()->date;
        //             if(!$datePick){
        //                 return false;
        //             }

        //             $dateNow  = date("Y-m-d");
        //             $datePick = date('Y-m-d', strtotime($datePick));
        //             if ($dateNow == $datePick) {
        //                 return true;
        //             }

        //             return false;
        //          });

        $schedule->job(new CashOutJob())->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
