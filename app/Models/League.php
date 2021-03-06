<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'name', 'flag', 'order',
        'league_id',
        'sport_id',
        'country_id',
        'manual',
    ];

    public function matches()
    {
        return $this->hasMany(Match::class, 'league_id', 'league_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'league_id', 'league_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
