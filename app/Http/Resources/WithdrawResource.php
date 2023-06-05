<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'name' => $this->resource->name,
                'type' => $this->withdraw->type ?? '',
                'bank_name' => $this->withdraw->bank_name ?? '',
                'bank_number' => $this->withdraw->bank_number ?? '',
                'wallet_number' => $this->withdraw->wallet_number ?? '',
                'cash_out' => $this->withdraw->cash_out ?? '',
            ]
        ];
    }
}
