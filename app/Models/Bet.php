<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'seller_id', 'cancel_id', 'client_name',
        'bet_value', 'quotation_total', 'prize', 'commission',
        'tips_quantity', 'status', 'origin', 'canceled_at', 'client_id'
    ];

    protected $dates = ['deleted_at', 'canceled_at'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function tips()
    {
        return $this->hasMany(Tip::class);
    }

    public function cancelBy()
    {
        return $this->belongsTo(User::class, 'cancel_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
