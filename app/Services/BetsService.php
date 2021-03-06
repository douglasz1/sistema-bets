<?php

namespace Bets\Services;


use Bets\Jobs\CalculateBetStatus;
use Bets\Models\Bet;
use Bets\Models\Match;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class BetsService
{
    /**
     * @var Bet
     */
    private $bet;
    /**
     * @var CompaniesService
     */
    private $companiesService;
    /**
     * @var TipsService
     */
    private $tipsService;
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(
        Bet $bet,
        CompaniesService $companiesService,
        TipsService $tipsService,
        UsersService $usersService
    ) {
        $this->bet = $bet;
        $this->companiesService = $companiesService;
        $this->tipsService = $tipsService;
        $this->usersService = $usersService;
    }

    /**
     * Creating a new Bet
     *
     * @param array $data
     * @return Bet
     * @throws Throwable
     */
    public function createWithoutSeller(array $data)
    {
        DB::beginTransaction();

        try {
            $data['seller_id'] = null;

            $quotations = $data['quotations'];
            unset($data['quotations']);

            $data['tips_quantity'] = count($quotations);

            /*
             * Creating a bet on the database
             * Returned the saved bet
             */
            $bet = $this->bet->create($data);

            /*
             * Saving many tips in the database
             * Returns the total quotation for all betting tips
             */
            $company = $this->companiesService->first();
            $quotationModifier = $company->quotation_modifier / 100;
            $bet->quotation_total = $this->tipsService->createMany($quotations, $bet, $quotationModifier);

            /*
             * Calculate betting commission
             */
            $bet->commission = 0;

            $bet->prize = $this->calculateBetPrize($bet, $company);

            $bet->save();

            DB::commit();

            return $bet;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function validate($data, $code)
    {
        DB::beginTransaction();

        try {
            $bet = $this->findByCode($code);

            $seller = auth()->user();

            $bet->seller_id = $seller->id;
            $bet->company_id = $seller->company_id;

            /*
             * Check if the current seller can make a new bet
             */
            $this->canBet($seller, $bet->tips_quantity, $bet->bet_value);

            if ($bet->tips_quantity === 1 && $seller->one_tip_quotation_min > 0) {
                if ($bet->tips[0]->value < $seller->one_tip_quotation_min) {
                    throw new Exception("A cotação para 1 palpite deve ser maior que {$seller->one_tip_quotation_min}");
                }
            } elseif ($bet->tips_quantity === 2 && $seller->two_tip_quotation_min > 0) {
                foreach ($bet->tips as $tip) {
                    if ($tip->value < $seller->two_tip_quotation_min) {
                        throw new Exception("A cotação para 2 palpites deve ser maior que {$seller->two_tip_quotation_min}");
                    }
                }
            } elseif ($bet->tips_quantity === 3 && $seller->three_tip_quotation_min > 0) {
                foreach ($bet->tips as $tip) {
                    if ($tip->value < $seller->three_tip_quotation_min) {
                        throw new Exception("A cotação para 3 palpites deve ser maior que {$seller->three_tip_quotation_min}");
                    }
                }
            }

            /*
             * Decreases bet value of the seller's balance
             */
            $this->usersService->decreaseBalance($seller->id, $bet->bet_value);


            $quotations = $data['quotations'];
            unset($data['quotations']);

            $bet->tips_quantity = count($quotations);

            /*
             * Returns the total quotation for all betting tips
             */
            $quotationModifier = $seller->quotationModifier() + ($seller->company->quotation_modifier / 100);

            \Bets\Models\Tip::where('bet_id', $bet->id)->delete();

            $bet->quotation_total = $this->tipsService->createMany($quotations, $bet, $quotationModifier);
            
            $bet->commission = $bet->bet_value * $seller->getCommission($bet->tips_quantity);

            $bet->client_name = $data['client_name'];
            
            $bet->client_id = $data['client_id'];
            
            $bet->bet_value = $data['bet_value'];

            $bet->prize = $this->calculateBetPrize($bet, $seller);

            $bet->setCreatedAt(Carbon::now()->toDateTimeString());

            $bet->save();

            DB::commit();

            return $bet;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function create(array $data, $isLiveBet = false)
    {
        DB::beginTransaction();

        try {
            $seller = auth()->user();

            $data['seller_id'] = $seller->id;
            $data['company_id'] = $seller->company_id;

            $quotations = $data['quotations'];
            unset($data['quotations']);

            $data['tips_quantity'] = count($quotations);

            /*
             * Check if the current seller can make a new bet
             */
            $this->canBet($seller, $data['tips_quantity'], $data['bet_value']);

            /**
             * Check by daily limit
             */
            if ($data['tips_quantity'] === 1 && $seller->one_tip_quotation_min > 0) {
                if ($quotations[0]['value'] < $seller->one_tip_quotation_min) {
                    throw new Exception("A cotação para 1 palpite deve ser maior que {$seller->one_tip_quotation_min}");
                }
            } elseif ($data['tips_quantity'] === 2 && $seller->two_tip_quotation_min > 0) {
                foreach ($quotations as $quotation) {
                    if ($quotation['value'] < $seller->two_tip_quotation_min) {
                        throw new Exception("A cotação para 2 palpites deve ser maior que {$seller->two_tip_quotation_min}");
                    }
                }
            } elseif ($data['tips_quantity'] === 3 && $seller->three_tip_quotation_min > 0) {
                foreach ($quotations as $quotation) {
                    if ($quotation['value'] < $seller->three_tip_quotation_min) {
                        throw new Exception("A cotação para 3 palpites deve ser maior que {$seller->three_tip_quotation_min}");
                    }
                }
            }

            /*
             * Creating a bet on the database
             * Returned the saved bet
             */
            $bet = $this->bet->newQuery()->create($data);

            /*
             * Decreases bet value of the seller's balance
             */
            $this->usersService->decreaseBalance($seller->id, $bet->bet_value);

            /*
             * Saving many tips in the database
             * Returns the total quotation for all betting tips
             */
            $quotationModifier = $seller->quotationModifier() + ($seller->company->quotation_modifier / 100);

            if ($isLiveBet) {
                $quotationModifier = $seller->quotationModifier() + (($seller->company->quotation_modifier - 5) / 100);
                $bet->quotation_total = $this->tipsService->createFromLiveBets($quotations, $bet, $quotationModifier);
                $bet->commission = $bet->bet_value * ($seller->comissao_ao_vivo / 100);
                $bet->origin = 'live';
            } else {
                $bet->quotation_total = $this->tipsService->createMany($quotations, $bet, $quotationModifier);
                $bet->commission = $bet->bet_value * $seller->getCommission($bet->tips_quantity);
            }

            if ($bet->tips()->count() === 0) {
                throw new Exception("Nenhum palpite encontrado no sistema, atualize e tente  novamente");
            }

            $bet->prize = $this->calculateBetPrize($bet, $seller);

            if (env('VERIFY_DUPLICATED', true)) {
                // verifica se exista aposta duplicada
                $data['prize'] = $bet->prize;
                if ($this->isDuplicated($seller, $data))
                    throw new \Exception("Ops, parece que sua aposta está duplicada!");
            }

            $bet->save();

            DB::commit();

            return $bet;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Prevent duplicated bet
     *
     * @param $seller
     * @param $betData
     * @return bool
     * @throws Exception
     */
    public function isDuplicated($seller, $betData)
    {
        $ultimaAposta = $this->bet->newQuery()
            ->where('seller_id', $seller->id)
            ->where('client_name', $betData['client_name'])
            ->where('bet_value', $betData['bet_value'])
            ->where('prize', $betData['prize'])
            ->where('tips_quantity', $betData['tips_quantity'])
            ->where('created_at', '>=', now()->subMinutes(2)->toDateTimeString())
            ->first();

        return ($ultimaAposta instanceof Bet);
    }

    /**
     * Check if the current seller can make a new bet
     *
     * @param $seller
     * @param int $quotationsQty
     * @param $betValue
     * @throws Exception
     */
    public function canBet($seller, int $quotationsQty, $betValue)
    {
        if ($seller->balance < $betValue) {
            throw new Exception("Saldo insuficiente! O saldo atual é de R$ " . moneyBR($seller->balance));
        }

        if ($quotationsQty < $seller->tips_min) {
            throw new Exception("A aposta deve conter, no mínimo, {$seller->tips_min} palpite(s)");
        }

        if ($quotationsQty > $seller->tips_max) {
            throw new Exception("A aposta deve conter, no máximo, {$seller->tips_max} palpite(s)");
        }

        if ($quotationsQty === 1) {
            if ($betValue < $seller->value_min1) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min1));
            } elseif ($betValue > $seller->value_max1) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max1));
            }

            $dailyBets = $this->bet
                ->where('seller_id', $seller->id)
                ->where('tips_quantity', 1)
                ->where('status', '<>', 'canceled')
                ->whereDate('created_at', Carbon::now()->toDateString())
                ->sum('bet_value');

            $dailyLimit = ($seller->daily_limit - $dailyBets);

            if ($betValue > $dailyLimit) {
                throw new Exception("Limite de apostas com 1 palpite excedido. Você ainda tem R$ {$dailyLimit} para apostas de 1 palpite.");
            }
        } elseif ($quotationsQty === 2) {
            if ($betValue < $seller->value_min2) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min2));
            } elseif ($betValue > $seller->value_max2) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max2));
            }
        } elseif ($quotationsQty >= 3 && $quotationsQty <= 5) {
            if ($betValue < $seller->value_min3) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min3));
            } elseif ($betValue > $seller->value_max3) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max3));
            }
        } elseif ($quotationsQty >= 6 && $quotationsQty <= 10) {
            if ($betValue < $seller->value_min6) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min6));
            } elseif ($betValue > $seller->value_max6) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max6));
            }
        } elseif ($quotationsQty >= 11 && $quotationsQty <= 15) {
            if ($betValue < $seller->value_min11) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min11));
            } elseif ($betValue > $seller->value_max11) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max11));
            }
        } else {
            if ($betValue < $seller->value_min16) {
                throw new Exception("Valor da aposta abaixo do permitido! O valor deve ser maior que R$ " . moneyBR($seller->value_min16));
            } elseif ($betValue > $seller->value_max16) {
                throw new Exception("Valor da aposta acima do permitido! O valor deve ser menor que R$ " . moneyBR($seller->value_max16));
            }
        }
    }

    public function calculateBetPrize($bet, $seller)
    {
        /*
         * Checks if the bet prize is higher than
         * the maximum allowable prize for the seller
         */
        $prize = $bet->bet_value * $bet->quotation_total;

        $betMaxValue = $bet->bet_value * $seller->max_prize_multiplier;

        if ($prize > $betMaxValue && $betMaxValue < $seller->max_prize) {
            $prize = $betMaxValue;
        }

        return ($prize > $seller->max_prize) ? $seller->max_prize : $prize;
    }

    /**
     * @param int $betId
     * @return \Bets\Models\Bet
     */
    public function cancel($betId)
    {
        $bet = $this->bet->find($betId);

        $bet->status = 'canceled';
        $bet->canceled_at = Carbon::now()->toDateTimeString();
        $bet->cancel_id = auth()->id();
        $bet->save();

        $this->usersService->increaseBalance($bet->seller_id, $bet->bet_value);

        return $bet;
    }

    public function remove($id)
    {
        return $this->bet->find($id)->delete();
    }

    /**
     * @param int $betId
     * @return \Bets\Models\Bet
     */
    public function findById($betId)
    {
        return $this->bet
            ->whereNotNull('seller_id')
            ->with([
                'tips',
                'tips.match',
                'tips.match.league',
                'tips.match.league.country',
                'tips.match.sport',
                'seller' => function ($query) {
                    return $query->select('name', 'id', 'company_id', 'username', 'percentual_premio');
                },
                'seller.company' => function ($query) {
                    return $query->select('print_name AS name', 'id');
                }
            ])
            ->find($betId);
    }

    /**
     * @param int $code
     * @return Bet|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findByCode($code)
    {
        return $this->bet
            ->whereNull('seller_id')
            ->with([
                'tips', 
                'tips.match',
                'tips.match.league',
                'tips.match.sport',
                'tips.match.league.country',
            ])
            ->findOrFail($code);
    }

    /**
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByFilters(array $filters)
    {
        return $this->bet
            ->when($filters['status'] !== 'all', function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '>=', $filters['start_date']);
            })
            ->when(!empty($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '<=', $filters['end_date']);
            })
            ->when(!empty($filters['sellers']), function ($query) use ($filters) {
                if (!is_array($filters['sellers']))
                    return $query->where('seller_id', $filters['sellers']);

                return $query->whereIn('seller_id', $filters['sellers']);
            })
            ->orderBy('created_at', 'DESC')
            ->when(isset($filters['withTips']) && $filters['withTips'], function ($query) {
                return $query->with(['tips.match']);
            })
            ->with(['seller' => function ($query) {
                return $query->select('name', 'id', 'profit_percentage', 'user_id')
                    ->with(['parentUser' => function ($query) {
                        return $query->select('name', 'id', 'user_id')
                            ->with(['parentUser' => function ($query) {
                                return $query->select('name', 'id');
                            }]);
                    }]);
            }])
            ->get();
    }

    public function getForReprint($sellerId)
    {
        $reprintTime = env('REPRINT_TIME', 0);

        if ($reprintTime > 0) {
            $nowDate = Carbon::now()->toDateTimeString();

            return $this->bet
                ->where('status', 'pending')
                ->where('seller_id', $sellerId)
                ->whereRaw(DB::raw("'{$nowDate}' <= DATE_ADD(`created_at`, INTERVAL {$reprintTime} MINUTE)"))
                ->orderBy('created_at', 'DESC')
                ->paginate();
        } else {
            return $this->bet
                ->where('seller_id', $sellerId)
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }
    }

    public function update(array $data, $id)
    {
        if (isset($data['status']) && $data['status'] === 'canceled') {
            return $this->cancel($id);
        }

        $bet = $this->findById($id);

        if (isset($data['created_at'])) {
            $bet->setCreatedAt($data['created_at']);
        }

        $bet->update($data);

        return $bet;
    }

    public function calculateQuotationTotal($bet)
    {
        $collection = $bet->tips->where('status', '<>', 'canceled')->pluck('value');

        $quotationTotal = $collection->reduce(function ($carry, $item) {
            return $carry * (float) $item;
        }, 1);

        return $quotationTotal;
    }

    public function recalculateBet($id)
    {
        $bet = $this->bet->findOrFail($id);

        $quotationTotal = 1;

        foreach ($bet->tips->where('status', '<>', 'canceled') as $tip) {
            if ((float) $tip->value < 1.01) {
                $tip->value = 1.01;
            } elseif ((float) $tip->value > 100) {
                $tip->value = 100;
            }
            $quotationTotal = $quotationTotal * $tip->value;
        }

        $bet->quotation_total = $quotationTotal;

        $bet->prize = $this->calculateBetPrize($bet, $bet->seller);

        $bet->save();

        dispatch((new CalculateBetStatus($bet))->onQueue('betStatus'));

        return $bet;
    }
}
