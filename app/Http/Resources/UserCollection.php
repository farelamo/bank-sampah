<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'username' => $data->username,
                    'phone' => $data->phone,
                    'balance' => $data->role == 'nasabah' ? $data->balance : 0,
                    'address' => $data->address
                ];
            }),
        ];
    }
}
