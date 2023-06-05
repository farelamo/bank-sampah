<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GarbageDepositShowCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $total = 0;
        foreach ($this->collection as $data) {
            $total += $data->weight * $data->price;
        }

        return [
            'success' => true,
            'data' => [
                'garbages' => $this->collection->transform(function ($data) {
                    return [
                        'garbage' => $data->garbage->name,
                        'weight' => $data->weight,
                        'price' => $data->price,
                    ];
                }),
                'total' => $total
            ],
        ];
    }
}
