<?php


namespace Bets\Services\Supervisor;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnaliticoService
{

    private $finalDate;
    private $startDate;
    private $companies;
    private $status;

    public function all()
    {
        $now = Carbon::now()->toDateString();
        $startDate = $this->startDate ?: $now;
        $finalDate = $this->finalDate ?: $now;
        $betStatus = $this->status ?: 'all';

        return DB::table('tips')
            ->selectRaw('count(`tips`.id) as `tips_count`, `results`.id, `results`.match_id, `results`.home_team, `results`.away_team, `results`.match_date, `bets`.company_id')
            ->leftJoin('results', 'tips.match_id', 'results.match_id')
            ->leftJoin('bets', 'tips.bet_id', 'bets.id')
            ->whereDate('tips.match_date', '>=', $startDate)
            ->whereDate('tips.match_date', '<=', $finalDate)
            ->whereIn('bets.company_id', $this->companies)
            ->when($betStatus !== 'all', function ($query) use ($betStatus) {
                $query->where('bets.status', $betStatus);
                $query->where('tips.status', $betStatus);
            })
            ->groupBy('tips.match_id')
            ->having('tips_count', '>=', 5)
            ->get();
    }

    public function findTips($matchId)
    {
        $betStatus = $this->status ?: 'all';

        return DB::table('tips')
            ->selectRaw('`tips`.choice_name, count(`tips`.id) as `quantity`, sum(`bets`.bet_value) as `total_value`, sum(`bets`.prize) as `total_prize`')
            ->leftJoin('bets', 'bets.id', 'tips.bet_id')
            ->where('tips.match_id', $matchId)
            ->when($betStatus !== 'all', function ($query) use ($betStatus) {
                $query->where('bets.status', $betStatus);
                $query->where('tips.status', $betStatus);
            })
            ->when($betStatus === 'all', function ($query) {
                $query->where('bets.status', '!=', 'canceled');
                $query->where('tips.status', '!=', 'canceled');
            })
            ->whereIn('bets.company_id', $this->companies)
            ->groupBy('tips.choice_slug')
            ->get();
    }

    public function find($id)
    {
        return app(\Bets\Models\Result::class)->findOrFail($id);
    }

    /**
     * @param mixed $finalDate
     * @return AnaliticoService
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;
        return $this;
    }

    /**
     * @param mixed $startDate
     * @return AnaliticoService
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @param mixed $companies
     * @return AnaliticoService
     */
    public function setCompanies($companies)
    {
        $this->companies = is_array($companies) ? $companies : [$companies];
        return $this;
    }

    /**
     * @param mixed $status
     * @return AnaliticoService
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
