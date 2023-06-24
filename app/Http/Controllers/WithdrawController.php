<?php

namespace App\Http\Controllers;

use App\Http\Resources\WithdrawResource;
use App\Http\Requests\WithdrawRequest;
use App\Models\CashOutDate;
use App\Models\Withdraw;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class WithdrawController extends Controller
{
    public function __construct() {
        $this->middleware('nasabah');
    }

    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function show()
    {
        $nasabah = User::where('id', auth()->user()->id)->first();
        if (!$nasabah) {
            return $this->returnCondition(false, 404, 'nasabah not found');
        }

        if ($nasabah->role != 'nasabah') {
            return $this->returnCondition(false, 422, 'Invalid nasabah role');
        }

        return new WithdrawResource($nasabah);
    }

    public function update(WithdrawRequest $request)
    {
        try {
    
            $nasabah = User::where('id', auth()->user()->id)->first();
            if (!$nasabah) {
                return $this->returnCondition(false, 404, 'nasabah not found');
            }

            if ($nasabah->role != 'nasabah') {
                return $this->returnCondition(false, 422, 'Invalid nasabah role');
            }

            $cashOutDate = CashOutDate::first();
            if(!$cashOutDate){
                return $this->returnCondition(false, 400, "please waiting update from admin");
            }
            
            $dateNow = date("Y-m-d");
            $dateReq = date('Y-m-d', strtotime($cashOutDate->date));
            $beforeDateReq = date("Y-m-d", strtotime("-1 day", strtotime($cashOutDate->date)));

            if ($dateNow == $dateReq || $dateNow == $beforeDateReq) {
                return $this->returnCondition(false, 400, "we're sorry, update data not available right now");
            }

            Withdraw::updateOrCreate(
                ['user_id' => $nasabah->id],
                [
                    'type' => $request->type,
                    'bank_name' => $request->bank_name,
                    'bank_number' => $request->bank_number,
                    'wallet_number' => $request->wallet_number,
                    'cash_out' => $request->cash_out,
                ]
            );

            return $this->returnCondition(true, 200, 'Successfully Update Data');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal Server Error');
        }
    }
}
