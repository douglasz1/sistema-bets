<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'seller_id',
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class, 'client_id');
    }
}
