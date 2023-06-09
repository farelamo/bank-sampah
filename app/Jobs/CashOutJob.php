<?php

namespace App\Jobs;

use DB;
use Exception;
use App\Models\User;
use App\Models\CashOut;
use App\Models\CashOutDate;
use App\Models\GarbageDeposit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CashOutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function handleQueryUpdate(&$user, &$result, &$cashOutDate, &$inserts, &$params, &$ids, &$cases)
    {
        try {

            $cases[] = "WHEN {$user->id} then ?";
            $ids[] = $user->id;

            // if user has with draw relation
            if ($user->withdraw) {
                // if user has withdraws with status tf/manual
                if ($user->withdraw->type != 'save') {
                    $calculate = ($user->balance + $result[$user->id]) - $user->withdraw->cash_out;
                    $params[] = $calculate < 0 ? 0 : $calculate; // if user cashout more than balance + result
                    $inserts[] = [
                        'user_id' => $user->id,
                        'status' => $user->withdraw->type,
                        'date_transaction' => $cashOutDate,
                        'cash_out' => $calculate < 0 ?
                                        $user->withdraw->cash_out + $calculate : // use + caused $calculate retrieve is - (negative)
                                        $user->withdraw->cash_out,
                    ];

                    return;
                }

                // if user who has withdraw with save type
                $params[] = $user->balance + $result[$user->id];
                $inserts[] = [
                    'user_id' => $user->id,
                    'status' => 'save',
                    'date_transaction' => $cashOutDate,
                    'cash_out' => 0,
                ];

                return;
            }

            // if user has not withdraw (save status automatically)
            $params[] = $user->balance + $result[$user->id];
            $inserts[] = [
                'user_id' => $user->id,
                'status' => 'save',
                'date_transaction' => $cashOutDate,
                'cash_out' => 0,
            ];
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function handle()
    {
        try {
            
            $ids = [];
            $params = [];
            $cases = [];
            $inserts = [];

            $users = User::select('id', 'name', 'balance')->where('role', 'nasabah')->with(['cash_outs' => function ($q) {
                $q->orderBy('date_transaction', 'desc');
            }])->get();

            $cashOutDate = CashOutDate::first()->date;

            $result = [];
            foreach ($users as $user) {
                if (is_null($user->cash_outs->first())) {
                    $deposits = GarbageDeposit::where('nasabah_id', $user->id)->get();

                    if ($deposits->isEmpty()) {
                        continue;
                    }

                    $deposits->map(function ($d) use (&$user, &$result) {
                        if (array_key_exists($user->id, $result)) {
                            return $result[$user->id] += $d->weight * $d->price;
                        }

                        $result[$user->id] = $d->weight * $d->price;
                    });

                    $this->handleQueryUpdate($user, $result, $cashOutDate, $inserts, $params, $ids, $cases);
                    continue;
                }

                $start = $user->cash_outs->first()->date_transaction;

                $deposits = GarbageDeposit::where('nasabah_id', $user->id)
                    ->where('date', '>', $start)
                    ->where('date', '<=', $cashOutDate)
                    ->get();

                $deposits->map(function ($d) use (&$user, &$result) {
                    if (array_key_exists($user->id, $result)) {
                        return $result[$user->id] += $d->weight * $d->price;
                    }

                    $result[$user->id] = $d->weight * $d->price;
                });

                $this->handleQueryUpdate($user, $result, $cashOutDate, $inserts, $params, $ids, $cases);
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);

            if (!empty($ids)) {
                \DB::update("UPDATE `users` SET `balance` = CASE `id` {$cases} END WHERE `id` in ({$ids})", $params);
            }

            \DB::disableQueryLog();

             
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
