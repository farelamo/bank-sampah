<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarbageDeposit extends Model
{
    use HasFactory;

    protected $flillable = ['garbage_id', 'nasabah_id', 'date', 'weight', 'price'];

    public function garbage()
    {
        return $this->belongsTo(Garbage::class);
    }

    public function nasabah()
    {
        return $this->belongsTo(User::class, 'nasabah_id');
    }
}
