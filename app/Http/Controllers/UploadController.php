<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\{
    data_nasabah,
    data_sampah,
    trx_sampah,
};
use App\Models\Garbage;
use Exception;
use Log;
use Arr;
use DB;
class UploadController extends Controller
{
    public function data_trx(Request $request) {
        $import = new trx_sampah();
        try {
            $result = $import->toArray($request->file('file'));
            // $result = collect($result[0])->groupBy('tanggal');
            $result = collect($result[0])->filter(function($value, $key){
                // Log::info(is_array($value));
                // Log::info($value->toArray());
                return array_filter($key, function($k){
                    Log::info(!is_null($k));
                    return !is_null($k);
                    // return array_filter($v, function($item){
                    //     return !is_null($item);
                    // });
                });
            });

            $nama   = "kresek_plastik_blok";
            $fix_nama = ucwords(str_replace("kg", "",str_replace("_", " ", $nama)));

            $cek    = Garbage::where('name', 'like', '%' . $fix_nama . '%')->first();
            return response()->json([
                'message' => 'success',
                'data'    => $result
            ], 200);
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $failures = $e->failures();
            return $this->returnCondition(false, 400, 'Data tidak valid! Silahkan cek kembali');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, $e->getMessage());
        }
    }
    public function data_nasabah(Request $request) {
        $import = new data_nasabah();
        try {
            $result = $import->toArray($request->file('file'));

            foreach(array_chunk($result[0], 100) as $responseChunk){
                $result = [];
                foreach ($responseChunk as $item) {
                    $result[] = [
                        'name'      => $item['nama'],
                        'username'  => $item['kode_pelanggan'],
                        'no_member' => $item['kode_pelanggan'],
                        'role'      => 'nasabah',
                        'balance'   => 0,
                        'phone'     => 0,
                        'address'   => $item['alamat'],
                        'password'  => bcrypt('rahasia'),
                    ];
                }
                DB::disableQueryLog();
                DB::table('users')->insert($result);
            };

            return response()->json([
                'status'  => true,
                'message' => 'Successfully import data',
                'data'    => [],
            ], 200);
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $failures = $e->failures();
            return $this->returnCondition(false, 400, 'Data tidak valid! Silahkan cek kembali');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, $e->getMessage());
        }

    }

    public function data_sampah(Request $request){
        $import = new data_sampah();
        try {
            $ids         = [];
            $cases       = [];
            $priceValues = [];
            $unitValues  = [];
            $inserts     = [];

            $result   = $import->toArray($request->file('file'));
            $garbages = Garbage::select('id','name', 'price', 'unit')->get()->toArray();

            foreach ($result[0] as $item) {
                $check = Arr::first($garbages, function($garbages) use ($item){
                    return $garbages['name'] == $item['jenis_sampah'];
                });
                
                if($check){
                    if($check['price'] != $item['harga'] || $check['unit'] != $item['satuan']){
                        $cases[]       = "WHEN {$check['id']} then ?";
                        $ids[]         = $check['id'];
                        $priceValues[] = $item['harga'];
                        $unitValues[]  = $item['satuan'];
                    }
                    continue;
                }

                $inserts[] = [
                    'name'  => $item['jenis_sampah'],
                    'price' => $item['harga'],
                    'unit'  => $item['satuan'],
                ];
            }

            $ids    = implode(',', $ids);
            $cases  = implode(' ', $cases);
            $values = array_merge($priceValues, $unitValues);

            if (!empty($ids)) {
                DB::update(
                    "UPDATE `garbages` SET `price` = CASE `id` {$cases} END,
                    `unit` = CASE `id` {$cases} END
                    WHERE `id` in ({$ids})", $values
                );
            }

            Garbage::insert($inserts);

            return response()->json([
                'status'  => true,
                'message' => 'Successfully import data',
                'data'    => [],
            ], 200);
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $failures = $e->failures();
            return $this->returnCondition(false, 400, 'Data tidak valid! Silahkan cek kembali');
        }catch(Exception $e){
            return $this->returnCondition(false, 500, "Internal Server Error");
        }
    }
}