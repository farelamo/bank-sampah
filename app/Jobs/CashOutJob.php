<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\GarbageDeposit;
use Illuminate\Bus\Queueable;
use App\Models\CashOutDate;
use App\Models\User;
use Log;

class CashOutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $users = User::select('id', 'name')->get();
        foreach ($users as $user) {
            if($user->id == 10){
                Log::info($user->cash_outs()->get());
            }
        }
    }
}
