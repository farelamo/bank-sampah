<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CashOutCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function($data){
                return [
                    'id' => $data->id,
                    'user' => $data->nasabah->name,
                    'date_transaction' => $data->date_transaction,
                    'cash_out' => $data->cash_out,
                    'status' => $data->status,
                ];
            })
        ];
    }
}
