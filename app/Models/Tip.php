<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    protected $fillable = [
        'bet_id',
        'match_id', 'match_date',
        'bet_slug', 'bet_name',
        'choice_name', 'choice_slug',
        'value', 'status'
    ];

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    public function match()
    {
        return $this->belongsTo(Result::class, 'match_id', 'match_id');
    }
}
