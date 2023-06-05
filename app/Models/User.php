<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','username','password', 'role', 'balance', 'address', 'phone'];
    protected $hidden   = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function garbage_deposits()
    {
        return $this->hasMany(GarbageDeposit::class, 'nasabah_id', 'id');
    }

    public function garbages()
    {
        return $this->belongsToMany(Garbage::class, 'garbage_deposits', 'nasabah_id', 'garbage_id')
                    ->withPivot('date', 'weight')
                    ->withTimestamps();
    }

    public function withdraw()
    {
        return $this->hasOne(Withdraw::class);
    }

    public function cash_outs()
    {
        return $this->hasMany(CashOut::class);
    }
}
