<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'bank_name', 'bank_number', 'wallet_number', 'cash_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
