<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'username' => fake()->unique()->word(),
            'password' => bcrypt('rahasia'),
            'role'     => 'nasabah',
            'phone'    => rand(10000000000000, 50000000000000),
            'address'  => fake()->realText(500),
            'balance'  => rand(100000, 900000)
        ];
    }
}
