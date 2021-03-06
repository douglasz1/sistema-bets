<?php

namespace Bets\Services\Admin;

use Bets\Models\User;
use Bets\Services\BaseService;
use Carbon\Carbon;

/**
 * Users Service from Admin
 */
class UsersService extends BaseService
{
    protected $modelClass = User::class;

    private $companyID;
    private $whereNotIDs;
    private $onlyActives = true;
    private $finalDate;
    private $startDate;
    private $sellers;
    private $manager;

    public function getWhereNotIn()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->where('company_id', $this->companyID);

        $query->select('id', 'name');

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        $query->whereNotIn('id', $this->whereNotIDs);

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller')
                ->orWhere('name', 'manager');
        });

        return $this->doQuery($query, false, false);
    }

    public function getAllSellers()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->with('company', 'roles');

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller')
                ->orWhere('name', 'manager');
        });

        return $this->doQuery($query, false, false);
    }

    public function getHasBets()
    {
        $query = $this->newQuery();

        $query->select('id', 'name', 'user_id', 'company_id', 'profit_percentage', 'manager_commission');

        if (isset($this->companyID)) {
            $query->where('company_id', $this->companyID);
        }

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

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller')
                ->orWhere('name', 'manager');
        });

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
            'company' => function ($query) {
                return $query->select('id', 'name')->with([
                    'supervisors' => function ($query) {
                        return $query->select('users.id', 'name');
                    }
                ]);
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

        if (isset($this->companyID)) {
            $query->where('company_id', $this->companyID);
        }

        if (isset($this->manager)) {
            $query->where('user_id', $this->manager);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller');
        });

        $nowDateString = Carbon::now()->toDateString();
        $startDate = $this->startDate ?? $nowDateString;
        $finalDate = $this->finalDate ?? $nowDateString;

        $query->whereDoesntHave('bets', function ($query) use ($startDate, $finalDate) {
            return $query->whereDate('bets.created_at', '>=', $startDate)
                ->whereDate('bets.created_at', '<=', $finalDate);
        });

        return $this->doQuery($query, false, false);
    }

    public function zeroToAll()
    {
        $query = $this->newQuery();

        $query->where('active', true);

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller')
                ->orWhere('name', 'manager');
        });

        return $query->update(['balance' => 0]);
    }

    /**
     * @param mixed $companyID
     * @return UsersService
     */
    public function setCompanyID($companyID)
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * @param mixed $whereNotIDs
     * @return UsersService
     */
    public function setWhereNotIDs($whereNotIDs)
    {
        if (is_array($whereNotIDs)) {
            $this->whereNotIDs = $whereNotIDs;

        } elseif (!is_null($whereNotIDs)) {
            $this->whereNotIDs = [$whereNotIDs];
        }

        return $this;
    }

    /**
     * @param bool $onlyActives
     * @return UsersService
     */
    public function setOnlyActives(bool $onlyActives): UsersService
    {
        $this->onlyActives = $onlyActives;
        return $this;
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
