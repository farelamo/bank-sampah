<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GarbageCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection->transform(function($data){
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'price'=> $data->price,
                    'unit' => $data->unit,
                ];
            })
        ];
    }
}
