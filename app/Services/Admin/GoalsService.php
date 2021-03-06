<?php

namespace Bets\Services\Admin;


use Bets\Models\User;
use Bets\Services\BaseService;
use Carbon\Carbon;

class GoalsService extends BaseService
{
    protected $modelClass = User::class;

    private $companyID;
    private $finalDate;
    private $startDate;

    public function getSellers()
    {
        $query = $this->newQuery();

        $query->select('id', 'name', 'user_id', 'company_id', 'sales_goal');

        $query->where('active', true);

        if (isset($this->companyID)) {
            $query->where('company_id', $this->companyID);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller');
        });

        $nowDateString = Carbon::now()->toDateString();
        $startDate = $this->startDate ?? $nowDateString;
        $finalDate = $this->finalDate ?? $nowDateString;

        $query->with([
            'bets' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bets.created_at', '>=', $startDate)
                    ->whereDate('bets.created_at', '<=', $finalDate)
                    ->where('status', '!=', 'canceled');
            },
            'company' => function ($query) {
                return $query->select('id', 'name');
            }
        ]);

//        return $query->toSql();

        return $this->doQuery($query, false, false);
    }

    /**
     * @param mixed $companyID
     * @return GoalsService
     */
    public function setCompanyID($companyID)
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * @param mixed $finalDate
     * @return GoalsService
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;
        return $this;
    }

    /**
     * @param mixed $startDate
     * @return GoalsService
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

}