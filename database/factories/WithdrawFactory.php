<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'ditabung',
            'bank_name' => 'BRI',
            'bank_number' => '1234567890',
            'wallet_number' => '0987654321',
            'cash_out' => 20000,
        ];
    }
}
