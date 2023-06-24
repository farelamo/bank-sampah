<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashOutDateRequest;
use App\Http\Resources\CashOutCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\CashOutDate;
use App\Models\CashOut;
use Carbon\Carbon;
use Exception;

class CashOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('superadminAdmin')->except(['getCashOutMine', 'showCashOutDate', 'getPhotoTrx']);
        $this->middleware('nasabah')->only(['getCashOutMine', 'showCashOutDate', 'getPhotoTrx']);
    }

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
            if (!$data) {
                return $this->returnCondition(false, 404, 'data not found');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $data->date,
                ],
            ], 200);
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function cashOutDate(CashOutDateRequest $request)
    {
        try {

            $dateNow = date("Y-m-d");
            $dateReq = date('Y-m-d', strtotime($request->date));
            if ($dateReq < $dateNow) {
                return $this->returnCondition(false, 400, 'Date must be greater or equal than date now');
            }

            $check = CashOutDate::count();
            if ($check >= 1) {
                return $this->returnCondition(false, 400, 'sorry, data has reach the limit, please update old data');
            }

            CashOutDate::create(['date' => $request->date]);

            return $this->returnCondition(true, 200, 'Successfully create data');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function updateCashOutDate(CashOutDateRequest $request)
    {
        try {

            $data = CashOutDate::first();
            if (!$data) {
                return $this->returnCondition(false, 400, 'There is no data, please create first');
            }

            $dateNow = date("Y-m-d");
            $dateReq = date('Y-m-d', strtotime($request->date));
            if ($dateReq < $dateNow) {
                return $this->returnCondition(false, 400, 'Date must be greater or equal than date now');
            }

            $data->update(['date' => $request->date]);

            return $this->returnCondition(true, 200, 'Successfully update data');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function photoTrx($id, Request $request)
    {
        $cashOut = CashOut::where('id', $id)->first();

        if (!$cashOut) {
            return $this->returnCondition(false, 400, 'cash out with id ' . $id . ' not found');
        }

        if ($cashOut->status == 'save') {
            return $this->returnCondition(false, 400, 'Invalid status');
        }

        $rules = [
            'trx_photo' => 'required|mimes:jpg,png,jpeg|max:5048',
        ];

        Validator::make($request->all(), $rules, $messages =
            [
                'trx_photo.required' => 'gambar harus diisi',
                'trx_photo.mimes' => 'gambar harus berupa jpg, png atau jpeg',
                'trx_photo.max' => 'maximum gambar adalah 5 MB',
            ])->validate();

        $imageOld = $cashOut->trx_photo;

        try {

            $imageFile = $request->file('trx_photo');
            $image = time() . '.' . $imageFile->getClientOriginalExtension();
            Storage::putFileAs('public/images', $imageFile, $image);

            $cashOut->update([
                'trx_photo' => $image,

            ]);

            if (Storage::disk('local')->exists('public/images/' . $imageOld)) {
                Storage::delete('public/images/' . $imageOld);
            }

            return $this->returnCondition(true, 200, 'Successfully updated data');
        } catch (Exception $e) {
            if (Storage::disk('local')->exists('public/images/' . $image)) {
                Storage::delete('public/images/' . $image);
            }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function getCashOut()
    {
        try {

            $cashOuts = CashOut::select('id', 'date_transaction', 'user_id', 'cash_out', 'status')->get();
            return new CashOutCollection($cashOuts);
        } catch (\Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function getCashOutMine()
    {
        try {

            if (auth()->user()->role != 'nasabah') {
                return $this->returnCondition(false, 400, 'Invalid role access');
            }

            $cashOuts = CashOut::where('user_id', auth()->user()->id)->get();
            return new CashOutCollection($cashOuts);
        } catch (\Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function getPhotoTrx($id)
    {
        try {

            $cashout = Cashout::where('id', $id)->first();
            if (!$cashout) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
            }

            if (!$cashout->trx_photo) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' doesnt have trx photo');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'image_url' => env('APP_URL') . 'storage/images/' . $cashout->trx_photo
                ]
            ]);
        } catch (\Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
