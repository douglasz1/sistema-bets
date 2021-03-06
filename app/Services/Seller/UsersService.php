<?php

namespace Bets\Services\Seller;


use Bets\Models\User;
use Bets\Services\BaseService;
use Carbon\Carbon;

class UsersService extends BaseService
{
    protected $modelClass = User::class;

    private $finalDate;
    private $startDate;
    private $seller;
    private $client;

    public function getHasBets()
    {
        $query = $this->newQuery();

        $query->select('id', 'name', 'profit_percentage');

        $query->where('id', $this->seller);

        $nowDateString = Carbon::now()->toDateString();
        $startDate = $this->startDate ?: $nowDateString;
        $finalDate = $this->finalDate ?: $nowDateString;

        $query->with([
            'bets' => function ($query) use ($startDate, $finalDate) {
                if($this->client) {
                    $query->where('client_id', $this->client);
                }

                return $query->whereDate('bets.created_at', '>=', $startDate)
                    ->whereDate('bets.created_at', '<=', $finalDate);
            },
            'apostasBolao' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('bolao_apostas.created_at', '>=', $startDate)
                    ->whereDate('bolao_apostas.created_at', '<=', $finalDate)
                    ->where('situacao', '!=', 'cancelado');
            },
            'payments' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('payments.date', '>=', $startDate)
                    ->whereDate('payments.date', '<=', $finalDate);
            },
            'expenses' => function ($query) use ($startDate, $finalDate) {
                return $query->whereDate('expenses.date', '>=', $startDate)
                    ->whereDate('expenses.date', '<=', $finalDate);
            }
        ]);

        return $this->doQuery($query, false, false);
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

    public function setSeller($seller)
    {
        if (!is_null($seller) && $seller > 0) {
            $this->seller = $seller;
        }

        return $this;
    }

    public function setClient($client)
    {
        if (!is_null($client) && $client > 0) {
            $this->client = $client;
        }

        return $this;
    }
}
