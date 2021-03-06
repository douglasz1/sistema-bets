<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'match_id',
        'league_id',
        'sport_id',
        'sport_slug',
        'home_team',
        'away_team',
        'match_date',
        'home_1st',
        'away_1st',
        'home_2nd',
        'away_2nd',
        'home_final',
        'away_final',
        'status',
        'have_tips'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'match_date'
    ];

    protected $casts = [
        'have_tips' => 'boolean',
    ];

    protected $appends = ['match_name'];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id', 'league_id');
    }

    public function tips()
    {
        return $this->hasMany(Tip::class, 'match_id', 'match_id');
    }

    public function getMatchNameAttribute()
    {
        if (empty(trim($this->away_team))) {
            return $this->attributes['home_team'];
        }

        return $this->attributes['home_team'] . " x " . $this->attributes['away_team'];
    }

    public function matchName()
    {
        if (empty(trim($this->away_team))) {
            return $this->attributes['home_team'];
        }

        return $this->attributes['home_team'] . " x " . $this->attributes['away_team'];
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}
