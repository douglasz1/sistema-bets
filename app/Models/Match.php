<?php

namespace Bets\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $fillable = [
        'match_id',
        'league_id',
        'sport_id',
        'sport_slug',
        'home_team',
        'away_team',
        'match_date',
        'quotations_qty',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'match_date'
    ];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id', 'league_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function matchDate()
    {
        $tempDate = Carbon::parse($this->attributes['match_date']);

        if ($tempDate->isToday())
            return "Hoje às " . $tempDate->format('H:i');
        elseif ($tempDate->isTomorrow())
            return "Amanhã às " . $tempDate->format('H:i');
        else
            return $tempDate->format('d/m/Y H:i');
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

    public function cotacoesPrincipais()
    {
        return $this->hasMany(Quotation::class)
            ->where('bet_slug', 'full_time_result')
            ->orWhere('bet_slug', 'to_win_match');
    }
}
