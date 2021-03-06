<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'api_id', 'name',
    ];
}
