<?php

namespace Bets\Services\Manager;


use Bets\Models\User;
use Bets\Services\BaseService;
use Carbon\Carbon;

class UsersService extends BaseService
{
    protected $modelClass = User::class;

    private $finalDate;
    private $startDate;
    private $sellers;
    private $manager;

    public function getHasBets()
    {
        $query = $this->newQuery();

        $query->select('id', 'name', 'user_id', 'company_id', 'profit_percentage', 'manager_commission');

        if (isset($this->manager)) {

            if (isset($this->sellers)) {
                $query->where('id', $this->sellers);
            } else {
                $query->where(function ($query) {
                    return $query->where('user_id', $this->manager)
                        ->orWhere('id', $this->manager);
                });
            }

        } elseif (isset($this->sellers)) {
            $query->where('id', $this->sellers);
        }

        $nowDateString = Carbon::now()->toDateString();
        $startDate = $this->startDate ?? $nowDateString;
        $finalDate = $this->finalDate ?? $nowDateString;

        $query->where(function ($query) use ($startDate, $finalDate) {
            $query->whereHas('bets', function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bets.created_at', '>=', $startDate)
                    ->whereDate('bets.created_at', '<=', $finalDate);
            });

            $query->orWhereHas('apostasBolao', function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bolao_apostas.created_at', '>=', $startDate)
                    ->whereDate('bolao_apostas.created_at', '<=', $finalDate);
            });

            $query->orWhereHas('expenses', function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('expenses.date', '>=', $startDate)
                    ->whereDate('expenses.date', '<=', $finalDate);
            });
        });

        $query->with([
            'bets' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bets.created_at', '>=', $startDate)
                    ->whereDate('bets.created_at', '<=', $finalDate);
            },
            'apostasBolao' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bolao_apostas.created_at', '>=', $startDate)
                    ->whereDate('bolao_apostas.created_at', '<=', $finalDate)
                    ->where('situacao', '!=', 'cancelado');
            },
            'expenses' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('expenses.date', '>=', $startDate)
                    ->whereDate('expenses.date', '<=', $finalDate);
            },
            'payments' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('payments.date', '>=', $startDate)
                    ->whereDate('payments.date', '<=', $finalDate);
            },
            'manager' => function ($query) {
                return $query->select('id', 'name', 'user_id', 'company_id', 'profit_percentage', 'manager_commission');
            }
        ]);

        return $this->doQuery($query, false, false);
    }

    public function getDoesntHaveBets()
    {
        $query = $this->newQuery();

        $query->select('id', 'name', 'user_id', 'company_id');

        $query->where('user_id', $this->manager);

        $nowDateString = Carbon::now()->toDateString();
        $startDate = $this->startDate ?? $nowDateString;
        $finalDate = $this->finalDate ?? $nowDateString;

        $query->whereDoesntHave('bets', function ($query) use ($startDate, $finalDate) {
            return $query->whereDate('bets.created_at', '>=', $startDate)
                ->whereDate('bets.created_at', '<=', $finalDate);
        });

        return $this->doQuery($query, false, false);
    }

    public function getAllSellers()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->with('company', 'roles');

        $query->where('active', true);

        if (isset($this->manager)) {

            if (isset($this->sellers)) {
                $query->where('id', $this->sellers);
            } else {
                $query->where(function ($query) {
                    return $query->where('user_id', $this->manager)
                        ->orWhere('id', $this->manager);
                });
            }

        } elseif (isset($this->sellers)) {
            $query->where('id', $this->sellers);
        }

        return $this->doQuery($query, false, false);
    }

    public function zeroToAll()
    {
        $query = $this->newQuery();

        $query->where('active', true);

        if (isset($this->manager)) {

            if (isset($this->sellers)) {
                $query->where('id', $this->sellers);
            } else {
                $query->where(function ($query) {
                    return $query->where('user_id', $this->manager)
                        ->orWhere('id', $this->manager);
                });
            }

        } elseif (isset($this->sellers)) {
            $query->where('id', $this->sellers);
        }

        return $query->update(['balance' => 0]);
    }

    /**
     * @param mixed $finalDate
     * @return UsersService
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;
        return $this;
    }

    /**
     * @param mixed $startDate
     * @return UsersService
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function setSellers($sellers)
    {
        if (!is_null($sellers) && $sellers > 0) {
            $this->sellers = $sellers;
        }

        return $this;
    }

    public function setManager($manager)
    {
        if (!is_null($manager) && $manager > 0) {
            $this->manager = $manager;
        }

        return $this;
    }
}
