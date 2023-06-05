<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOutDate extends Model
{
    use HasFactory;

    protected $fillable = ['date'];
    protected $table    = 'cash_out_date';
}
