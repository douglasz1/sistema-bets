<?php

namespace Bets\Services\Supervisor;


use Bets\Models\Bet;
use Bets\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Supervisor BetsService
 */
class BetsService extends BaseService
{
    protected $modelClass = Bet::class;

    private $betId;
    private $companiesIDs;
    private $finalDate;
    private $startDate;
    private $status;
    private $sellers;
    private $manager;

    public function getAll()
    {
        $query = $this->newQuery();

        $status = $this->status ?? 'all';

        if ($status === 'partial') {
            $query->where('bets.status', 'pending');
            $query->whereHas('tips', function ($query) {
                $query->where('tips.status', 'win')->limit(1);
            });
        } elseif ($status !== 'all' && $status !== 'partial') {
            $query->where('bets.status', $status);
        }

        $startDate = $this->startDate ?? Carbon::now()->toDateString();
        $query->whereDate('bets.created_at', '>=', $startDate);

        $finalDate = $this->finalDate ?? Carbon::now()->toDateString();
        $query->whereDate('bets.created_at', '<=', $finalDate);

        $query->whereIn('bets.company_id', $this->companiesIDs);

        if (isset($this->sellers)) {
            $query->whereIn('seller_id', $this->sellers);
        }

        if (isset($this->manager) && !isset($this->sellers)) {
            $query->select('users.id', 'users.user_id', 'bets.*')
                ->join('users', function ($join) {
                    $join->on('users.user_id', '=', DB::raw("{$this->manager}"))
                        ->orOn('users.id', '=', DB::raw("{$this->manager}"));
                })
                ->whereRaw("bets.seller_id = users.id");
        }

        $query->with([
            'seller' => function ($query) {
                return $query->select('name', 'id', 'profit_percentage', 'manager_commission', 'user_id')
                    ->with([
                        'manager' => function ($query) {
                            return $query->select('name', 'id', 'profit_percentage', 'manager_commission')
                                ->with('roles');
                        },
                        'roles'
                    ]);
            },
            'company'
        ]);

        $query->orderBy('created_at', 'DESC');

        $query->whereNotNull('seller_id');

        return $this->doQuery($query, false, false);
    }

    public function search()
    {
        $query = $this->newQuery();

        $query->whereNotNull('seller_id');

        $query->whereIn('company_id', $this->companiesIDs);

        $query->where('id', 'like', "{$this->betId}%");

        $query->with([
            'seller' => function ($query) {
                return $query->select('name', 'id', 'profit_percentage', 'manager_commission', 'user_id')
                    ->with([
                        'manager' => function ($query) {
                            return $query->select('name', 'id', 'profit_percentage', 'manager_commission')
                                ->with('roles');
                        },
                        'roles'
                    ]);
            },
            'company'
        ]);

        $query->orderBy('created_at', 'DESC');

        return $this->doQuery($query, 10, false);
    }

    /**
     * @param mixed $finalDate
     * @return BetsService
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;
        return $this;
    }

    /**
     * @param mixed $startDate
     * @return BetsService
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @param mixed $status
     * @return BetsService
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param mixed $sellers
     * @return BetsService
     */
    public function setSellers($sellers)
    {
        if (is_array($sellers)) {
            $this->sellers = $sellers;

        } elseif (!is_null($sellers) && $sellers > 0) {
            $this->sellers = [$sellers];
        }

        return $this;
    }

    /**
     * @param mixed $companiesIDs
     * @return BetsService
     */
    public function setCompaniesIDs($companiesIDs)
    {
        if (is_array($companiesIDs)) {
            $this->companiesIDs = $companiesIDs;

        } elseif (!is_null($companiesIDs) && $companiesIDs > 0) {
            $this->companiesIDs = [$companiesIDs];
        }

        return $this;
    }

    /**
     * @param mixed $manager
     * @return BetsService
     */
    public function setManager($manager): BetsService
    {
        if (!is_null($manager) && $manager > 0) {
            $this->manager = $manager;
        }

        return $this;
    }

    /**
     * @param mixed $betId
     * @return BetsService
     */
    public function setBetId($betId)
    {
        $this->betId = $betId;
        return $this;
    }
}
