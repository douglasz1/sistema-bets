<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationCategory extends Model
{
    protected $fillable = [
        'name', 'label', 'quotation_modifier', 'order', 'active', 'sport_id'
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}
