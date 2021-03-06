<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'match_id',
        'bet_id',
        'bet_name',
        'bet_slug',
        'choice_id',
        'choice_name',
        'choice_slug',
        'bet_order',
        'choice_order',
        'value',
        'upgradable',
    ];

    protected $casts = [
        'upgradable' => 'boolean',
        'value' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function match()
    {
        return $this->belongsTo(Match::class);
    }
}
