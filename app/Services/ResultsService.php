<?php

namespace Bets\Services;


use Bets\Jobs\CalculateTipStatus;
use Bets\Jobs\CancelTips;
use Bets\Models\Result;
use Bets\Models\Tip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResultsService
{
    /**
     * @var Result
     */
    private $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * @param array $data
     * @return \Bets\Models\Result
     */
    public function create(array $data)
    {
        $result = $this->result->where('match_id', $data['match_id'])->first();

        if (!$result instanceof Result) {
            return $this->result->create($data);
        }

        return $result;
    }

    /**
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByFilters(array $filters)
    {
        return $this->result
            ->when($filters['status'] !== 'all', function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '>=', $filters['start_date']);
            })
            ->when(!empty($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '<=', $filters['end_date']);
            })
            ->where('have_tips', true)
            ->orderBy('match_date', 'ASC')
            ->with(['league', 'league.country', 'sport'])
            ->get();
    }

    public function findById($id)
    {
        return $this->result->with('league')->find($id);
    }

    public function update(array $data, $id)
    {
        if (isset($data['_token']))
            unset($data['_token']);

        $result = $this->result->find($id);

        if (!$result instanceof Result) {
            return $this->create($data);
        }

        // Update data from array
        $result->update($data);

        // Dispatch the jobs to calculate the tip status
        if (isset($data['status']) && $data['status'] === 'finished') {
            $tips = Tip::query()
                ->where('match_id', $result->match_id)
                ->where('status', '!=', 'canceled')
                ->get();

            foreach ($tips as $tip) {
                dispatch((new CalculateTipStatus($tip, $result))->onQueue('betStatus'));
            }
        } elseif (isset($data['status']) && $data['status'] === 'canceled') {
            $tips = Tip::query()
                ->where('match_id', $result->match_id)
                ->get();

            foreach ($tips as $tip) {
                dispatch((new CancelTips($tip))->onQueue('betStatus'));
            }
        }

        return $result;
    }

    public function getPastResults()
    {
        $resultados = \Bets\Models\Tip::query()
            ->whereDate('created_at', '>=', today()->subDays(5))
            ->groupBy(['match_id'])
            ->where('status', 'pending')
            ->pluck('match_id');

        return Result::query()
            ->whereIn('match_id', $resultados)
            ->get();
    }

    public function updateOrCreate(array $attributes)
    {
        try {
            $match = $this->result
                ->where('match_id', $attributes['match_id'])
                ->firstOrFail();

            $match->fill($attributes);
            $match->save();

            if (isset($attributes['status']) && $attributes['status'] === 'canceled') {
                $tips = Tip::query()
                    ->where('match_id', $match->match_id)
                    ->get();

                foreach ($tips as $tip) {
                    dispatch((new CancelTips($tip))->onQueue('betStatus'));
                }
            }

            return $match;

        } catch (ModelNotFoundException $exception) {
            return $this->create($attributes);
        }
    }

}
