<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garbage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'unit'];

    public function garbage_deposits()
    {
        return $this->hasMany(GarbageDeposit::class);
    }
}
