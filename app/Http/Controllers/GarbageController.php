<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Garbage;
use App\Http\Resources\BaseResource;
use App\Http\Requests\GarbageRequest;
use App\Http\Resources\GarbageCollection;

class GarbageController extends Controller
{
    public function __construct() {
        $this->middleware('superadminAdmin')->except(['index']);
    }

    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function index()
    {
        $garbages = Garbage::select('id', 'name', 'price', 'unit')->paginate(5);
        return new GarbageCollection($garbages);
    }

    public function show($id)
    {
        $garbage = Garbage::where('id', $id)->first();
        if(!$garbage) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

        return new BaseResource($garbage);
    }

    public function store(GarbageRequest $request)
    {
        try {
            Garbage::create([
                'name'  => $request->name,
                'price' => $request->price,
                'unit'  => $request->unit,
            ]);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update($id, GarbageRequest $request)
    {
        try {
            $garbage = Garbage::where('id', $id)->first();
            if (!$garbage) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
            }

            $garbage->update([
                'name'  => $request->name,
                'price' => $request->price,
                'unit'  => $request->unit,
            ]);

            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function destroy($id)
    {
        try {
            $garbage = Garbage::where('id', $id)->first();
            if (!$garbage) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
            }

            $garbage->garbage_deposits()->delete();
            $garbage->delete();

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }
}
