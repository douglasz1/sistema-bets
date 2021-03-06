<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'seller_id', 'company_id',
        'description', 'value', 'date'
    ];

    protected $dates = ['deleted_at', 'date'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
