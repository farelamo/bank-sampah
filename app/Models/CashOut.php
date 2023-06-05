<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOut extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date_transaction', 'cash_out', 'trx_photo', 'status'];

    public function nasabah()
    {
        return $this->belongsTo(User::class);
    }
}
