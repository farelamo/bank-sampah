<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\User;
use App\Models\CashOut;
use App\Models\Garbage;
use App\Models\CashOutDate;
use Illuminate\Http\Request;
use App\Models\GarbageDeposit;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\GarbageDepositRequest;
use App\Http\Resources\GarbageDepositCollection;
use App\Http\Resources\GarbageDepositShowCollection;

class GarbageDepositController extends Controller
{
    public function __construct() {
        $this->middleware('superadminAdmin')->except(['transaction', 'show']);
    }

    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function index(Request $request)
    {
        try {

            if ($request->nasabah) :
                $nasabah = User::where('name', $request->nasabah)->first();
                if (!$nasabah) {
                    return $this->returnCondition(false, 404, 'nasabah with name ' . $request->nasabah . ' not found');
                }

                if ($nasabah->role != 'nasabah') {
                    return $this->returnCondition(false, 422, 'Invalid nasabah role');
                }
            endif;

            $data = $nasabah ?? 0;
            $deposit = GarbageDeposit::distinct()
                            ->when($request->nasabah, function ($e) use($data) {
                                $e->where('nasabah_id', $data->id);
                            })
                            ->orderBy('date', 'desc')
                            ->select('nasabah_id', 'date')
                            ->paginate(5);

            return new GarbageDepositCollection($deposit);
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function show($id, Request $request)
    {
        if(!$request->date){
            return $this->returnCondition(false, 400, 'date must be filled');
        }

        $check = User::where('id', $id)->first();
        if (!$check) {
            return $this->returnCondition(false, 404, 'nasabah with id ' . $id . ' not found');
        }

        if ($check->role != 'nasabah') {
            return $this->returnCondition(false, 422, 'Invalid nasabah role');
        }

        $deposit = GarbageDeposit::where('nasabah_id', $id)->where('date', $request->date)->get();
        if(empty($deposit->toArray())) return $this->returnCondition(false, 404, 'record not found');

        return new GarbageDepositShowCollection($deposit);
    }

    public function checkDeposit($request)
    {
        $rules = [
            'deposit' => 'required|array',
            'deposit.*.garbage_id' => 'required|exists:garbages,id',
            'deposit.*.weight' => 'required|numeric',
        ];

        Validator::make($request->all(), $rules, $messages =
            [
                'deposit.array' => 'deposit must be type of array',
                'deposit.required' => 'deposit must be filled',
                'deposit.*.garbage_id.required' => 'garbage_id :index must be filled',
                'deposit.*.garbage_id.exists' => "garbage_id :index doesn't exist",
                'deposit.*.weight.required' => 'weight :index must be filled',
                'deposit.*.weight.numeric' => 'weight :index must be numeric',
            ])->validate();
    }

    public function handleResult(&$result, $deposit, $date)
    {
        $id = $deposit['garbage_id'];
        array_shift($deposit);

        $price = Garbage::where('id', $id)->first()->price;

        $deposit['date'] = $date;
        $deposit['price'] = $price;
        return $result['attach'][$id] = $deposit;
    }

    public function handleDeposit($request, $date, $data = null, $update = false)
    {
        $result = [];

        $old = $update ? $data->garbage_deposits()->where('date', $date)->get()->map(function($e) {
                    return $e->garbage_id;
                })->toArray() : [];

        $garbageId = $update ? array_column($request, 'garbage_id') : [];
        $detachOld = $update ? array_diff($old, $garbageId) : [];
        $attachNew = $update ? array_diff($garbageId, $old) : [];

        if($detachOld){
            $result['detach'] = $detachOld;
        }

        foreach ($request as $deposit) {
            if(!$update):
                $this->handleResult($result, $deposit, $date);
            endif;

            if($update):
                if(in_array($deposit['garbage_id'], $attachNew)){
                    $this->handleResult($result, $deposit, $date);
                }
            endif;
        }

        return $result;
    }

    public function store(GarbageDepositRequest $request)
    {
        if(!$request->nasabah_id){
            return $this->returnCondition(false, 400, 'nasabah_id must be filled');
        }

        $nasabah = User::where('id', $request->nasabah_id)->first();
        if (!$nasabah) {
            return $this->returnCondition(false, 404, 'nasabah with id ' . $nasabah_id . ' not found');
        }

        if ($nasabah->role != 'nasabah') {
            return $this->returnCondition(false, 422, 'Invalid nasabah role');
        }

        $this->checkDeposit($request);

        try {
            $nasabah->garbages()->attach($this->handleDeposit($request->deposit, $request->date, null)['attach']);
            
            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update($id, GarbageDepositRequest $request)
    {

        $nasabah = User::where('id', $id)->first();
        if (!$nasabah) {
            return $this->returnCondition(false, 404, 'nasabah with id ' . $id . ' not found');
        }

        if ($nasabah->role != 'nasabah') {
            return $this->returnCondition(false, 422, 'Invalid nasabah role');
        }

        $check = $nasabah->garbages()->wherePivot('nasabah_id', $nasabah->id)
                         ->wherePivot('date', $request->date)
                         ->get();
                         
        if(empty($check->toArray())){
            return $this->returnCondition(false, 404, 'deposit with id ' . $nasabah->id . ' on date ' . $request->date . ' not found');
        }

        $this->checkDeposit($request);

        try {

            $result = $this->handleDeposit($request->deposit, $request->date, $nasabah, true);
            if(array_key_exists('attach', $result)) $nasabah->garbages()->attach($result['attach']);
            if(array_key_exists('detach', $result)) $nasabah->garbages()->detach($result['detach']);

            return $this->returnCondition(true, 200, 'Successfully update data');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id, Request $request)
    {
        try {

            if (!$request->date) {
                return $this->returnCondition(false, 400, 'date must be filled');
            }

            $nasabah = User::where('id', $id)->first();
            if (!$nasabah) {
                return $this->returnCondition(false, 404, 'nasabah with id ' . $id . ' not found');
            }

            if ($nasabah->role != 'nasabah') {
                return $this->returnCondition(false, 422, 'Invalid nasabah role');
            }

            if(empty($nasabah->garbages()->wherePivot('date', $request->date)->get()->toArray())){
                return $this->returnCondition(false, 404, 'Deposit with id ' . $id . ' on date ' . $request->date . ' not found');
            }

            $nasabah->garbages()->wherePivot('date', $request->date)->detach();

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function transaction(TransactionRequest $request)
    {

        try {

            $nasabah = User::where('name', $request->nasabah)->first();
            if (!$nasabah) {
                return $this->returnCondition(false, 404, 'nasabah with name ' . $request->nasabah . ' not found');
            }

            if ($nasabah->role != 'nasabah') {
                return $this->returnCondition(false, 422, 'Invalid nasabah role');
            }

            $deposit = GarbageDeposit::distinct()
                            ->where('nasabah_id', $nasabah->id)
                            ->whereBetween('date', [$request->start, $request->end])
                            ->orderBy('date', 'desc')
                            ->select('nasabah_id', 'date')
                            ->paginate(5);

            return new GarbageDepositCollection($deposit);
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
