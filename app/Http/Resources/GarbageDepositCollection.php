<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GarbageDepositCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function ($data) {
                return [
                    'nasabah' => [
                        'id' => $data->nasabah->id,
                        'name' => $data->nasabah->name
                    ],
                    'date' => $data->date,
                ];
            }),
        ];
    }
}
