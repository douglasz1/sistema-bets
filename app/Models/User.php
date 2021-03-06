<?php

namespace Bets\Models;

use Bets\Models\Bolao\Aposta;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'photo', 'username', 'password',
        'quotation_modifier', 'profit_percentage', 'manager_commission',
        'balance', 'daily_limit', 'limit', 'sales_goal',
        'one_tip_quotation_min', 'two_tip_quotation_min', 'three_tip_quotation_min',
        'max_prize', 'max_prize_multiplier', 'tips_min', 'tips_max',
        'commission1', 'value_min1', 'value_max1',
        'commission2', 'value_min2', 'value_max2',
        'commission3', 'value_min3', 'value_max3',
        'commission6', 'value_min6', 'value_max6',
        'commission11', 'value_min11', 'value_max11',
        'commission16', 'value_min16', 'value_max16',
        'active', 'comments', 'user_id', 'company_id',
        'comissao_ao_vivo', 'ao_vivo',
        'percentual_premio',
    ];

    protected static $logName = 'usuario';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    protected static $logAttributes = [
        'name', 'username',
        'quotation_modifier', 'profit_percentage', 'manager_commission',
        'daily_limit', 'limit', 'sales_goal',
        'one_tip_quotation_min', 'two_tip_quotation_min', 'three_tip_quotation_min',
        'max_prize', 'max_prize_multiplier', 'tips_min', 'tips_max',
        'commission1', 'value_min1', 'value_max1',
        'commission2', 'value_min2', 'value_max2',
        'commission3', 'value_min3', 'value_max3',
        'commission6', 'value_min6', 'value_max6',
        'commission11', 'value_min11', 'value_max11',
        'commission16', 'value_min16', 'value_max16',
        'comments', 'user_id', 'company_id',
        'comissao_ao_vivo', 'ao_vivo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'ao_vivo' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasPermission(Permission $permission)
    {
        return $this->hasRoles($permission->roles);
    }

    public function hasRoles($roles)
    {
        if (is_array($roles) || is_object($roles)) {
            return $roles->intersect($this->roles)->count();
        }
        return $this->roles->contains('name', $roles);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager()
    {
        return $this->parent();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_supervisor')->withPivot('percentage');
    }

    public function bets()
    {
        return $this->hasMany(Bet::class, 'seller_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'seller_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'seller_id');
    }

    public function salesGoal()
    {
        $betsValue = $this->bets()
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime('monday this week')))
                        ->whereDate('created_at', '<=', date('Y-m-d', strtotime('sunday this week')))
                        ->sum('bet_value');

        return round(($betsValue / $this->attributes['sales_goal']) * 100, 0);
    }

    public function quotationModifier()
    {
        return $this->attributes['quotation_modifier'] / 100;
    }

    public function getCommission($quotationsQty)
    {
        if ($quotationsQty === 1) {
            return $this->attributes['commission1'] / 100;
        } elseif ($quotationsQty === 2) {
            return $this->attributes['commission2'] / 100;
        } elseif ($quotationsQty >= 3 && $quotationsQty <= 5) {
            return $this->attributes['commission3'] / 100;
        } elseif ($quotationsQty >= 6 && $quotationsQty <= 10) {
            return $this->attributes['commission6'] / 100;
        } elseif ($quotationsQty >= 11 && $quotationsQty <= 15) {
            return $this->attributes['commission11'] / 100;
        }

        return $this->attributes['commission16'] / 100;
    }

    public function apostasBolao()
    {
        return $this->hasMany(Aposta::class, 'vendedor_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
