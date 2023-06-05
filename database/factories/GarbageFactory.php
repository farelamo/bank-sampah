<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GarbageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => rand(1000, 10000),
            'unit' => 'kg'
        ];
    }
}
