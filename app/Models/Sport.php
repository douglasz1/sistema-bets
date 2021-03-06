<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'api_id',
        'name',
        'slug',
        'active',
    ];

    public function leagues()
    {
        return $this->hasMany(League::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
