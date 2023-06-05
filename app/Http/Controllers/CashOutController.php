<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashOutDateRequest;
use Illuminate\Http\Request;
use App\Models\CashOutDate;
use App\Models\CashOut;
use Carbon\Carbon;
use Exception;

class CashOutController extends Controller
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    } 

    public function showCashOutDate()
    {
        try {

            $data = CashOutDate::first();
            if(!$data){
                return $this->returnCondition(false, 404, 'data not found');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $data->date,
                ],
            ], 200);
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function cashOutDate(CashOutDateRequest $request)
    {
        try {

            $dateNow = date("Y-m-d");
            $dateReq = date('Y-m-d', strtotime($request->date));
            if($dateReq < $dateNow){
                return $this->returnCondition(false, 400, 'Date must be greater or equal than date now');
            }

            $check = CashOutDate::count();
            if($check >= 1){
                return $this->returnCondition(false, 400, 'sorry, data has reach the limit, please update old data');
            }

            CashOutDate::create(['date' => $request->date]);

            return $this->returnCondition(true, 200, 'Successfully create data');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function updateCashOutDate(CashOutDateRequest $request)
    {
        try {

            $data = CashOutDate::first();
            if(!$data){
                return $this->returnCondition(false, 400, 'There is no data, please create first');
            }

            $dateNow = date("Y-m-d");
            $dateReq = date('Y-m-d', strtotime($request->date));
            if ($dateReq < $dateNow) {
                return $this->returnCondition(false, 400, 'Date must be greater or equal than date now');
            }

            $data->update(['date' => $request->date]);

            return $this->returnCondition(true, 200, 'Successfully update data');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
