<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Withdraw;
use App\Models\Garbage;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'superadmin',
            'username' => 'superadmin',
            'role'     => 'superadmin',
            'phone'    => '081234567890',
            'address'  => 'Desa Sengguruh',
            'password' => bcrypt('rahasia')
        ]);

        User::factory()
            ->state(
                [
                    'role' => 'admin',
                    'balance' => null
                ]
            )
            ->count(5)
            ->create();

        User::factory()
            ->state(
                [
                    'role' => 'nasabah',
                    'balance' => rand(10000, 100000)
                ]
            )
            ->hasAttached(
                Garbage::factory()->count(2),
                [
                    'weight' => rand(5, 10),
                    'date'   => date('Y-m-d'),
                    'price'  => rand(100000, 500000),
                ]
            )
            ->count(5)
            ->create()
            ->each(function ($user){
                $withdraw = Withdraw::factory()->make();
                $user->withdraw()->save($withdraw);
            });
    }
}
