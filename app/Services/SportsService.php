<?php


namespace Bets\Services;


use Bets\Models\Sport;

class SportsService extends BaseService
{
    private $actives = false;

    protected $modelClass = Sport::class;

    public function all()
    {
        $query = $this->newQuery();

        if ($this->actives) {
            $query->where('active', true);
        }

        return $this->doQuery($query, false, false);
    }

    /**
     * @return SportsService
     */
    public function actives(): SportsService
    {
        $this->actives = true;
        return $this;
    }
}
