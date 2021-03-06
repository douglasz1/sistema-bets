<?php

namespace Bets\Services;


use Bets\Models\Quotation;

class QuotationsService
{
    /**
     * @var Quotation
     */
    private $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * @param int $matchId
     * @param bool $includeMatchResult
     * @return array
     */
    public function quotationByMatch($matchId, $includeMatchResult = false)
    {
        $quotations = $this->quotation
            ->with(['match', 'match.league', 'match.league.country', 'match.sport'])
            ->where("match_id", $matchId)
            ->when(!$includeMatchResult, function ($query) {
                return $query->where('bet_slug', '<>', 'full_time_result')
                    ->where('bet_slug', '<>', 'to_win_match');
            })
            ->orderByRaw('sign(`bet_order`) desc')
            ->orderBy('bet_order', 'ASC')->get();

        return $quotations;
    }

    /**
     * Find the quotation choice by ID
     *
     * @param int $id
     * @return \Bets\Models\Quotation
     */
    public function findById($id)
    {
        return $this->quotation->with('match')->find($id);
    }

    public function whereIn(array $data)
    {
        return $this->quotation
            ->whereIn('id', $data)
            ->with('match')->get();
    }

    public function update(array $data, $id)
    {
        $quotation = $this->quotation->find($id);

        if ($quotation->value == $data['value']) {
            unset($data['value']);
        } else {
            $data['upgradable'] = false;
        }

        $quotation->fill($data);

        $quotation->save();

        return $quotation;
    }

    public function create(array $data)
    {
        $quotation = $this->quotation->firstOrCreate($data);

        return $quotation;
    }

    public function firstOrCreate(array $data)
    {
        try {
            $quotation = $this->quotation
                ->where('match_id', $data['match_id'])
                ->where('bet_slug', $data['bet_slug'])
                ->where('choice_slug', $data['choice_slug'])
                ->firstOrFail();

            $quotation->fill($data);
            $quotation->save();

            return $quotation;
        } catch (\Throwable $exception) {
            return $this->quotation->create($data);
        }
    }

    public function searchChoices(array $choices)
    {
        $query = $this->quotation->newQuery()->with('match');

        foreach ($choices as $choice) {
            $query->orWhere(function ($query) use ($choice) {
                $query->where([
                    'match_id' => $choice['match_id'],
                    'bet_slug' => $choice['bet_slug'],
                    'choice_slug' => $choice['choice_slug'],
                ]);
            });
        }

        return $query->get();
    }
}
