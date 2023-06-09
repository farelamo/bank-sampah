<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashOutDate;
use App\Jobs\CashOutJob;

class CashOutCommand extends Command
{
    protected $signature = 'app:cashout';
    protected $description = 'Running Cashout Task';

    public function handle()
    {
        dispatch(new CashOutJob());
    }
}
