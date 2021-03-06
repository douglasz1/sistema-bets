<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'print_name',
        'quotation_modifier',
        'max_prize',
        'max_prize_multiplier',
        'bets_limit', 'rules'
    ];

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'company_supervisor')->withPivot('percentage');
    }
}
